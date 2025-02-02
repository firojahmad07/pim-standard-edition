<?php

declare(strict_types=1);

namespace Akeneo\Pim\Enrichment\Bundle\EventSubscriber\EntityWithAssociations;

use Akeneo\Pim\Enrichment\Component\Product\Model\AssociationInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\EntityWithAssociationsInterface;
use Akeneo\Pim\Enrichment\Component\Product\Storage\Indexer\ProductIndexerInterface;
use Akeneo\Pim\Enrichment\Component\Product\Storage\Indexer\ProductModelIndexerInterface;
use Akeneo\Tool\Component\StorageUtils\StorageEvents;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PersistTwoWayAssociationSubscriber implements EventSubscriberInterface
{
    private ManagerRegistry $registry;
    private ProductIndexerInterface $productIndexer;
    private ProductModelIndexerInterface $productModelIndexer;
    private array $productIdentifiersToIndex = [];
    private array $productModelCodesToIndex = [];

    public function __construct(
        ManagerRegistry $registry,
        ProductIndexerInterface $productIndexer,
        ProductModelIndexerInterface $productModelIndexer
    ) {
        $this->registry = $registry;
        $this->productIndexer = $productIndexer;
        $this->productModelIndexer = $productModelIndexer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StorageEvents::PRE_SAVE => 'handlePreSave',
            StorageEvents::POST_SAVE => 'indexAssociatedEntities',
        ];
    }

    public function handlePreSave(GenericEvent $event): void
    {
        $entity = $event->getSubject();

        if (!$entity instanceof EntityWithAssociationsInterface) {
            return;
        }

        $em = $this->registry->getManager();

        /** @var AssociationInterface $association */
        foreach ($entity->getAssociations() as $association) {
            $associationType = $association->getAssociationType();

            if (!$associationType->isTwoWay()) {
                continue;
            }

            foreach ($association->getProducts() as $product) {
                $em->persist($product);
                $this->productIdentifiersToIndex[] = $product->getIdentifier();
            }

            foreach ($association->getProductModels() as $productModel) {
                $em->persist($productModel);
                $this->productModelCodesToIndex[] = $productModel->getCode();
            }
        }
    }

    public function indexAssociatedEntities()
    {
        $this->productIndexer->indexFromProductIdentifiers($this->productIdentifiersToIndex);
        $this->productModelIndexer->indexFromProductModelCodes($this->productModelCodesToIndex);

        $this->productIdentifiersToIndex = [];
        $this->productModelCodesToIndex = [];
    }
}
