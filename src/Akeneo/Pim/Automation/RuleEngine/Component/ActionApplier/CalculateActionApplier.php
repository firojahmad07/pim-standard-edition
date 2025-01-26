<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2020 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\Automation\RuleEngine\Component\ActionApplier;

use Akeneo\Pim\Automation\RuleEngine\Component\ActionApplier\Calculate\GetOperandValue;
use Akeneo\Pim\Automation\RuleEngine\Component\Event\SkippedActionForSubjectEvent;
use Akeneo\Pim\Automation\RuleEngine\Component\Exception\NonApplicableActionException;
use Akeneo\Pim\Automation\RuleEngine\Component\Model\Operand;
use Akeneo\Pim\Automation\RuleEngine\Component\Model\Operation;
use Akeneo\Pim\Automation\RuleEngine\Component\Model\ProductCalculateActionInterface;
use Akeneo\Pim\Automation\RuleEngine\Component\Model\ProductTarget;
use Akeneo\Pim\Enrichment\Component\Product\Model\EntityWithFamilyVariantInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\EntityWithValuesInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Structure\Component\AttributeTypes;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\Attribute;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\GetAttributes;
use Akeneo\Tool\Bundle\RuleEngineBundle\Model\ActionInterface;
use Akeneo\Tool\Component\RuleEngine\ActionApplier\ActionApplierInterface;
use Akeneo\Tool\Component\StorageUtils\Updater\PropertySetterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

class CalculateActionApplier implements ActionApplierInterface
{
    /** @var GetAttributes */
    private $getAttributes;

    /** @var GetOperandValue */
    private $getOperandValue;

    /** @var NormalizerInterface */
    private $priceNormalizer;

    /** @var PropertySetterInterface */
    private $propertySetter;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        GetAttributes $getAttributes,
        GetOperandValue $getOperandValue,
        NormalizerInterface $priceNormalizer,
        PropertySetterInterface $propertySetter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->getAttributes = $getAttributes;
        $this->getOperandValue = $getOperandValue;
        $this->priceNormalizer = $priceNormalizer;
        $this->propertySetter = $propertySetter;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function applyAction(ActionInterface $action, array $items = []): array
    {
        Assert::isInstanceOf($action, ProductCalculateActionInterface::class);
        foreach ($items as $index => $entityWithValues) {
            try {
                $this->actionCanBeAppliedToEntity($entityWithValues, $action);
                $result = $this->calculateDataForEntity($entityWithValues, $action);
                $data = $this->getStandardData($entityWithValues, $action->getDestination(), $result);
                $this->propertySetter->setData(
                    $entityWithValues,
                    $action->getDestination()->getField(),
                    $data,
                    [
                        'scope' => $action->getDestination()->getScope(),
                        'locale' => $action->getDestination()->getLocale(),
                    ]
                );
            } catch (NonApplicableActionException $e) {
                unset($items[$index]);
                $this->eventDispatcher->dispatch(
                    new SkippedActionForSubjectEvent($action, $entityWithValues, $e->getMessage())
                );
            }
        }

        return $items;
    }

    public function supports(ActionInterface $action)
    {
        return $action instanceof ProductCalculateActionInterface;
    }

    /**
     * We do not apply the action if:
     *  - destination attribute does not belong to the family
     *  - entity is variant (variant product or product model) and destination attribute is not on the entity's variation level
     */
    private function actionCanBeAppliedToEntity(
        EntityWithFamilyVariantInterface $entity,
        ProductCalculateActionInterface $action
    ): void {
        $destination = $this->getAttributes->forCode($action->getDestination()->getField());
        Assert::isInstanceOf(
            $destination,
            Attribute::class,
            \sprintf('The "%s" attribute does not exist', $action->getDestination()->getField())
        );

        $family = $entity->getFamily();
        if (null === $family) {
            return;
        }
        if (!$family->hasAttributeCode($destination->code())) {
            throw new NonApplicableActionException(
                \sprintf(
                    'The "%s" attribute does not belong to the family of this %s',
                    $destination->code(),
                    $entity instanceof ProductModelInterface ? 'product model' : 'product'
                )
            );
        }

        $familyVariant = $entity->getFamilyVariant();
        if (null !== $familyVariant && $familyVariant->getLevelForAttributeCode($destination->code()) !== $entity->getVariationLevel()) {
            throw new NonApplicableActionException(
                \sprintf(
                    'The "%s" property cannot be updated for this %s, as it is not at the same variation level',
                    $destination->code(),
                    $entity instanceof ProductModelInterface ? 'product model' : 'product'
                )
            );
        }
    }

