<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\AssetManager\Component\Query;

/**
 * Fetches a asset's basic information such as label to show in the datagrid.
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface GetAssetInformationQueryInterface
{
    public function fetch(string $assetFamilyIdentifier, string $assetCode): AssetInformation;
}
