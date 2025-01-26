<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2020 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\Permission\Component\EntityWithFamilyVariant;

use Akeneo\Pim\Enrichment\Component\Product\EntityWithFamilyVariant\RemoveParentInterface;
use Akeneo\Pim\Enrichment\Component\Product\Model\ProductInterface;
use Akeneo\Pim\Permission\Component\Attributes;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class GrantedRemoveParent implements RemoveParentInterface
{
    /** @var RemoveParentInterface */
    private $removeParent;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    public function __construct(
        RemoveParentInterface $removeParent,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->removeParent = $removeParent;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function from(ProductInterface $product): void
    {
        if (!$this->authorizationChecker->isGranted(Attributes::OWN, $product)) {
            throw new InvalidArgumentException(
                'To be able to convert this variant product you need to have the \'Own\' permission on its categories.'
            );
        }

        $this->removeParent->from($product);
    }
}
