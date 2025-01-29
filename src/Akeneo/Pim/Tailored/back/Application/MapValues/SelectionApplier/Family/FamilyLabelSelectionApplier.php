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

namespace Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\Family;

use Akeneo\Pim\Tailored\Application\Common\Selection\Family\FamilyLabelSelection;
use Akeneo\Pim\Tailored\Application\Common\Selection\SelectionInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\FamilyValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\MapValues\SelectionApplier\SelectionApplierInterface;
use Akeneo\Pim\Tailored\Domain\Query\FindFamilyLabelInterface;

class FamilyLabelSelectionApplier implements SelectionApplierInterface
{
    private FindFamilyLabelInterface $findFamilyLabel;

    public function __construct(FindFamilyLabelInterface $findFamilyLabel)
    {
        $this->findFamilyLabel = $findFamilyLabel;
    }

    public function applySelection(SelectionInterface $selection, SourceValueInterface $value): string
    {
        if (
            !$selection instanceof FamilyLabelSelection
            || !$value instanceof FamilyValue) {
            throw new \InvalidArgumentException('Cannot apply Family selection on this entity');
        }

        $familyCode = $value->getFamilyCode();
        $familyTranslation = $this->findFamilyLabel->byCode($familyCode, $selection->getLocale());

        return $familyTranslation ?? sprintf('[%s]', $familyCode);
    }

    public function supports(SelectionInterface $selection, SourceValueInterface $value): bool
    {
        return $selection instanceof FamilyLabelSelection
            && $value instanceof FamilyValue;
    }
}
