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

namespace Specification\Akeneo\Platform\TailoredExport\Application\MapValues\SelectionApplier\QuantifiedAssociations;

use Akeneo\Platform\TailoredExport\Application\Common\Selection\Boolean\BooleanSelection;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\QuantifiedAssociations\QuantifiedAssociationsQuantitySelection;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\BooleanValue;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\QuantifiedAssociation;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\QuantifiedAssociationsValue;
use PhpSpec\ObjectBehavior;

class QuantifiedAssociationsQuantitySelectionApplierSpec extends ObjectBehavior
{
    public function it_applies_the_selection_on_products()
    {
        $productAssociations = [new QuantifiedAssociation('1111111171', 3), new QuantifiedAssociation('13620748', 2)];
        $productModelAssociations = [new QuantifiedAssociation('athena', 1), new QuantifiedAssociation('hat', 2)];

        $selection = new QuantifiedAssociationsQuantitySelection('products', ',');
        $value = new QuantifiedAssociationsValue($productAssociations, $productModelAssociations);

        $this->applySelection($selection, $value)
            ->shouldReturn('3,2');
    }

    public function it_applies_the_selection_on_product_models()
    {
        $productAssociations = [new QuantifiedAssociation('1111111171', 3), new QuantifiedAssociation('13620748', 2)];
        $productModelAssociations = [new QuantifiedAssociation('athena', 1), new QuantifiedAssociation('hat', 2)];

        $selection = new QuantifiedAssociationsQuantitySelection('product_models', '|');
        $value = new QuantifiedAssociationsValue($productAssociations, $productModelAssociations);

        $this->applySelection($selection, $value)
            ->shouldReturn('1|2');
    }

    public function it_does_not_apply_selection_on_not_supported_selections_and_values()
    {
        $notSupportedSelection = new BooleanSelection();
        $notSupportedValue = new BooleanValue(true);

        $this
            ->shouldThrow(new \InvalidArgumentException('Cannot apply quantified associations quantity selection on this entity'))
            ->during('applySelection', [$notSupportedSelection, $notSupportedValue]);
    }

    public function it_supports_quantified_association_quantity_selection_with_quantified_association_value()
    {
        $selection = new QuantifiedAssociationsQuantitySelection('products', '/');
        $value = new QuantifiedAssociationsValue([], []);

        $this->supports($selection, $value)->shouldReturn(true);
    }

    public function it_does_not_support_other_selections_and_values()
    {
        $notSupportedSelection = new BooleanSelection();
        $notSupportedValue = new BooleanValue(true);

        $this->supports($notSupportedSelection, $notSupportedValue)->shouldReturn(false);
    }
}
