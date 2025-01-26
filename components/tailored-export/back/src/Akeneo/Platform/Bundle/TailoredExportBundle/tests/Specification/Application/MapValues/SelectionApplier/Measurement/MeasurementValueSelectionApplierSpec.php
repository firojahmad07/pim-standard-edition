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

namespace Specification\Akeneo\Platform\TailoredExport\Application\MapValues\SelectionApplier\Measurement;

use Akeneo\Platform\TailoredExport\Application\Common\Selection\Boolean\BooleanSelection;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\Measurement\MeasurementValueSelection;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\BooleanValue;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\MeasurementValue;
use PhpSpec\ObjectBehavior;

class MeasurementValueSelectionApplierSpec extends ObjectBehavior
{
    public function it_applies_the_selection()
    {
        $selection = new MeasurementValueSelection('.');
        $value = new MeasurementValue('10.4', 'kilogram');

        $this->applySelection($selection, $value)
            ->shouldReturn('10.4');
    }

    public function it_applies_the_selection_and_format_using_decimal_separator()
    {
        $selection = new MeasurementValueSelection(',');
        $value = new MeasurementValue('10.4', 'kilogram');

        $this->applySelection($selection, $value)
            ->shouldReturn('10,4');
    }

    public function it_does_not_apply_selection_on_not_supported_selections_and_values()
    {
        $notSupportedSelection = new BooleanSelection();
        $notSupportedValue = new BooleanValue(true);

        $this
            ->shouldThrow(new \InvalidArgumentException('Cannot apply Measurement value selection on this entity'))
            ->during('applySelection', [$notSupportedSelection, $notSupportedValue]);
    }

    public function it_supports_measurement_value_selection_with_measurement_value()
    {
        $selection = new MeasurementValueSelection('.');
        $value = new MeasurementValue('10', 'kilogram');

        $this->supports($selection, $value)->shouldReturn(true);
    }

    public function it_does_not_support_other_selections_and_values()
    {
        $notSupportedSelection = new BooleanSelection();
        $notSupportedValue = new BooleanValue(true);

        $this->supports($notSupportedSelection, $notSupportedValue)->shouldReturn(false);
    }
}
