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

namespace Akeneo\Pim\Tailored\Application\MapValues\OperationApplier;

use Akeneo\Pim\Tailored\Application\Common\Operation\DefaultValueOperation;
use Akeneo\Pim\Tailored\Application\Common\Operation\OperationInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\NullValue;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\SourceValueInterface;
use Akeneo\Pim\Tailored\Application\Common\SourceValue\StringValue;

class DefaultValueOperationApplier implements OperationApplierInterface
{
    public function applyOperation(
        OperationInterface $operation,
        SourceValueInterface $value
    ): SourceValueInterface {
        if (
            !$operation instanceof DefaultValueOperation
            || !$value instanceof NullValue
        ) {
            throw new \InvalidArgumentException('Cannot apply Default value operation');
        }

        return new StringValue($operation->getDefaultValue());
    }

    public function supports(OperationInterface $operation, SourceValueInterface $value): bool
    {
        return $value instanceof NullValue && $operation instanceof DefaultValueOperation;
    }
}
