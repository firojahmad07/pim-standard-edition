<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2018 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\AssetManager\Infrastructure\Connector\Api\Asset\JsonSchema\Value;

use Akeneo\AssetManager\Domain\Model\Attribute\TextAttribute;
use Akeneo\AssetManager\Infrastructure\Connector\Api\Asset\JsonSchema\AssetValueValidatorInterface;
use JsonSchema\Validator;

/**
 * @author    Laurent Petard <laurent.petard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class TextTypeValidator implements AssetValueValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $normalizedAsset): array
    {
        $asset = Validator::arrayToObjectRecursive($normalizedAsset);
        $validator = new Validator();
        $validator->validate($asset, $this->getJsonSchema());

        return $validator->getErrors();
    }

    public function forAttributeType(): string
    {
        return TextAttribute::class;
    }

    private function getJsonSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'values' => [
                    'type' => 'object',
                    'patternProperties' => [
                        '.+' => [
                            'type'  => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'locale' => [
                                        'type' => ['string', 'null'],
                                    ],
                                    'channel' => [
                                        'type' => ['string', 'null'],
                                    ],
                                    'data' => [
                                        'type' => ['string', 'null'],
                                    ],
                                ],
                                'required' => ['locale', 'channel', 'data'],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
