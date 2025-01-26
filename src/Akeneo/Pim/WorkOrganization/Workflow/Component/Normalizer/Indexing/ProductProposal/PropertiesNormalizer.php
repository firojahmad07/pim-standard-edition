<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2017 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\WorkOrganization\Workflow\Component\Normalizer\Indexing\ProductProposal;

use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Enrichment\Component\Product\Normalizer\Indexing\Value\ValueCollectionNormalizer;
use Akeneo\Pim\Enrichment\Component\Product\Normalizer\Standard\Product\PropertiesNormalizer as StandardPropertiesNormalizer;
use Akeneo\Pim\WorkOrganization\Workflow\Component\Model\ProductDraft;
use Akeneo\Pim\WorkOrganization\Workflow\Component\Normalizer\Indexing\ProductProposalNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;

/**
 * Transform the properties of a product proposal (aka product draft) object to the indexing format.
 *
 * @author Olivier Soulet <olivier.soulet@akeneo.com>
 */
class PropertiesNormalizer implements NormalizerInterface, SerializerAwareInterface, CacheableSupportsMethodInterface
{
    use SerializerAwareTrait;

    const FIELD_ID = 'id';
    const FIELD_ENTITY_WITH_VALUES_IDENTIFIER = 'entity_with_values_identifier';
    const FIELD_AUTHOR = 'author';
    const FIELD_SOURCE = 'source';

    const PRODUCT_IDENTIFIER_PREFIX = 'product_draft_';

    /**
     * {@inheritdoc}
     */
    public function normalize($productProposal, $format = null, array $context = []): array
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new \LogicException('Serializer must be a normalizer');
        }

        $context['is_workflow'] = true;
        $data = [];

        $data[self::FIELD_ID] = self::PRODUCT_IDENTIFIER_PREFIX. (string) $productProposal->getId();

        $product = $productProposal->getEntityWithValue();
        $data[self::FIELD_ENTITY_WITH_VALUES_IDENTIFIER] = $product->getIdentifier();
        $data[StandardPropertiesNormalizer::FIELD_IDENTIFIER] =  (string) $productProposal->getId();
        $data[StandardPropertiesNormalizer::FIELD_CREATED] = $this->serializer->normalize(
            $productProposal->getCreatedAt(),
            ValueCollectionNormalizer::INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX
        );
        $data[StandardPropertiesNormalizer::FIELD_FAMILY] = null !== $product->getFamily() ? $this->serializer->normalize(
            $product->getFamily(),
            ValueCollectionNormalizer::INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX
        ) : null;
        $data[self::FIELD_AUTHOR] = $productProposal->getAuthor();
        $data[self::FIELD_SOURCE] = $productProposal->getSource();
        $data[StandardPropertiesNormalizer::FIELD_CATEGORIES] = $product->getCategoryCodes();
        $data[StandardPropertiesNormalizer::FIELD_VALUES] = !$productProposal->getValues()->isEmpty()
            ? $this->serializer->normalize(
                $productProposal->getValues(),
                ValueCollectionNormalizer::INDEXING_FORMAT_PRODUCT_AND_MODEL_INDEX,
                $context
            ) : [];

        $attributeAsLabel = null !== $product->getFamily() ? $product->getFamily()->getAttributeAsLabel() : null;
        $attributeAsLabelCode = null !== $attributeAsLabel ? $attributeAsLabel->getCode() : null;

        $productLabel = [];
        if (null !== $attributeAsLabelCode) {
            $labelValue = $product->getValue($attributeAsLabelCode);
            $productLabel = null !== $labelValue
                ? $this->serializer->normalize(
                    $labelValue,
                    $format
                ) : [];
        }

        $data[StandardPropertiesNormalizer::FIELD_LABEL] = $this->getLabel(
            $productLabel,
            $product
        );

        return $data;
    }

    private function getLabel(array $values, ProductInterface $product): array
    {
        if (null === $product->getFamily()) {
            return [];
        }

        $labelAttribute = $product->getFamily()->getAttributeAsLabel();
        if (null === $labelAttribute) {
            return [];
        }

        $valuePath = sprintf('%s-text', $labelAttribute->getCode());
        if (!isset($values[$valuePath])) {
            return [];
        }

        return $values[$valuePath];
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof ProductDraft && ProductProposalNormalizer::INDEXING_FORMAT_PRODUCT_PROPOSAL_INDEX === $format;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
