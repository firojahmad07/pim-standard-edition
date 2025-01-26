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

namespace Akeneo\ReferenceEntity\Infrastructure\Connector\Api\Record\JsonSchema;

use JsonSchema\Validator;

/**
 * Validate the first level properties of a record using JSON Schema.
 *
 * @author    Laurent Petard <laurent.petard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class RecordPropertiesValidator
{
    public function validate(array $normalizedRecord): array
    {
        if (isset($normalizedRecord['values']) && [] === $normalizedRecord['values']) {
            $normalizedRecord['values'] = (object) [];
        }

        $validator = new Validator();
        $normalizedRecordObject = Validator::arrayToObjectRecursive($normalizedRecord);
        $validator->validate($normalizedRecordObject, $this->getJsonSchema());

        return $validator->getErrors();
    }

    private function getJsonSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                '_links' => [
                    'type' => 'object'
                ],
                'code' => [
                    'type' => ['string'],
                ],
                'values' => [
                    'type' => 'object',
                    'patternProperties' => [
                        '.+' => ['type' => 'array'],
                    ],
                ],
            ],
            'required' => ['code'],
            'additionalProperties' => false,
        ];
    }
}
