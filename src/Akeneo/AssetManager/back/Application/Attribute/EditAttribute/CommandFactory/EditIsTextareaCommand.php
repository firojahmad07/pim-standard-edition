<?php

declare(strict_types=1);

namespace Akeneo\AssetManager\Application\Attribute\EditAttribute\CommandFactory;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class EditIsTextareaCommand extends AbstractEditAttributeCommand
{
    public ?bool $isTextarea = null;

    public function __construct(string $identifier, ?bool $isTextarea)
    {
        parent::__construct($identifier);

        $this->isTextarea = $isTextarea;
    }
}
