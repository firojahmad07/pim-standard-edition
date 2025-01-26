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

namespace Akeneo\Platform\TailoredExport\Infrastructure\Validation\Source\ReferenceEntityCollection;

use Akeneo\Platform\TailoredExport\Infrastructure\Validation\Operation\DefaultValueOperationConstraint;
use Akeneo\Platform\TailoredExport\Infrastructure\Validation\Operation\ReplacementOperationConstraint;
use Akeneo\Platform\TailoredExport\Infrastructure\Validation\Selection\CodeLabelCollectionSelectionConstraint;
use Akeneo\Platform\TailoredExport\Infrastructure\Validation\Source\SourceConstraintProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Optional;
use Symfony\Component\Validator\ConstraintValidator;

class ReferenceEntityCollectionSourceValidator extends ConstraintValidator
{
    public function validate($source, Constraint $constraint): void
    {
        $validator = $this->context->getValidator();
        $sourceConstraintFields = SourceConstraintProvider::getConstraintCollection()->fields;
        $sourceConstraintFields['selection'] = new CodeLabelCollectionSelectionConstraint();
        $sourceConstraintFields['operations'] = new Collection(['fields' => [
            'default_value' => new Optional(new DefaultValueOperationConstraint()),
            'replacement' => new Optional(new ReplacementOperationConstraint()),
        ]]);

        $violations = $validator->validate($source, new Collection(['fields' => $sourceConstraintFields]));

        foreach ($violations as $violation) {
            $this->context->buildViolation(
                $violation->getMessage(),
                $violation->getParameters()
            )
                ->atPath($violation->getPropertyPath())
                ->addViolation();
        }
    }
}
