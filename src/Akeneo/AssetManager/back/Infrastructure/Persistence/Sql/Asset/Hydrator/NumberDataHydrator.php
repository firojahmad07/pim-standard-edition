<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2019 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\AssetManager\Infrastructure\Persistence\Sql\Asset\Hydrator;

use Akeneo\AssetManager\Domain\Model\Asset\Value\NumberData;
use Akeneo\AssetManager\Domain\Model\Asset\Value\ValueDataInterface;
use Akeneo\AssetManager\Domain\Model\Attribute\AbstractAttribute;
use Akeneo\AssetManager\Domain\Model\Attribute\NumberAttribute;

/**
 * @author    Adrien Pétremann <adrien.petremann@akeneo.com>
 * @copyright 2019 Akeneo SAS (https://www.akeneo.com)
 */
class NumberDataHydrator implements DataHydratorInterface
{
    public function supports(AbstractAttribute $attribute): bool
    {
        return $attribute instanceof NumberAttribute;
    }

    public function hydrate($normalizedData, AbstractAttribute $attribute): ValueDataInterface
    {
        return NumberData::createFromNormalize($normalizedData);
    }
}
