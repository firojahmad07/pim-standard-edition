<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Product\Component\Product\Webhook;

use Akeneo\Pim\Enrichment\Component\Product\Message\ProductModelRemoved;
use Akeneo\Pim\Enrichment\Component\Product\Webhook\ProductModelRemovedEventDataBuilder as BaseProductModelRemovedEventDataBuilder;
use Akeneo\Pim\Enrichment\Product\Component\Product\Query\GetViewableCategoryCodes;
use Akeneo\Platform\Component\EventQueue\BulkEventInterface;
use Akeneo\Platform\Component\Webhook\Context;
use Akeneo\Platform\Component\Webhook\EventDataBuilderInterface;
use Akeneo\Platform\Component\Webhook\EventDataCollection;

/**
 * @author    Thomas Galvaing <thomas.galvaing@akeneo.com>
 * @copyright 2020 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ProductModelRemovedEventDataBuilder implements EventDataBuilderInterface
{
    private BaseProductModelRemovedEventDataBuilder $baseProductModelRemovedEventDataBuilder;
    private GetViewableCategoryCodes $getViewableCategoryCodes;

    public function __construct(
        BaseProductModelRemovedEventDataBuilder $baseProductModelRemovedEventDataBuilder,
        GetViewableCategoryCodes $getViewableCategoryCodes
    ) {
        $this->baseProductModelRemovedEventDataBuilder = $baseProductModelRemovedEventDataBuilder;
        $this->getViewableCategoryCodes = $getViewableCategoryCodes;
    }

    public function supports(BulkEventInterface $event): bool
    {
        return $this->baseProductModelRemovedEventDataBuilder->supports($event);
    }

    public function build(BulkEventInterface $event, Context $context): EventDataCollection
    {
        if (false === $this->supports($event)) {
            throw new \InvalidArgumentException();
        }

        $collection = new EventDataCollection();

        /** @var ProductModelRemoved $productModelRemovedEvent */
        foreach ($event->getEvents() as $productModelRemovedEvent) {
            $productModelCategoryCodes = $productModelRemovedEvent->getCategoryCodes();
            if (0 < count($productModelCategoryCodes)) {
                $grantedCategoryCodes = $this->getViewableCategoryCodes->forCategoryCodes(
                    $context->getUserId(),
                    $productModelCategoryCodes
                );

                if (0 === count($grantedCategoryCodes)) {
                    $collection->setEventDataError(
                        $productModelRemovedEvent,
                        new NotGrantedProductModelException(
                            $context->getUsername(),
                            $productModelRemovedEvent->getCode()
                        )
                    );

                    continue;
                }
            }

            $data = [
                'resource' => [
                    'code' => $productModelRemovedEvent->getCode(),
                ],
            ];

            $collection->setEventData($productModelRemovedEvent, $data);
        }

        return $collection;
    }
}
