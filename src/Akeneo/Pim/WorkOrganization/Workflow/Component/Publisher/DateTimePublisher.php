<?php

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2014 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Akeneo\Pim\WorkOrganization\Workflow\Component\Publisher;

/**
 * Datetime publisher
 *
 * @author Nicolas Dupont <nicolas@akeneo.com>
 */
class DateTimePublisher implements PublisherInterface
{
    /**
     * {@inheritdoc}
     */
    public function publish($object, array $options = [])
    {
        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return $object instanceof \DateTime;
    }
}
