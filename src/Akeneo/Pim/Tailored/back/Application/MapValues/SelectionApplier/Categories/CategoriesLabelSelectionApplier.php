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

namespace Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\Categories;

use Akeneo\Pim\Tailored\Application\Common\Selection\Categories\CategoriesLabelSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\CategoriesValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;
use Akeneo\Pim\Tailored\Domain\Query\FindCategoryLabelsInterface;

class CategoriesLabelSelectionApplier implements SelectionApplierInterface
{
    private FindCategoryLabelsInterface $findCategoryLabels;

    public function __construct(FindCategoryLabelsInterface $findCategoryLabels)
    {
        $this->findCategoryLabels = $findCategoryLabels;
    }

    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof CategoriesLabelSelection
            ||!$value instanceof CategoriesValue
        ) {
            throw new \InvalidArgumentException('Cannot apply Categories selection on this entity');
        }

        $categoryCodes = $value->getCategoryCodes();

        $categoryTranslations = $this->findCategoryLabels->byCodes($categoryCodes, $selection->getLocale());
        $selectedData = array_map(static fn ($categoryCode) => $categoryTranslations[$categoryCode] ??
            sprintf('[%s]', $categoryCode), $categoryCodes);

        return implode($selection->getSeparator(), $selectedData);
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $value instanceof CategoriesValue
            && $selection instanceof CategoriesLabelSelection;
    }
}
