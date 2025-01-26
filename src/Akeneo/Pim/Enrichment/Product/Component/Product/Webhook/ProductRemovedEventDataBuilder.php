<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Product\Component\Product\Webhook;

use Akeneo\Pim\Enrichment\Component\Product\Message\ProductRemoved;
use Akeneo\Pim\Enrichment\Component\Product\Webhook\ProductRemovedEventDataBuilder as BaseProductRemovedEventDataBuilder;
use Akeneo\Pim\Enrichment\Product\Component\Product\Query\GetViewableCategoryCodes;
use Akeneo\Platform\Component\EventQueue\BulkEventInterface;
use Akeneo\Platform\Component\Webhook\Context;
use Akeneo\Platform\Component\Webhook\EventDataBuilderInterface;
use Akeneo\Platform\Component\Webhook\EventDataCollection;

/**
 * @author Willy Mesnage <willy.mesnage@akeneo.com>
 */
class ProductRemovedEventDataBuilder implements EventDataBuilderInterface
{
    private BaseProductRemovedEventDataBuilder $baseProductRemovedEventDateBuilder;
    private GetViewableCategoryCodes $getViewableCategoryCodes;

    public function __construct(
        BaseProductRemovedEventDataBuilder $baseProductRemovedEventDateBuilder,
        GetViewableCategoryCodes $getViewableCategoryCodes
    ) {
        $this->baseProductRemovedEventDateBuilder = $baseProductRemovedEventDateBuilder;
        $this->getViewableCategoryCodes = $getViewableCategoryCodes;
    }

    public function supports(BulkEventInterface $event): bool
    {
        return $this->baseProductRemovedEventDateBuilder->supports($event);
    }

    public function build(BulkEventInterface $event, Context $context): EventDataCollection
    {
        if (false === $this->supports($event)) {
            throw new \InvalidArgumentException();
        }

        $collection = new EventDataCollection();

        /** @var ProductRemoved $productRemovedEvent */
        foreach ($event->getEvents() as $productRemovedEvent) {
            $productCategoryCodes = $productRemovedEvent->getCategoryCodes();
            if (0 < count($productCategoryCodes)) {
                $grantedCategoryCodes = $this->getViewableCategoryCodes->forCategoryCodes(
                    $context->getUserId(),
                    $productCategoryCodes
                );

                if (0 === count($grantedCategoryCodes)) {
                    $collection->setEventDataError(
                        $productRemovedEvent,
                        new NotGrantedProductException($context->getUsername(), $productRemovedEvent->getIdentifier())
                    );

                    continue;
                }
            }

            $data = [
                'resource' => [
                    'identifier' => $productRemovedEvent->getIdentifier(),
                ],
            ];

            $collection->setEventData($productRemovedEvent, $data);
        }

        return $collection;
    }
}
