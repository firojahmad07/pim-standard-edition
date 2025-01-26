<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2020 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\Automation\RuleEngine\Component\Model;

use Akeneo\Tool\Bundle\RuleEngineBundle\Model\ActionInterface;

interface ProductCalculateActionInterface extends ActionInterface
{
    public function getDestination(): ProductTarget;

    public function getSource(): Operand;

    public function getOperationList(): OperationList;

    public function isRoundEnabled(): bool;

    public function getRoundPrecision(): ?int;
}
