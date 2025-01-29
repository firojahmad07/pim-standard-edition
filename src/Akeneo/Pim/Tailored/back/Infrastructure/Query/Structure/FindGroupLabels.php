<?php

declare(strict_types=1);

namespace Akeneo\Pim\Tailored\Infrastructure\Query\Structure;

use Akeneo\Pim\Structure\Component\Query\PublicApi\Group\GetGroupTranslations;
use Akeneo\Pim\Tailored\Domain\Query\FindGroupLabelsInterface;

class FindGroupLabels implements FindGroupLabelsInterface
{
    private GetGroupTranslations $getGroupTranslations;

    public function __construct(GetGroupTranslations $getGroupTranslations)
    {
        $this->getGroupTranslations = $getGroupTranslations;
    }

    /**
     * @inheritDoc
     */
    public function byCodes(array $groupCodes, string $locale): array
    {
        return $this->getGroupTranslations->byGroupCodesAndLocale($groupCodes, $locale);
    }
}
