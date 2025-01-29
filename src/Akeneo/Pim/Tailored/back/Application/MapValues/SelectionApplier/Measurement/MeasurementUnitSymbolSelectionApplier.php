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

namespace Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\Measurement;

use Akeneo\Pim\Tailored\Application\Common\Selection\Measurement\MeasurementUnitSymbolSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\MeasurementValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;
use Akeneo\Pim\Tailored\Domain\Query\FindUnitSymbolInterface;

class MeasurementUnitSymbolSelectionApplier implements SelectionApplierInterface
{
    public function __construct(
        private FindUnitSymbolInterface $findUnitSymbol
    ) {
    }

    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof MeasurementUnitSymbolSelection
            || !$value instanceof MeasurementValue
        ) {
            throw new \InvalidArgumentException('Cannot apply Measurement unit symbol selection on this entity');
        }

        $unitSymbol = $this->findUnitSymbol->byFamilyCodeAndUnitCode(
            $selection->getMeasurementFamilyCode(),
            $value->getUnitCode(),
        );

        return $unitSymbol;
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof MeasurementUnitSymbolSelection
            && $value instanceof MeasurementValue;
    }
}
