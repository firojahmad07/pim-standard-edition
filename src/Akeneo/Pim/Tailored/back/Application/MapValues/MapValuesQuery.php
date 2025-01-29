<?php

declare(strict_types=1);

namespace Akeneo\Pim\Tailored\Application\MapValues;

use Akeneo\Pim\Tailored\Application\Common\Column\ColumnCollection;
use Akeneo\Pim\Tailored\Application\Common\ValueCollection;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2021 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MapValuesQuery
{
    private ColumnCollection $columnCollection;
    private ValueCollection $valueCollection;

    public function __construct(ColumnCollection $columnCollection, ValueCollection $valueCollection)
    {
        $this->columnCollection = $columnCollection;
        $this->valueCollection = $valueCollection;
    }

    public function getColumnCollection(): ColumnCollection
    {
        return $this->columnCollection;
    }

    public function getValueCollection(): ValueCollection
    {
        return $this->valueCollection;
    }
}
