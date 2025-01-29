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

namespace Akeneo\Pim\Tailored\Infrastructure\Hydrator;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductModelInterface;
use Akeneo\Pim\Tailored\Application\Common\Column\ColumnCollection;
use Akeneo\Pim\Tailored\Application\Common\Source\AssociationTypeSource;
use Akeneo\Pim\Tailored\Application\Common\Source\AttributeSource;
use Akeneo\Pim\Tailored\Application\Common\Source\PropertySource;
use Akeneo\Pim\Tailored\Application\Common\ValueCollection;

class ValueCollectionHydrator
{
    private ValueHydrator $valueHydrator;

    public function __construct(
        ValueHydrator $valueHydrator
    ) {
        $this->valueHydrator = $valueHydrator;
    }

    /**
     * @param ProductInterface|ProductModelInterface $productOrProductModel
     */
    public function hydrate(
        $productOrProductModel,
        ColumnCollection $columnConfiguration
    ): ValueCollection {
        if (
            !$productOrProductModel instanceof ProductInterface
            && !$productOrProductModel instanceof ProductModelInterface
        ) {
            throw new \InvalidArgumentException('Cannot hydrate this entity');
        }

        $allSources = $columnConfiguration->getAllSources();
        $valueCollection = new ValueCollection();

        foreach ($allSources as $source) {
            $value = $this->valueHydrator->hydrate($productOrProductModel, $source);

            switch (true) {
                case $source instanceof AttributeSource:
                    $valueCollection->add($value, $source->getCode(), $source->getChannel(), $source->getLocale());
                    break;
                case $source instanceof PropertySource:
                    $valueCollection->add($value, $source->getName(), null, null);
                    break;
                case $source instanceof AssociationTypeSource:
                    $valueCollection->add($value, $source->getCode(), null, null);
                    break;
                default:
                    throw new \InvalidArgumentException('Unsupported source');
            }
        }

        return $valueCollection;
    }
}
