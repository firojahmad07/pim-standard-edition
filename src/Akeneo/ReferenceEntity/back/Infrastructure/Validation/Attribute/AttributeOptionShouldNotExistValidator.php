<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2018 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\ReferenceEntity\Infrastructure\Validation\Attribute;

use Akeneo\ReferenceEntity\Application\Attribute\AppendAttributeOption\AppendAttributeOptionCommand;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeCode;
use Akeneo\ReferenceEntity\Domain\Model\Attribute\AttributeOption\OptionCode;
use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntityIdentifier;
use Akeneo\ReferenceEntity\Domain\Query\Attribute\GetAttributeIdentifierInterface;
use Akeneo\ReferenceEntity\Domain\Repository\AttributeRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class AttributeOptionShouldNotExistValidator extends ConstraintValidator
{
    public function __construct(
        private GetAttributeIdentifierInterface $getAttributeIdentifier,
        private AttributeRepositoryInterface $attributeRepository
    ) {
    }

    public function validate($command, Constraint $constraint): void
    {
        $this->checkConstraintType($constraint);
        $this->checkCommandType($command);
        $this->validateCommand($command);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function checkCommandType($command): void
    {
        if (!$command instanceof AppendAttributeOptionCommand) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected argument to be of class "%s", "%s" given',
                    AppendAttributeOptionCommand::class,
                    $command::class
                )
            );
        }
    }

    /**
     * @throws UnexpectedTypeException
     */
    private function checkConstraintType(Constraint $constraint): void
    {
        if (!$constraint instanceof AttributeOptionShouldNotExist) {
            throw new UnexpectedTypeException($constraint, self::class);
        }
    }

    private function validateCommand(AppendAttributeOptionCommand $command): void
    {
        $identifier = $this->getAttributeIdentifier->withReferenceEntityAndCode(
            ReferenceEntityIdentifier::fromString($command->referenceEntityIdentifier),
            AttributeCode::fromString($command->attributeCode)
        );
        $attribute = $this->attributeRepository->getByIdentifier($identifier);

        $optionCode = OptionCode::fromString($command->optionCode);
        if ($attribute->hasAttributeOption($optionCode)) {
            $this->context->buildViolation(AttributeOptionShouldNotExist::ERROR_MESSAGE)
                ->setParameter('%option_code%', $command->optionCode)
                ->atPath('code')
                ->addViolation();
        }
    }
}
