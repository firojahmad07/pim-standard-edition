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

namespace Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\MultiSelect;

use Akeneo\Pim\Tailored\Application\Common\Selection\MultiSelect\MultiSelectCodeSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\MultiSelectValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;

class MultiSelectCodeSelectionApplier implements SelectionApplierInterface
{
    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof MultiSelectCodeSelection
            || !$value instanceof MultiSelectValue
        ) {
            throw new \InvalidArgumentException('Cannot apply Multi Select selection on this entity');
        }

        $optionsCodes = $value->getOptionCodes();

        $selectedData = array_map(static function ($optionCode) use ($value) {
            if ($value->hasMappedValue($optionCode)) {
                return $value->getMappedValue($optionCode);
            }

            return $optionCode;
        }, $optionsCodes);

        return implode($selection->getSeparator(), $selectedData);
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof MultiSelectCodeSelection
            && $value instanceof MultiSelectValue;
    }
}
