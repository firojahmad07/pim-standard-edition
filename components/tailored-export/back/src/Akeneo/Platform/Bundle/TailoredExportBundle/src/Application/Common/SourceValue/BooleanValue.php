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

namespace Akeneo\Platform\TailoredExport\Application\Common\SourceValue;

class BooleanValue implements SourceValueInterface
{
    private bool $data;

    public function __construct(bool $data)
    {
        $this->data = $data;
    }

    public function getData(): bool
    {
        return $this->data;
    }
}
