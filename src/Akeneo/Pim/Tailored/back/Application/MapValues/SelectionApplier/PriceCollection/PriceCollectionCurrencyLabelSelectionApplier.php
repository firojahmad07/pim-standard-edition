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

namespace Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\PriceCollection;

use Akeneo\Pim\Tailored\Application\Common\Selection\PriceCollection\PriceCollectionCurrencyLabelSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\Price;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\PriceCollectionValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;
use Akeneo\Pim\Tailored\Domain\Query\FindCurrencyLabelsInterface;

class PriceCollectionCurrencyLabelSelectionApplier implements SelectionApplierInterface
{
    private FindCurrencyLabelsInterface $findCurrencyLabels;

    public function __construct(FindCurrencyLabelsInterface $findCurrencyLabels)
    {
        $this->findCurrencyLabels = $findCurrencyLabels;
    }

    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (!($selection instanceof PriceCollectionCurrencyLabelSelection
            && $value instanceof PriceCollectionValue)) {
            throw new \InvalidArgumentException('Cannot apply Price collection selection on this entity');
        }

        $priceCollection = $value->getPriceCollection();
        $currencies = $selection->getCurrencies();
        if ($currencies) {
            $priceCollection = array_filter($priceCollection, static fn (Price $price) => in_array($price->getCurrency(), $currencies));
        }

        $currencyCodes = array_map(static fn (Price $price) => $price->getCurrency(), $priceCollection);

        $currencyLabels = $this->findCurrencyLabels->byCodes(array_values($currencyCodes), $selection->getLocale());

        $selectedData = array_map(static fn ($currencyCode) => $currencyLabels[$currencyCode] ?? sprintf('[%s]', $currencyCode), $currencyCodes);

        return implode($selection->getSeparator(), $selectedData);
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof PriceCollectionCurrencyLabelSelection
            && $value instanceof PriceCollectionValue;
    }
}
