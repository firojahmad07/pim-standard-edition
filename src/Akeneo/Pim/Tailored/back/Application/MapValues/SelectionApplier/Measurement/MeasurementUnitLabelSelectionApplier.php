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

use Akeneo\Pim\Tailored\Application\Common\Selection\Measurement\MeasurementUnitLabelSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\MeasurementValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Domain\Query\FindUnitLabelInterface;

class MeasurementUnitLabelSelectionApplier implements MeasurementApplierInterface
{
    private FindUnitLabelInterface $findUnitLabels;

    public function __construct(FindUnitLabelInterface $findUnitLabels)
    {
        $this->findUnitLabels = $findUnitLabels;
    }

    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof MeasurementUnitLabelSelection
            || !$value instanceof MeasurementValue
        ) {
            throw new \InvalidArgumentException('Cannot apply Measurement unit label selection on this entity');
        }

        $unitCode = $value->getUnitCode();

        $unitTranslation = $this->findUnitLabels->byFamilyCodeAndUnitCode(
            $selection->getMeasurementFamilyCode(),
            $unitCode,
            $selection->getLocale()
        );

        return $unitTranslation ?? sprintf('[%s]', $unitCode);
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof MeasurementUnitLabelSelection
            && $value instanceof MeasurementValue;
    }
}
