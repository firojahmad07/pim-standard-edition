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

namespace Akeneo\Pim\Enrichment\AssetManager\Component\Normalizer;

use Akeneo\AssetManager\Domain\Model\Asset\AssetCode;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class AssetCodeNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * {@inheritdoc}
     *
     * @param AssetCode $assetCode
     */
    public function normalize($assetCode, $format = null, array $context = [])
    {
        if (key_exists('field_name', $context)) {
            return [$context['field_name'] => (string) $assetCode];
        }

        return $assetCode->normalize();
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AssetCode && ('standard' === $format || 'storage' === $format || 'flat' === $format);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
