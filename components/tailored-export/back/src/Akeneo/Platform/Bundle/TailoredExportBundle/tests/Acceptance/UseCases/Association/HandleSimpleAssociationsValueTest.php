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

namespace Akeneo\Platform\TailoredExport\Test\Acceptance\UseCases\Association;

use Akeneo\Platform\TailoredExport\Application\Common\Selection\SelectionInterface;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\SimpleAssociations\SimpleAssociationsCodeSelection;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\SimpleAssociations\SimpleAssociationsGroupsLabelSelection;
use Akeneo\Platform\TailoredExport\Application\Common\Selection\SimpleAssociations\SimpleAssociationsLabelSelection;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\SimpleAssociationsValue;
use Akeneo\Platform\TailoredExport\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Platform\TailoredExport\Application\MapValues\MapValuesQuery;
use Akeneo\Platform\TailoredExport\Test\Acceptance\FakeServices\Group\InMemoryFindGroupLabels;
use Akeneo\Platform\TailoredExport\Test\Acceptance\FakeServices\Product\InMemoryFindProductLabels;
use Akeneo\Platform\TailoredExport\Test\Acceptance\FakeServices\ProductModel\InMemoryFindProductModelLabels;
use PHPUnit\Framework\Assert;

final class HandleSimpleAssociationsValueTest extends AssociationTestCase
{
    public const ASSOCIATION_TYPE_CODE = 'X_SELL';

    /**
     * @dataProvider provider
     */
    public function test_it_can_transform_a_associations_value(
        array $operations,
        SelectionInterface $selection,
        SourceValueInterface $value,
        array $expected
    ): void {
        $mapValuesQueryHandler = $this->getMapValuesQueryHandler();
        $this->loadAssociatedEntityLabels();

        $columnCollection = $this->createSingleSourceColumnCollection(false, $operations, $selection);
        $valueCollection = $this->createSingleValueValueCollection($value);

        $mappedProduct = $mapValuesQueryHandler->handle(new MapValuesQuery($columnCollection, $valueCollection));

        Assert::assertSame($expected, $mappedProduct);
    }

    public function provider(): array
    {
        return [
            'Associated products code selection with ";" separator' => [
                'operations' => [],
                'selection' => new SimpleAssociationsCodeSelection('products', ';'),
                'value' => new SimpleAssociationsValue(['1111111171', '13620748'], ['diana', 'stilleto'], ['summerSale2020', 'summerSale2021']),
                'expected' => [self::TARGET_NAME => '1111111171;13620748']
            ],
            'Associated products code selection with "," separator' => [
                'operations' => [],
                'selection' => new SimpleAssociationsCodeSelection('products', ','),
                'value' => new SimpleAssociationsValue(['1111111171', '13620748'], ['diana', 'stilleto'], ['summerSale2020', 'summerSale2021']),
                'expected' => [self::TARGET_NAME => '1111111171,13620748']
            ],
            'Associated product models code selection' => [
                'operations' => [],
                'selection' => new SimpleAssociationsCodeSelection('product_models', ','),
                'value' => new SimpleAssociationsValue(['1111111171', '13620748'], ['diana', 'stilleto'], ['summerSale2020', 'summerSale2021']),
                'expected' => [self::TARGET_NAME => 'diana,stilleto']
            ],
            'Associated groups code selection' => [
                'operations' => [],
                'selection' => new SimpleAssociationsCodeSelection('groups', ','),
                'value' => new SimpleAssociationsValue(['1111111171', '13620748'], ['diana', 'stilleto'], ['summerSale2020', 'summerSale2021']),
                'expected' => [self::TARGET_NAME => 'summerSale2020,summerSale2021']
            ],
            'Associated products label selection with ";" as separator' => [
                'operations' => [],
                'selection' => new SimpleAssociationsLabelSelection('products', 'ecommerce', 'en_US', ';'),
                'value' => new SimpleAssociationsValue(['1111111171', '122334620748'], ['diana', 'stilleto'], ['summerSale2020', 'summerSale2021']),
                'expected' => [self::TARGET_NAME => 'Bag;Sunglasses']
            ],
            'Associated products label selection with a product without translation' => [
                'operations' => [],
                'selection' => new SimpleAssociationsLabelSelection('products', 'ecommerce', 'en_US', ','),
                'value' => new SimpleAssociationsValue(['1111111171', '13620748'], ['diana', 'stilleto'], ['summerSale2020', 'summerSale2021']),
                'expected' => [self::TARGET_NAME => 'Bag,[13620748]']
            ],
            'Associated product models label selection with a product model without translation' => [
                'operations' => [],
                'selection' => new SimpleAssociationsLabelSelection('product_models', 'ecommerce', 'en_US', ','),
                'value' => new SimpleAssociationsValue(['1111111171', '13620748'], ['diana', 'stilleto'], ['summerSale2020', 'summerSale2021']),
                'expected' => [self::TARGET_NAME => 'Diana,[stilleto]']
            ],
            'Associated groups label selection with a group without translation' => [
                'operations' => [],
                'selection' => new SimpleAssociationsGroupsLabelSelection('en_US', ','),
                'value' => new SimpleAssociationsValue(['1111111171', '13620748'], ['diana', 'stilleto'], ['summerSale2020', 'summerSale2021']),
                'expected' => [self::TARGET_NAME => '[summerSale2020],Summer sale 2021']
            ],
        ];
    }

    private function loadAssociatedEntityLabels(): void
    {
        /** @var InMemoryFindProductLabels $productLabelRepository */
        $productLabelRepository = self::$container->get('Akeneo\Platform\TailoredExport\Domain\Query\FindProductLabelsInterface');
        $productLabelRepository->addProductLabel('1111111171', 'ecommerce', 'en_US', 'Bag');
        $productLabelRepository->addProductLabel('122334620748', 'ecommerce', 'en_US', 'Sunglasses');

        /** @var InMemoryFindProductModelLabels $productLabelRepository */
        $productLabelRepository = self::$container->get('Akeneo\Platform\TailoredExport\Domain\Query\FindProductModelLabelsInterface');
        $productLabelRepository->addProductModelLabel('diana', 'ecommerce', 'en_US', 'Diana');

        /** @var InMemoryFindGroupLabels $groupLabelRepository */
        $groupLabelRepository = self::$container->get('Akeneo\Platform\TailoredExport\Domain\Query\FindGroupLabelsInterface');
        $groupLabelRepository->addGroupLabel('summerSale2021', 'en_US', 'Summer sale 2021');
    }
}
