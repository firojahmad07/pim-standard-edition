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

namespace Akeneo\ReferenceEntity\Infrastructure\Persistence\Sql\Record\Hydrator\Transformer;

use Akeneo\ReferenceEntity\Domain\Model\Attribute\AbstractAttribute;

/**
 * @author    Laurent Petard <laurent.petard@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class ConnectorValueTransformerRegistry
{
    public function __construct(
        private iterable $ConnectorValueTransformers
    ) {
    }

    public function getTransformer(AbstractAttribute $attribute): ConnectorValueTransformerInterface
    {
        foreach ($this->ConnectorValueTransformers as $ConnectorValueTransformer) {
            if ($ConnectorValueTransformer->supports($attribute)) {
                return $ConnectorValueTransformer;
            }
        }

        throw new \RuntimeException(sprintf('There was no transformer found for attribute %s', $attribute::class));
    }
}
