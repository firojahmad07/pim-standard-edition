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

namespace Specification\Akeneo\Platform\TailoredExport\Application\Common\Selection\File;

use PhpSpec\ObjectBehavior;

class FilePathSelectionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('nice_attribute_code');
    }

    public function it_returns_the_attribute_code()
    {
        $this->getAttributeCode()->shouldReturn('nice_attribute_code');
    }
}
