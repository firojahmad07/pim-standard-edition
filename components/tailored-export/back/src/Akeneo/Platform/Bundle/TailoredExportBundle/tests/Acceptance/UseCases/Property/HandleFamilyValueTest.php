<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2021 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Platform\TailoredExport\Test\Acceptance\UseCases\Property;

use Akeneo\Platform\TailoredExport\Application\Common\Operation\DefaultValueOperation;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\Family\FamilyCodeSelection;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\Family\FamilyLabelSelection;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\SelectionInterface;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\FamilyValue;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\NullValue;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Platform\TailoredExport\Application\MapValues\MapValuesQuery;
use Akeneo\Platform\TailoredExport\Test\Acceptance\FakeServices\Family\InMemoryFindFamilyLabel;
use PHPUnit\Framework\Assert;

final class HandleFamilyValueTest extends PropertyTestCase
{
    public const PROPERTY_NAME = 'family';

    /**
     * @dataProvider provider
     */
    public function test_it_can_transform_a_family_value(
        array $operations,
        SelectionInterface $selection,
        SourceValueInterface $value,
        array $expected
    ): void {
        $mapValuesQueryHandler = $this->getMapValuesQueryHandler();
        $this->loadFamilyLabels();

        $columnCollection = $this->createSingleSourceColumnCollection($operations, $selection);
        $valueCollection = $this->createSingleValueValueCollection($value);

        $mappedProduct = $mapValuesQueryHandler->handle(new MapValuesQuery($columnCollection, $valueCollection));

        Assert::assertSame($expected, $mappedProduct);
    }

    public function provider(): array
    {
        return [
            'it selects the family code' => [
                'operations' => [],
                'selection' => new FamilyCodeSelection(),
                'value' => new FamilyValue('pants'),
                'expected' => [self::TARGET_NAME => 'pants']
            ],
            'it fallbacks on the family code when the label is not found' => [
                'operations' => [],
                'selection' => new FamilyLabelSelection('en_US'),
                'value' => new FamilyValue('pants'),
                'expected' => [self::TARGET_NAME => '[pants]']
            ],
            'it selects the family label' => [
                'operations' => [],
                'selection' => new FamilyLabelSelection('fr_FR'),
                'value' => new FamilyValue('pants'),
                'expected' => [self::TARGET_NAME => 'Pantalons']
            ],
            'it applies default value operation when value is null' => [
                'operations' => [
                    new DefaultValueOperation('n/a'),
                ],
                'selection' => new FamilyCodeSelection(),
                'value' => new NullValue(),
                'expected' => [self::TARGET_NAME => 'n/a']
            ],
            'it does not apply default value operation when value is not null' => [
                'operations' => [
                    new DefaultValueOperation('n/a'),
                ],
                'selection' => new FamilyCodeSelection(),
                'value' => new FamilyValue('pants'),
                'expected' => [self::TARGET_NAME => 'pants']
            ],
        ];
    }

    private function loadFamilyLabels(): void
    {
        /** @var InMemoryFindFamilyLabel $familyLabelsRepository */
        $familyLabelsRepository = self::$container->get('Akeneo\Platform\TailoredExport\Domain\Query\FindFamilyLabelInterface');
        $familyLabelsRepository->addFamilyLabel('pants', 'fr_FR', 'Pantalons');
    }
}
