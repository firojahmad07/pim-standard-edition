<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2021 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\TableAttribute\Infrastructure\Validation\Attribute;

use Symfony\Component\Validator\Constraint;

final class FirstColumnShouldHaveSelectDataType extends Constraint
{
    public string $message = 'pim_table_configuration.validation.table_configuration.first_column_should_be_select';

    public function getTargets(): array
    {
        return [self::PROPERTY_CONSTRAINT];
    }
}
