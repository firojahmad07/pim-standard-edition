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

namespace Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\AssetCollection;

use Akeneo\Pim\Tailored\Application\Common\Selection\AssetCollection\AssetCollectionCodeSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\AssetCollectionValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;

class AssetCollectionCodeSelectionApplier implements SelectionApplierInterface
{
    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof AssetCollectionCodeSelection
            || !$value instanceof AssetCollectionValue
        ) {
            throw new \InvalidArgumentException('Cannot apply Asset Collection selection on this entity');
        }

        return implode($selection->getSeparator(), $value->getAssetCodes());
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof AssetCollectionCodeSelection
            && $value instanceof AssetCollectionValue;
    }
}
