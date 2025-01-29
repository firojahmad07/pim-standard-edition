<?php

declare(strict_types=1);

namespace Akeneo\Pim\Tailored\Infrastructure\Query\Enrichment;

use Akeneo\Pim\Enrichment\Component\Product\Query\GetProductModelLabelsInterface;
use Akeneo\Pim\Tailored\Domain\Query\FindProductModelLabelsInterface;

class FindProductModelLabels implements FindProductModelLabelsInterface
{
    private GetProductModelLabelsInterface $getProductModelLabels;

    public function __construct(GetProductModelLabelsInterface $getProductModelLabels)
    {
        $this->getProductModelLabels = $getProductModelLabels;
    }

    /**
     * @inheritDoc
     */
    public function byCodes(array $productModelCodes, string $channel, string $locale): array
    {
        return $this->getProductModelLabels->byCodesAndLocaleAndScope($productModelCodes, $locale, $channel);
    }
}
