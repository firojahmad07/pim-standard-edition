<?php

namespace Akeneo\Pim\Structure\Component\AttributeType;

use Akeneo\Pim\Structure\Component\AttributeTypes;

/**
 * Text attribute type
 *
 * @author    Filips Alpe <filips@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class TextType extends AbstractAttributeType
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return AttributeTypes::TEXT;
    }
}
