<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2021 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\AssetManager\Application\Asset\MassDeleteAssets;

/**
 * Command to mass delete assets from a family
 * @author Julien Sanchez <julien@akeneo.com>
 * @copyright 2021 Akeneo SAS (https://www.akeneo.com)
 */
class MassDeleteAssetsCommand
{
    public string $assetFamilyIdentifier;
    public array $query;

    public function __construct(string $assetFamilyIdentifier, array $query)
    {
        $this->assetFamilyIdentifier = $assetFamilyIdentifier;
        $this->query = $query;
    }
}
