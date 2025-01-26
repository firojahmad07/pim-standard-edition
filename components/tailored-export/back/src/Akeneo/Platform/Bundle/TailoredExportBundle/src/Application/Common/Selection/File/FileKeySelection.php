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

namespace Akeneo\Platform\TailoredExport\Application\Common\Selection\File;

final class FileKeySelection implements FileSelectionInterface
{
    public const TYPE = 'key';
    private string $attributeCode;

    public function __construct(string $attributeCode)
    {
        $this->attributeCode = $attributeCode;
    }

    public function getAttributeCode(): string
    {
        return $this->attributeCode;
    }
}
