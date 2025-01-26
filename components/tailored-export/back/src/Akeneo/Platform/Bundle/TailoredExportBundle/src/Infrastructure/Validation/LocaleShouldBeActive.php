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

namespace Akeneo\Platform\TailoredExport\Infrastructure\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * @copyright 2021 Akeneo SAS (https://www.akeneo.com)
 */
final class LocaleShouldBeActive extends Constraint
{
    public const NOT_ACTIVE_MESSAGE = 'akeneo.tailored_export.validation.locale.should_be_active';

    /**
     * {@inheritdoc}
     */
    public function validatedBy(): string
    {
        return 'akeneo.tailored_export.validation.locale_should_be_active';
    }
}
