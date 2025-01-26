<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2018 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\AssetManager\Common\Fake;

use Akeneo\AssetManager\Domain\Model\AssetFamily\AssetFamilyIdentifier;
use Akeneo\AssetManager\Domain\Model\AssetFamily\AttributeAsLabelReference;
use Akeneo\AssetManager\Domain\Query\AssetFamily\FindAssetFamilyAttributeAsLabelInterface;
use Akeneo\AssetManager\Domain\Repository\AssetFamilyNotFoundException;

class InMemoryFindAssetFamilyAttributeAsLabel implements FindAssetFamilyAttributeAsLabelInterface
{
    private InMemoryAssetFamilyRepository $assetFamilyRepository;

    public function __construct(InMemoryAssetFamilyRepository $assetFamilyRepository)
    {
        $this->assetFamilyRepository = $assetFamilyRepository;
    }

    public function find(AssetFamilyIdentifier $assetFamilyIdentifier): AttributeAsLabelReference
    {
        try {
            $assetFamily = $this->assetFamilyRepository->getByIdentifier($assetFamilyIdentifier);
        } catch (AssetFamilyNotFoundException $e) {
            return AttributeAsLabelReference::noReference();
        }

        return $assetFamily->getAttributeAsLabelReference();
    }
}
