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

namespace Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\Enabled;

use Akeneo\Pim\Tailored\Application\Common\Selection\Enabled\EnabledSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\EnabledValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;

class EnabledSelectionApplier implements SelectionApplierInterface
{
    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof EnabledSelection
            || !$value instanceof EnabledValue
        ) {
            throw new \InvalidArgumentException('Cannot apply Enabled selection on this entity');
        }

        return $value->isEnabled() ? '1' : '0';
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof EnabledSelection
            && $value instanceof EnabledValue;
    }
}
