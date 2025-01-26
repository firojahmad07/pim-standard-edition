<?php

namespace Akeneo\Pim\TableAttribute\Infrastructure\Validation\Attribute;

use Symfony\Component\Validator\Constraint;

final class IsMaximumTableAttributesReached extends Constraint
{
    public const LIMIT = 50;
    public string $message = 'pim_table_configuration.validation.table_configuration.too_many_table_attributes';

    public function getTargets(): array
    {
        return [self::CLASS_CONSTRAINT];
    }
}
