<?php

declare(strict_types=1);

namespace Akeneo\AssetManager\Infrastructure\Connector\Api\Attribute;

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2018 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Akeneo\AssetManager\Domain\Model\AssetFamily\AssetFamilyIdentifier;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeCode;
use Akeneo\AssetManager\Domain\Query\AssetFamily\AssetFamilyExistsInterface;
use Akeneo\AssetManager\Domain\Query\Attribute\Connector\FindConnectorAttributeByIdentifierAndCodeInterface;
use Oro\Bundle\SecurityBundle\SecurityFacade;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class GetConnectorAttributeAction
{
    private FindConnectorAttributeByIdentifierAndCodeInterface $findConnectorAttributeQuery;
    private AssetFamilyExistsInterface $assetFamilyExists;

    private SecurityFacade $securityFacade;

    public function __construct(
        FindConnectorAttributeByIdentifierAndCodeInterface $findConnectorAttributeQuery,
        AssetFamilyExistsInterface $assetFamilyExists,
        SecurityFacade $securityFacade
    ) {
        $this->assetFamilyExists = $assetFamilyExists;
        $this->findConnectorAttributeQuery = $findConnectorAttributeQuery;
        $this->securityFacade = $securityFacade;
    }

    /**
     * @throws UnprocessableEntityHttpException
     * @throws NotFoundHttpException
     */
    public function __invoke(string $code, string $assetFamilyIdentifier): JsonResponse
    {
        $this->denyAccessUnlessAclIsGranted();

        try {
            $assetFamilyIdentifier = AssetFamilyIdentifier::fromString($assetFamilyIdentifier);
            $attributeCode = AttributeCode::fromString($code);
        } catch (\Exception $exception) {
            throw new UnprocessableEntityHttpException($exception->getMessage());
        }

        $assetFamilyExists = $this->assetFamilyExists->withIdentifier($assetFamilyIdentifier);

        if (!$assetFamilyExists) {
            throw new NotFoundHttpException(sprintf('Asset family "%s" does not exist.', $assetFamilyIdentifier));
        }

        $attribute = $this->findConnectorAttributeQuery->find($assetFamilyIdentifier, $attributeCode);

        if (null === $attribute) {
            throw new NotFoundHttpException(sprintf('Attribute "%s" does not exist for the asset family "%s".', $code, $assetFamilyIdentifier));
        }

        $normalizedAttribute = $attribute->normalize();

        return new JsonResponse($normalizedAttribute);
    }

    private function denyAccessUnlessAclIsGranted(): void
    {
        if (!$this->securityFacade->isGranted('pim_api_asset_family_list')) {
            throw new AccessDeniedHttpException('Access forbidden. You are not allowed to list asset families.');
        }
    }
}
