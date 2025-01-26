<?php

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2014 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\WorkOrganization\ProductRevert;

use Akeneo\Pim\Enrichment\Bundle\DependencyInjection\Compiler\RegisterSerializerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Enterprise revert bundle
 *
 * @author Nicolas Dupont <nicolas@akeneo.com>
 */
class AkeneoPimProductRevertBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
    }
}
