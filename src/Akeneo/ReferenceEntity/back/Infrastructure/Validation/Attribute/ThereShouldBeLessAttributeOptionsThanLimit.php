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

use Symfony\Component\Validator\Constraint;

class ThereShouldBeLessAttributeOptionsThanLimit extends Constraint
{
    public const MESSAGE_TOO_MANY_OPTIONS = 'pim_reference_entity.attribute.validation.options.too_many';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return 'akeneo_referenceentity.validator.attribute.there_should_be_less_attribute_options_than_limit';
    }
}
