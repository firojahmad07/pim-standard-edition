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

use Akeneo\Pim\Tailored\Application\Common\Selection\AssetCollection\AssetCollectionMediaLinkSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\AssetCollectionValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;
use Akeneo\Pim\Tailored\Domain\Query\AssetCollection\FindAssetMainMediaAttributeInterface;
use Akeneo\Pim\Tailored\Domain\Query\AssetCollection\MediaLinkAsMainMedia;
use Akeneo\Pim\Tailored\Domain\Query\FindAssetMainMediaDataInterface;

class AssetCollectionMediaLinkSelectionApplier implements SelectionApplierInterface
{
    private FindAssetMainMediaDataInterface $findAssetMainMediaData;
    private FindAssetMainMediaAttributeInterface $findAssetMainMediaAttribute;

    public function __construct(
        FindAssetMainMediaDataInterface $findAssetMainMediaData,
        FindAssetMainMediaAttributeInterface $findAssetMainMediaAttribute
    ) {
        $this->findAssetMainMediaData = $findAssetMainMediaData;
        $this->findAssetMainMediaAttribute = $findAssetMainMediaAttribute;
    }

    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof AssetCollectionMediaLinkSelection
            || !$value instanceof AssetCollectionValue
        ) {
            throw new \InvalidArgumentException('Cannot apply Asset Collection selection on this entity');
        }

        $assetMainMediaLinkData = $this->findAssetMainMediaData->forAssetFamilyAndAssetCodes(
            $selection->getAssetFamilyCode(),
            $value->getAssetCodes(),
            $selection->getChannel(),
            $selection->getLocale()
        );

        if ($selection->withPrefixAndSuffix()) {
            $assetMainMediaLinkData = $this->applyPrefixAndSuffix($selection, $assetMainMediaLinkData);
        }

        return implode($selection->getSeparator(), $assetMainMediaLinkData);
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof AssetCollectionMediaLinkSelection
            && $value instanceof AssetCollectionValue;
    }

    private function applyPrefixAndSuffix(AssetCollectionMediaLinkSelection $selection, array $assetMainMediaLinkData): array
    {
        $attributeAsMainMedia = $this->findAssetMainMediaAttribute->forAssetFamily($selection->getAssetFamilyCode());
        if (!$attributeAsMainMedia instanceof MediaLinkAsMainMedia) {
            throw new \InvalidArgumentException('Asset main media is not a media link');
        }

        return array_map(
            static fn (string $mediaLinkData) => sprintf('%s%s%s', $attributeAsMainMedia->getPrefix(), $mediaLinkData, $attributeAsMainMedia->getSuffix()),
            $assetMainMediaLinkData
        );
    }
}
