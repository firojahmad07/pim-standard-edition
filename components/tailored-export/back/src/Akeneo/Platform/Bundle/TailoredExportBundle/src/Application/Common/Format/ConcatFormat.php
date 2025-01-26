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

namespace Akeneo\Platform\TailoredExport\Application\Common\Format;

class ConcatFormat implements FormatInterface
{
    private ElementCollection $elementCollection;
    private bool $hasSpaceBetween;

    public function __construct(ElementCollection $elementCollection, bool $hasSpaceBetween)
    {
        $this->elementCollection = $elementCollection;
        $this->hasSpaceBetween = $hasSpaceBetween;
    }

    public function hasSpaceBetween(): bool
    {
        return $this->hasSpaceBetween;
    }

    public function getElementCollection(): ElementCollection
    {
        return $this->elementCollection;
    }
}
