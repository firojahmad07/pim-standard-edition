<?php

declare(strict_types=1);

namespace Akeneo\ReferenceEntity\Domain\Model\Record\Value;

use Akeneo\ReferenceEntity\Domain\Model\ChannelIdentifier;

/**
 * A ChannelReference expresses a link to a channel.
 *
 * If there is one, then the channel reference it is wrapping a ChannelIdentifier
 * If it has no link then it is null
 *
 * @see ChannelIdentifier
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2018 Akeneo SAS (https://www.akeneo.com)
 */
class ChannelReference
{
    private function __construct(
        private ?ChannelIdentifier $identifier
    ) {
    }

    public static function fromChannelIdentifier(ChannelIdentifier $identifier): self
    {
        return new self($identifier);
    }

    public static function noReference(): self
    {
        return new self(null);
    }

    public static function createFromNormalized(?string $normalizedReference): self
    {
        if (null === $normalizedReference) {
            return ChannelReference::noReference();
        }

        return self::fromChannelIdentifier(ChannelIdentifier::fromCode($normalizedReference));
    }

    public function equals(ChannelReference $channelReference): bool
    {
        if ($channelReference->isEmpty() && $this->isEmpty()) {
            return true;
        }
        if ($channelReference->isEmpty() !== $this->isEmpty()) {
            return false;
        }

        return $this->identifier->equals($channelReference->identifier);
    }

    public function getIdentifier(): ChannelIdentifier
    {
        return $this->identifier;
    }

    public function normalize(): ?string
    {
        if (!$this->identifier instanceof ChannelIdentifier) {
            return null;
        }

        return $this->identifier->normalize();
    }

    public function isEmpty(): bool
    {
        return !$this->identifier instanceof ChannelIdentifier;
    }
}
