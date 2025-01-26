<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2019 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\AssetManager\Infrastructure\Filesystem\PreviewGenerator;

/**
 * @author    Christophe Chausseray <christophe.chausseray@akeneo.com>
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 */
interface DefaultImageProviderInterface
{
    /**
     * Return the url of the default image corresponding to the specified file type
     *
     * @param $fileType File type, defined in Akeneo\Pim\Enrichment\Bundle\File\FileTypes
     * @param $filter   Transformation filter name
     */
    public function getImageUrl(string $fileType, string $filter): string;
}
