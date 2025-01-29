<?php

declare(strict_types=1);

namespace Akeneo\Pim\Tailored\Domain\Query;

interface FindProductModelLabelsInterface
{
    /**
     * @param string[] $productModelCodes
     * @return array<string, string>
     */
    public function byCodes(array $productModelCodes, string $channel, string $locale): array;
}
