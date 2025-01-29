<?php

declare(strict_types=1);

namespace Akeneo\Pim\Tailored\Infrastructure\Hydrator;

use Akeneo\Pim\Tailored\Application\Common\Format\ElementCollection;
use Akeneo\Pim\Tailored\Application\Common\Format\SourceElement;
use Akeneo\Pim\Tailored\Application\Common\Format\TextElement;

class ElementCollectionHydrator
{
    public function hydrate(
        array $normalizedElements
    ): ElementCollection {
        $elements = array_map(static function (array $element) {
            if ('text'  === $element['type']) {
                return new TextElement($element['value']);
            } elseif ('source' === $element['type']) {
                return new SourceElement($element['value']);
            } else {
                throw new \InvalidArgumentException(sprintf('Unsupported element type "%s"', $element['type']));
            }
        }, $normalizedElements);

        return ElementCollection::create($elements);
    }
}