    private function calculateDataForEntity(EntityWithValuesInterface $entity, ProductCalculateActionInterface $action): float
    {
        $value = $this->getOperandValue($entity, $action->getSource());

        foreach ($action->getOperationList() as $operation) {
            $value = $this->applyOperation(
                $operation->getOperator(),
                $value,
                $this->getOperandValue($entity, $operation->getOperand())
            );
        }

        if ($action->isRoundEnabled()) {
            $value = round($value, $action->getRoundPrecision());
        }

        return $value;
    }

    private function applyOperation(string $operator, float $firstOperand, float $secondOperand): float
    {
        if (Operation::DIVIDE === $operator && 0.0 === $secondOperand) {
            throw new NonApplicableActionException('Cannot apply operation: division by zero');
        }

        switch ($operator) {
            case Operation::MULTIPLY:
                return $firstOperand * $secondOperand;
            case Operation::DIVIDE:
                return $firstOperand / $secondOperand;
            case Operation::ADD:
                return $firstOperand + $secondOperand;
            case Operation::SUBTRACT:
                return $firstOperand - $secondOperand;
            default:
                throw new \LogicException('Operator not supported');
        }
    }

    private function getOperandValue(EntityWithValuesInterface $entity, Operand $operand): float
    {
        if (null !== $operand->getConstantValue()) {
            return $operand->getConstantValue();
        }

        $data = $this->getOperandValue->fromEntity($entity, $operand);
        if (null !== $data) {
            return $data;
        }

        throw new NonApplicableActionException(
            sprintf(
                'The entity has no value for %s-%s-%s%s',
                $operand->getAttributeCode(),
                $operand->getChannelCode() ?: '<all_channels>',
                $operand->getLocaleCode() ?: '<all_locales>',
                $operand->getCurrencyCode() ? sprintf(' (%s)', $operand->getCurrencyCode()) : ''
            )
        );
    }

    private function getStandardData(EntityWithValuesInterface $entity, ProductTarget $destination, float $data)
    {
        $attribute = $this->getAttributes->forCode($destination->getField());
        $formattedData = null;

        switch ($attribute->type()) {
            case AttributeTypes::NUMBER:
                $formattedData = $data;
                break;
            case AttributeTypes::METRIC:
                $unit = $destination->getUnit() ?? $attribute->defaultMetricUnit();
                $formattedData = [
                    'amount' => $data,
                    'unit' => $unit,
                ];
                break;
            case AttributeTypes::PRICE_COLLECTION:
                $formattedData = $this->getPriceCollectionData($entity, $destination, $data);
                break;
            default:
                throw new \InvalidArgumentException('Unsupported destination type');
        }

        return $formattedData;
    }

    /**
     * Gets the new price collection value in standard format
     * Replaces prices with same currency from the former value
     */
    private function getPriceCollectionData(
        EntityWithValuesInterface $entity,
        ProductTarget $destination,
        float $amount
    ): array {
        Assert::string($destination->getCurrency());
        $standardizedPrices = [
            [
                'amount' => $amount,
                'currency' => $destination->getCurrency(),
            ]
        ];

        $previousValue = $entity->getValue(
            $destination->getField(),
            $destination->getLocale(),
            $destination->getScope()
        );
        if (null === $previousValue) {
            return $standardizedPrices;
        }

        foreach ($previousValue->getData() as $previousPrice) {
            if ($previousPrice->getCurrency() !== $destination->getCurrency()) {
                $standardizedPrices[] = $this->priceNormalizer->normalize($previousPrice, 'standard');
            }
        }

        return $standardizedPrices;
    }
}
