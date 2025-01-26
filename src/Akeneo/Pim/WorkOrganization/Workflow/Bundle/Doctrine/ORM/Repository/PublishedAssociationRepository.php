<?php

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2014 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\WorkOrganization\Workflow\Bundle\Doctrine\ORM\Repository;

use Akeneo\Pim\WorkOrganization\Workflow\Component\Model\PublishedProductAssociation;
use Akeneo\Pim\WorkOrganization\Workflow\Component\Model\PublishedProductInterface;
use Akeneo\Pim\WorkOrganization\Workflow\Component\Repository\PublishedAssociationRepositoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Published association repository
 *
 * @author Julien Janvier <julien.janvier@akeneo.com>
 */
class PublishedAssociationRepository extends EntityRepository implements PublishedAssociationRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findOneByTypeAndOwner(int $associationTypeId, $ownerId): ?PublishedProductAssociation
    {
        return $this->findOneBy(
            [
                'owner'           => $ownerId,
                'associationType' => ['id' => $associationTypeId],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removePublishedProduct(PublishedProductInterface $published, $nbAssociationTypes = null)
    {
        $qb = $this->createQueryBuilder('pp');
        $rootEntity = current($qb->getRootEntities());
        $productsMapping = $this->_em->getClassMetadata($rootEntity)->getAssociationMapping('products');

        // DELETE FROM pimee_workflow_published_product_association_published_product WHERE  product_id = ?
        $sql = sprintf(
            "DELETE p FROM %s p WHERE %s = %d",
            $productsMapping['joinTable']['name'],
            $productsMapping['joinTable']['inverseJoinColumns'][0]['name'],
            $published->getId()
        );

        $this->_em->getConnection()->prepare($sql)->execute();
    }
}
