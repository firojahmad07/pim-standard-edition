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

namespace Specification\Akeneo\Platform\TailoredExport\Application\Common\SourceValue;

use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\ReferenceEntityValue;
use PhpSpec\ObjectBehavior;

class ReferenceEntityValueSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('a_record_code');
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(ReferenceEntityValue::class);
    }

    public function it_returns_the_record_code()
    {
        $this->getRecordCode()->shouldReturn('a_record_code');
    }
}
