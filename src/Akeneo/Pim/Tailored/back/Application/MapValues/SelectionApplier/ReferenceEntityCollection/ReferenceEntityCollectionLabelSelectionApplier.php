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

namespace Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\ReferenceEntityCollection;

use Akeneo\Pim\Tailored\Application\Common\Selection\ReferenceEntityCollection\ReferenceEntityCollectionLabelSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\ReferenceEntityCollectionValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;
use Akeneo\Pim\Tailored\Domain\Query\FindRecordLabelsInterface;

class ReferenceEntityCollectionLabelSelectionApplier implements SelectionApplierInterface
{
    private FindRecordLabelsInterface $findRecordLabels;

    public function __construct(FindRecordLabelsInterface $findRecordLabels)
    {
        $this->findRecordLabels = $findRecordLabels;
    }

    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$value instanceof ReferenceEntityCollectionValue
            || !$selection instanceof ReferenceEntityCollectionLabelSelection
        ) {
            throw new \InvalidArgumentException('Cannot apply Reference Entity Collection selection on this entity');
        }

        $recordCodes = $value->getRecordCodes();
        $referenceEntityCode = $selection->getReferenceEntityCode();
        $recordTranslations = $this->findRecordLabels->byReferenceEntityCodeAndRecordCodes(
            $referenceEntityCode,
            $recordCodes,
            $selection->getLocale()
        );

        $selectedData = array_map(static function ($recordCode) use ($value, $recordTranslations) {
            if ($value->hasMappedValue($recordCode)) {
                return $value->getMappedValue($recordCode);
            }

            return $recordTranslations[$recordCode] ?? sprintf('[%s]', $recordCode);
        }, $recordCodes);

        return implode($selection->getSeparator(), $selectedData);
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof ReferenceEntityCollectionLabelSelection
            && $value instanceof ReferenceEntityCollectionValue;
    }
}
