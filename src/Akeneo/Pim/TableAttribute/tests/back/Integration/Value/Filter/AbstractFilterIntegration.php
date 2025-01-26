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

namespace Akeneo\Test\Pim\TableAttribute\Integration\Value\Filter;

use Akeneo\Channel\Component\Model\ChannelInterface;
use Akeneo\Pim\Structure\Component\AttributeTypes;
use Akeneo\Test\Integration\Configuration;
use Akeneo\Test\Integration\TestCase;
use PHPUnit\Framework\Assert;

abstract class AbstractFilterIntegration extends TestCase
{
    abstract protected function getTestedOperator(): string;

    protected function createProductWithNutrition(string $identifier, array $nutritionValue, ?string $familyCode = null): void
    {
        $product = $this->get('pim_catalog.builder.product')->createProduct($identifier);
        $this->get('pim_catalog.updater.product')->update(
            $product,
            [
                'categories' => ['master'],
                'values' => [
                    'nutrition' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => $nutritionValue,
                        ],
                    ],
                ],
                'family' => $familyCode,
            ]
        );

        $violations = $this->get('pim_catalog.validator.product')->validate($product);
        Assert::assertCount(0, $violations, \sprintf('The product is not valid: %s', $violations));
        $this->get('pim_catalog.saver.product')->save($product);
        $this->get('akeneo_elasticsearch.client.product_and_product_model')->refreshIndex();
    }

    protected function createProductWithValues(string $identifier, array $values, ?string $familyCode = null): void
    {
        $product = $this->get('pim_catalog.builder.product')->createProduct($identifier);
        $this->get('pim_catalog.updater.product')->update(
            $product,
            [
                'categories' => ['master'],
                'values' => $values,
                'family' => $familyCode,
            ]
        );

        $violations = $this->get('pim_catalog.validator.product')->validate($product);
        Assert::assertCount(0, $violations, \sprintf('The product is not valid: %s', $violations));
        $this->get('pim_catalog.saver.product')->save($product);
        $this->get('akeneo_elasticsearch.client.product_and_product_model')->refreshIndex();
    }

    protected function assertFilter(string $attributeCode, array $filter, array $expectedProductIdentifiers): void
    {
        $data = array_filter([
            'value' => $filter['value'] ?? null,
            'column' => $filter['column'] ?? null,
            'row' => $filter['row'] ?? null,
        ], static fn ($value): bool => null !== $value);

        $pqb = $this->get('pim_catalog.query.product_query_builder_factory')->create();
        $pqb->addFilter($attributeCode, $this->getTestedOperator(), $data, [
            'locale' => $filter['locale'] ?? null,
            'scope' => $filter['scope'] ?? null,
        ]);
        $result = $pqb->execute();

        $actualProductIdentifiers = [];
        foreach ($result as $product) {
            $actualProductIdentifiers[] = $product->getIdentifier();
        }

        Assert::assertEqualsCanonicalizing($expectedProductIdentifiers, $actualProductIdentifiers);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->createChannel([
            'code' => 'mobile',
            'category_tree' => 'master',
            'currencies' => ['USD'],
            'locales' => ['en_US', 'fr_FR'],
            'labels' => [],
        ]);
        $attribute = $this->get('pim_catalog.factory.attribute')->create();
        $this->get('pim_catalog.updater.attribute')->update(
            $attribute,
            [
                'code' => 'nutrition',
                'type' => AttributeTypes::TABLE,
                'group' => 'other',
                'table_configuration' => [
                    [
                        'code' => 'ingredient',
                        'data_type' => 'select',
                        'options' => [
                            ['code' => 'sugar'],
                            ['code' => 'salt'],
                            ['code' => 'egg'],
                            ['code' => 'flour'],
                            ['code' => 'chocolate'],
                            ['code' => 'cheese'],
                        ],
                    ],
                    [
                        'code' => 'quantity',
                        'data_type' => 'number',
                        'validations' => ['decimals_allowed' => true],
                    ],
                    [
                        'code' => 'allergen',
                        'data_type' => 'boolean',
                    ],
                    [
                        'code' => 'additional_info',
                        'data_type' => 'text',
                    ],
                    [
                        'code' => 'nutrition_score',
                        'data_type' => 'select',
                        'options' => [
                            ['code' => 'A'],
                            ['code' => 'B'],
                            ['code' => 'C'],
                            ['code' => 'D'],
                        ],
                    ],
                ],
            ]
        );
        $validator = $this->get('validator');
        $violations = $validator->validate($attribute);
        Assert::assertCount(0, $violations, \sprintf('The attribute is not valid: %s', $violations));
        $this->get('pim_catalog.saver.attribute')->save($attribute);

        $localizableScopableAttribute = $this->get('pim_catalog.factory.attribute')->create();
        $this->get('pim_catalog.updater.attribute')->update(
            $localizableScopableAttribute,
            [
                'code' => 'localizable_scopable_nutrition',
                'type' => AttributeTypes::TABLE,
                'localizable' => true,
                'scopable' => true,
                'group' => 'other',
                'table_configuration' => [
                    [
                        'code' => 'ingredient',
                        'data_type' => 'select',
                        'options' => [
                            ['code' => 'sugar'],
                            ['code' => 'salt'],
                            ['code' => 'egg'],
                            ['code' => 'flour'],
                            ['code' => 'chocolate'],
                            ['code' => 'cheese'],
                        ],
                    ],
                    [
                        'code' => 'quantity',
                        'data_type' => 'number',
                        'validations' => ['decimals_allowed' => true],
                    ],
                    [
                        'code' => 'allergen',
                        'data_type' => 'boolean',
                    ],
                    [
                        'code' => 'additional_info',
                        'data_type' => 'text',
                    ],
                    [
                        'code' => 'nutrition_score',
                        'data_type' => 'select',
                        'options' => [
                            ['code' => 'A'],
                            ['code' => 'B'],
                            ['code' => 'C'],
                            ['code' => 'D'],
                        ],
                    ],
                ],
            ]
        );
        $validator = $this->get('validator');
        $violations = $validator->validate($localizableScopableAttribute);
        Assert::assertCount(0, $violations, \sprintf('The attribute is not valid: %s', $violations));
        $this->get('pim_catalog.saver.attribute')->save($localizableScopableAttribute);

        $family = $this->get('pim_catalog.factory.family')->create();
        $this->get('pim_catalog.updater.family')->update($family, [
            'code' => 'family_with_table',
            'attributes' => ['sku', 'nutrition', 'localizable_scopable_nutrition'],
        ]);
        $familyViolations = $validator->validate($family);
        Assert::assertCount(0, $familyViolations, \sprintf('The attribute is not valid: %s', $familyViolations));
        $this->get('pim_catalog.saver.family')->save($family);

        // this product should NEVER appear in the results
        $this->createProductWithNutrition('empty_product_without_family', []);

        $this->get('akeneo_elasticsearch.client.product_and_product_model')->refreshIndex();
    }

    private function createChannel(array $data = []): ChannelInterface
    {
        $channel = $this->get('pim_catalog.factory.channel')->create();
        $this->get('pim_catalog.updater.channel')->update($channel, $data);

        $errors = $this->get('validator')->validate($channel);
        Assert::assertCount(0, $errors, $errors->__toString());

        $this->get('pim_catalog.saver.channel')->save($channel);

        return $channel;
    }

    protected function getConfiguration(): Configuration
    {
        return $this->catalog->useMinimalCatalog();
    }
}
