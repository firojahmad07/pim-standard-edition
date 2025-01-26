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

use Akeneo\Pim\Automation\RuleEngine\Component\Event\SkippedActionForSubjectEvent;
use Akeneo\Pim\Automation\RuleEngine\Component\Exception\NonApplicableActionException;
use Akeneo\Pim\Automation\RuleEngine\Component\Model\ProductClearActionInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\EntityWithFamilyVariantInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\EntityWithValuesInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Structure\Component\Query\PublicApi\AttributeType\GetAttributes;
use Akeneo\Tool\Bundle\RuleEngineBundle\Model\ActionInterface;
use Akeneo\Tool\Component\RuleEngine\ActionApplier\ActionApplierInterface;
use Akeneo\Tool\Component\StorageUtils\Updater\PropertyClearerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Webmozart\Assert\Assert;

/**
 * @author    Nicolas Marniesse <nicolas.marniesse@akeneo.com>
 * @copyright 2020 Akeneo SAS (http://www.akeneo.com)
 */
final class ClearerActionApplier implements ActionApplierInterface
{
    /** @var PropertyClearerInterface */
    protected $propertyClearer;

    /** @var GetAttributes */
    private $getAttributes;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(
        PropertyClearerInterface $propertyClearer,
        GetAttributes $getAttributes,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->propertyClearer = $propertyClearer;
        $this->getAttributes = $getAttributes;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ActionInterface $action): bool
    {
        return $action instanceof ProductClearActionInterface;
    }

    /**
     * {@inheritDoc}
     */
    public function applyAction(ActionInterface $action, array $entitiesWithValues = []): array
    {
        Assert::isInstanceOf($action, ProductClearActionInterface::class);

        foreach ($entitiesWithValues as $index => $entityWithValues) {
            try {
                $this->actionCanBeAppliedToEntity($entityWithValues, $action);
                $this->clearDataOnEntityWithValues($entityWithValues, $action);
            } catch (NonApplicableActionException $e) {
                unset($entitiesWithValues[$index]);
                $this->eventDispatcher->dispatch(
                    new SkippedActionForSubjectEvent($action, $entityWithValues, $e->getMessage())
                );
            }
        }

        return $entitiesWithValues;
    }

    /**
     * We do not apply the action if field is an attribute and:
     *  - field is "groups" and entity is a product model
     *  - entity is variant (variant product or product model) and attribute is not on the entity's variation level
     */
    private function actionCanBeAppliedToEntity(
        EntityWithFamilyVariantInterface $entity,
        ProductClearActionInterface $action
    ): void {
        $field = $action->getField();

        if ('groups' === $field && $entity instanceof ProductModelInterface) {
            throw new NonApplicableActionException(
                'The "groups" property cannot be cleared from a product model'
            );
        }

        $attribute = $this->getAttributes->forCode($field);
        if (null === $attribute) {
            return;
        }

        $family = $entity->getFamily();
        if (null === $family || !$family->hasAttributeCode($attribute->code())) {
            return;
        }

        $familyVariant = $entity->getFamilyVariant();
        if (null !== $familyVariant &&
            $familyVariant->getLevelForAttributeCode($attribute->code()) !== $entity->getVariationLevel()) {
            throw new NonApplicableActionException(
                \sprintf(
                    'The "%s" property cannot be updated for this %s, as it is not at the same variation level',
                    $attribute->code(),
                    $entity instanceof ProductModelInterface ? 'product model' : 'product'
                )
            );
        }
    }

    private function clearDataOnEntityWithValues(
        EntityWithValuesInterface $entityWithValues,
        ProductClearActionInterface $action
    ): void {
        $this->propertyClearer->clear(
            $entityWithValues,
            $action->getField(),
            ['locale' => $action->getLocale(), 'scope'  => $action->getScope()]
        );
    }
}
