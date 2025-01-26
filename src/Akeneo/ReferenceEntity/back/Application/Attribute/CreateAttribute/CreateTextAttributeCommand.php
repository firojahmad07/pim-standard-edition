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

namespace Akeneo\ReferenceEntity\Application\Attribute\CreateAttribute;

/**
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class CreateTextAttributeCommand extends AbstractCreateAttributeCommand
{
    /** @var int */
    public $maxLength;

    /** @var bool */
    public $isTextarea;

    /** @var bool */
    public $isRichTextEditor;

    /** @var string|null */
    public $validationRule;

    /** @var string|null */
    public $regularExpression;

    public function __construct(
        string $referenceEntityIdentifier,
        string $code,
        array $labels,
        bool $isRequired,
        bool $valuePerChannel,
        bool $valuePerLocale,
        ?int $maxLength,
        bool $isTextarea,
        bool $isRichTextEditor,
        ?string $validationRule,
        ?string $regularExpression
    ) {
        parent::__construct(
            $referenceEntityIdentifier,
            $code,
            $labels,
            $isRequired,
            $valuePerChannel,
            $valuePerLocale
        );

        $this->maxLength = $maxLength;
        $this->isTextarea = $isTextarea;
        $this->isRichTextEditor = $isRichTextEditor;
        $this->validationRule = $validationRule;
        $this->regularExpression = $regularExpression;
    }
}
