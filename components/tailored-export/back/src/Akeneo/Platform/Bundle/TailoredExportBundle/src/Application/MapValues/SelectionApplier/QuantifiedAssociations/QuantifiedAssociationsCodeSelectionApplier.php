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

namespace Akeneo\Platform\TailoredExport\Application\MapValues\SelectionApplier\QuantifiedAssociations;

use Akeneo\Platform\TailoredExport\Application\Common\Selection\QuantifiedAssociations\QuantifiedAssociationsCodeSelection;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\SelectionInterface;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\QuantifiedAssociationsValue;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Platform\TailoredExport\Application\MapValues\SelectionApplier\SelectionApplierInterface;

class QuantifiedAssociationsCodeSelectionApplier implements SelectionApplierInterface
{
    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof QuantifiedAssociationsCodeSelection
            || !$value instanceof QuantifiedAssociationsValue
        ) {
            throw new \InvalidArgumentException('Cannot apply quantified associations code selection on this entity');
        }

        $associatedEntityCodes = $this->getAssociatedEntityCodes($selection, $value);

        return \implode($selection->getSeparator(), $associatedEntityCodes);
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof QuantifiedAssociationsCodeSelection
            && $value instanceof QuantifiedAssociationsValue;
    }

    /**
     * @return string[]
     */
    private function getAssociatedEntityCodes(QuantifiedAssociationsCodeSelection $selection, QuantifiedAssociationsValue $value): array
    {
        if ($selection->isProductsSelection()) {
            return $value->getAssociatedProductIdentifiers();
        } elseif ($selection->isProductModelsSelection()) {
            return $value->getAssociatedProductModelCodes();
        }

        throw new \InvalidArgumentException('Entity type is not supported in this selection');
    }
}
