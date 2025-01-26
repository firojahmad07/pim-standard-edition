<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2018 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\ReferenceEntity\Integration\PublicApi\Analytics\AverageMaxPercentageOfAttributesPerReferenceEntity;

use Akeneo\ReferenceEntity\Domain\Model\Image;
use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntity;
use Akeneo\ReferenceEntity\Domain\Model\ReferenceEntity\ReferenceEntityIdentifier;
use Akeneo\ReferenceEntity\Infrastructure\PublicApi\Analytics\AverageMaxPercentageOfAttributesPerReferenceEntity\SqlLocalizableOnly;
use Akeneo\ReferenceEntity\Integration\SqlIntegrationTestCase;
use Ramsey\Uuid\Uuid;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class SqlLocalizableOnlyTest extends SqlIntegrationTestCase
{
    /** @var SqlLocalizableOnly */
    private $averageMaxPercentageOfLocalizableOnlyAttributessPerReferenceEntity;

    /** @var CreateAttributesHelper */
    private $createAttributesHelper;

    public function setUp(): void
    {
        parent::setUp();

        $this->averageMaxPercentageOfLocalizableOnlyAttributessPerReferenceEntity = $this->get('akeneo_referenceentity.infrastructure.persistence.query.analytics.average_max_percentage_of_attributes_per_reference_entity.localizable_only');
        $this->createAttributesHelper = new CreateAttributesHelper($this->get('akeneo_referenceentity.infrastructure.persistence.repository.attribute'));
        $this->resetDB();
    }

    private function resetDB(): void
    {
        $this->get('akeneoreference_entity.tests.helper.database_helper')->resetDatabase();
    }

    /**
     * @test
     */
    public function it_returns_the_average_and_max_percentage_of_localizable_only_attributes_per_reference_entity()
    {
        $referenceEntityIdentifier = $this->createReferenceEntity();
        $this->createAttributesHelper->loadLocalizableOnlyAttributesForReferenceEntity($referenceEntityIdentifier, 4);
        $this->createAttributesHelper->loadLocalizableAndScopableAttributesForReferenceEntity(
            $referenceEntityIdentifier,
            10
        );
        $this->createAttributesHelper->loadScopableOnlyAttributesForReferenceEntity($referenceEntityIdentifier, 10);
        $this->createAttributesHelper->loadNotLocalizableNotScopableAttributesForReferenceEntity(
            $referenceEntityIdentifier,
            10
        );

        $anotherReferenceEntityIdentifier = $this->createReferenceEntity();
        $this->createAttributesHelper->loadLocalizableOnlyAttributesForReferenceEntity(
            $anotherReferenceEntityIdentifier,
            2
        );
        $this->createAttributesHelper->loadLocalizableAndScopableAttributesForReferenceEntity(
            $anotherReferenceEntityIdentifier,
            10
        );
        $this->createAttributesHelper->loadScopableOnlyAttributesForReferenceEntity(
            $anotherReferenceEntityIdentifier,
            10
        );
        $this->createAttributesHelper->loadNotLocalizableNotScopableAttributesForReferenceEntity(
            $anotherReferenceEntityIdentifier,
            10
        );

        $volume = $this->averageMaxPercentageOfLocalizableOnlyAttributessPerReferenceEntity->fetch();

        $this->assertEquals('11', $volume->getMaxVolume());
        $this->assertEquals('10', $volume->getAverageVolume());
    }

    private function createReferenceEntity(): ReferenceEntityIdentifier
    {
        $referenceEntityRepository = $this->get('akeneo_referenceentity.infrastructure.persistence.repository.reference_entity');
        $referenceEntityIdentifier = $this->getRandomIdentifier();
        $referenceEntityRepository->create(ReferenceEntity::create(
            $referenceEntityIdentifier,
            [],
            Image::createEmpty()
        ));

        return $referenceEntityIdentifier;
    }

    private function getRandomIdentifier(): ReferenceEntityIdentifier
    {
        return ReferenceEntityIdentifier::fromString(str_replace('-', '_', Uuid::uuid4()->toString()));
    }
}
