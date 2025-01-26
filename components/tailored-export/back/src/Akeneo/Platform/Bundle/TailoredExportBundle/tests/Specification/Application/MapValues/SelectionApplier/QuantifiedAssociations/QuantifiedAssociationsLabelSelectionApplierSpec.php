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
use Akeneo\Platform\TailoredExport\Application\Common\Selection\QuantifiedAssociations\QuantifiedAssociationsLabelSelection;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\BooleanValue;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\QuantifiedAssociation;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\QuantifiedAssociationsValue;
use Akeneo\Platform\TailoredExport\Domain\Query\FindProductLabelsInterface;
use Akeneo\Platform\TailoredExport\Domain\Query\FindProductModelLabelsInterface;
use PhpSpec\ObjectBehavior;

class QuantifiedAssociationsLabelSelectionApplierSpec extends ObjectBehavior
{
    public function let(
        FindProductLabelsInterface $findProductLabels,
        FindProductModelLabelsInterface $findProductModelLabels
    ) {
        $this->beConstructedWith($findProductLabels, $findProductModelLabels);
    }

    public function it_applies_the_selection_on_products(FindProductLabelsInterface $findProductLabels)
    {
        $productAssociations = [new QuantifiedAssociation('1111111171', 3), new QuantifiedAssociation('13620748', 2)];
        $productModelAssociations = [new QuantifiedAssociation('athena', 1), new QuantifiedAssociation('hat', 2)];
        $value = new QuantifiedAssociationsValue($productAssociations, $productModelAssociations);

        $selection = new QuantifiedAssociationsLabelSelection(
            'products',
            'ecommerce',
            'fr_FR',
            ','
        );

        $findProductLabels->byIdentifiers(
            ['1111111171', '13620748'],
            'ecommerce',
            'fr_FR'
        )->shouldBeCalledTimes(1)->willReturn([
            '1111111171' => 'Bag',
        ]);

        $this->applySelection($selection, $value)
            ->shouldReturn('Bag,[13620748]');
    }


    public function it_applies_the_selection_on_product_models(FindProductModelLabelsInterface $findProductModelLabels)
    {
        $productAssociations = [new QuantifiedAssociation('1111111171', 3), new QuantifiedAssociation('13620748', 2)];
        $productModelAssociations = [new QuantifiedAssociation('athena', 1), new QuantifiedAssociation('hat', 2)];
        $value = new QuantifiedAssociationsValue($productAssociations, $productModelAssociations);

        $selection = new QuantifiedAssociationsLabelSelection(
            'product_models',
            'ecommerce',
            'fr_FR',
            ','
        );

        $findProductModelLabels->byCodes(
            ['athena', 'hat'],
            'ecommerce',
            'fr_FR'
        )->shouldBeCalledTimes(1)->willReturn([
            'athena' => 'Athena',
        ]);

        $this->applySelection($selection, $value)
            ->shouldReturn('Athena,[hat]');
    }

    public function it_does_not_apply_selection_on_not_supported_selections_and_values()
    {
        $notSupportedSelection = new BooleanSelection();
        $notSupportedValue = new BooleanValue(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('Cannot apply quantified associations label selection on this entity'))
            ->during('applySelection', [$notSupportedSelection, $notSupportedValue]);
    }

    public function it_supports_quantified_associations_label_selection_with_quantified_associations_value()
    {
        $selection = new QuantifiedAssociationsLabelSelection(
            'product_models',
            'ecommerce',
            'fr_FR',
            ','
        );
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
