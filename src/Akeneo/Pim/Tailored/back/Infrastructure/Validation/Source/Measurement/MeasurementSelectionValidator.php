<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2021 Akeneo SAS (https://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\Tailored\Infrastructure\Validation\Source\Measurement;

use Akeneo\Pim\Tailored\Application\Common\Selection\Measurement\MeasurementUnitCodeSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\Measurement\MeasurementUnitLabelSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\Measurement\MeasurementUnitSymbolSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\Measurement\MeasurementValueAndUnitLabelSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\Measurement\MeasurementValueAndUnitSymbolSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\Measurement\MeasurementValueSelection;
use Akeneo\Pim\Tailored\Infrastructure\Validation\LocaleShouldBeActive;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintValidator;

class MeasurementSelectionValidator extends ConstraintValidator
{
    /** @var string[] */
    private array $availableDecimalSeparator;

    public function __construct(array $availableDecimalSeparator)
    {
        $this->availableDecimalSeparator = $availableDecimalSeparator;
    }

    public function validate($selection, Constraint $constraint): void
    {
        $validator = $this->context->getValidator();
        $violations = $validator->validate($selection, new Collection(
            [
                'fields' => [
                    'type' => new Choice(
                        [
                            'choices' => [
                                MeasurementUnitCodeSelection::TYPE,
                                MeasurementUnitSymbolSelection::TYPE,
                                MeasurementUnitLabelSelection::TYPE,
                                MeasurementValueSelection::TYPE,
                                MeasurementValueAndUnitLabelSelection::TYPE,
                                MeasurementValueAndUnitSymbolSelection::TYPE,
                            ],
                        ]
                    ),
                    'locale' => new Optional([new Type('string')]),
                    'decimal_separator' => new Optional(new Choice(
                        [
                            'choices' => $this->availableDecimalSeparator,
                        ]
                    )),
                ],
            ]
        ));

        if (0 < $violations->count()) {
            foreach ($violations as $violation) {
                $this->context->buildViolation(
                    $violation->getMessage(),
                    $violation->getParameters()
                )
                    ->atPath($violation->getPropertyPath())
                    ->addViolation();
            }

            return;
        }

        if (MeasurementUnitLabelSelection::TYPE === $selection['type']
            || MeasurementValueAndUnitLabelSelection::TYPE === $selection['type']
        ) {
            $violations = $validator->validate($selection['locale'], [
                new NotBlank(),
                new LocaleShouldBeActive()
            ]);

            foreach ($violations as $violation) {
                $this->context->buildViolation(
                    $violation->getMessage(),
                    $violation->getParameters()
                )
                    ->atPath('[locale]')
                    ->addViolation();
            }
        }
    }
}
