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

namespace Akeneo\AssetManager\Application\Asset\MassEditAssets;

use Akeneo\AssetManager\Application\Asset\EditAsset\CommandFactory\AbstractEditValueCommand;

/**
 * Command to mass edit assets from a family
 *
 * @author Julien Sanchez <julien@akeneo.com>
 * @copyright 2021 Akeneo SAS (https://www.akeneo.com)
 */
class MassEditAssetsCommand
{
    public string $assetFamilyIdentifier;
    public array $query;
    /** @var AbstractEditValueCommand[] */
    public array $editValueCommands;

    public function __construct(string $assetFamilyIdentifier, array $query, array $editValueCommands)
    {
        $this->assetFamilyIdentifier = $assetFamilyIdentifier;
        $this->query = $query;
        $this->editValueCommands = $editValueCommands;
    }
}
