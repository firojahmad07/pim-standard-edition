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

namespace spec\Akeneo\AssetManager\Infrastructure\Connector\Api\Attribute\JsonSchema\Edit;

use Akeneo\AssetManager\Domain\Model\AssetFamily\AssetFamilyIdentifier;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeCode;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeIdentifier;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeIsReadOnly;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeIsRequired;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeMaxLength;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeOrder;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeRegularExpression;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeValidationRule;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeValuePerChannel;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeValuePerLocale;
use Akeneo\AssetManager\Domain\Model\Attribute\TextAttribute;
use Akeneo\AssetManager\Domain\Model\LabelCollection;
use Akeneo\AssetManager\Infrastructure\Connector\Api\Attribute\JsonSchema\Edit\AttributeValidatorInterface;
use Akeneo\AssetManager\Infrastructure\Connector\Api\Attribute\JsonSchema\Edit\TextAttributeValidator;
use PhpSpec\ObjectBehavior;

class TextAttributeValidatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TextAttributeValidator::class);
    }

    function it_is_an_attribute_validator()
    {
        $this->shouldImplement(AttributeValidatorInterface::class);
    }

    function it_is_json_schema_of_text_attribute()
    {
        $attribute = TextAttribute::createText(
            AttributeIdentifier::fromString('foo'),
            AssetFamilyIdentifier::fromString('foo'),
            AttributeCode::fromString('main_color'),
            LabelCollection::fromArray(['en_US' => 'Main color', 'fr_FR' => 'Couleur principale']),
            AttributeOrder::fromInteger(0),
            AttributeIsRequired::fromBoolean(true),
            AttributeIsReadOnly::fromBoolean(false),
            AttributeValuePerChannel::fromBoolean(false),
            AttributeValuePerLocale::fromBoolean(true),
            AttributeMaxLength::fromInteger(155),
            AttributeValidationRule::fromString(AttributeValidationRule::REGULAR_EXPRESSION),
            AttributeRegularExpression::fromString('/\w+/')
        );
        $this->support($attribute)->shouldReturn(true);
    }

    function it_does_not_return_any_error_when_the_attribute_is_valid()
    {
        $attribute = [
            'code' => 'name',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
            'labels' => [
                'en_US' => 'Philippe Starck'
            ],
            'is_required_for_completeness' => false,
            'is_textarea' => false,
            'max_characters' => 12,
            'validation_rule' => 'regular_expression',
            'validation_regexp' => 'foo',
            'is_rich_text_editor' => true,
            '_links' => [
                'self' => [
                    'href' => 'http://localhost/api/rest/v1/asset-families/designer/attributes/name'
                ]
            ],
        ];

        $this->validate($attribute)->shouldReturn([]);
    }

    function it_does_not_return_any_error_when_no_labels()
    {
        $attribute = [
            'code' => 'name',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
            'labels' => [],
            'is_required_for_completeness' => false,
            'is_textarea' => false,
            'max_characters' => 12,
            'validation_rule' => 'regular_expression',
            'validation_regexp' => 'foo',
            'is_rich_text_editor' => true,
            '_links' => [
                'self' => [
                    'href' => 'http://localhost/api/rest/v1/asset-families/designer/attributes/name'
                ]
            ],
        ];

        $this->validate($attribute)->shouldReturn([]);
    }

    function it_does_not_return_any_error_when_only_the_code_is_provided()
    {
        $attribute = [
            'code' => 'starck',
        ];

        $this->validate($attribute)->shouldReturn([]);
    }

    function it_is_mandatory_to_provide_the_code_of_the_attribute()
    {
        $attribute = [
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_an_additional_property_is_filled()
    {
        $attribute = [
            'unknown_property' => 'michel',
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_code_is_not_a_string()
    {
        $attribute = [
            'code' => 1,
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_type_is_not_a_string()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 1,
            'value_per_channel' => true,
            'value_per_locale' => true,
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_channel_is_not_a_boolean()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => 'foo',
            'value_per_locale' => true,
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_locale_is_not_a_boolean()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => false,
            'value_per_locale' => 'foo',
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_labels_has_a_wrong_format()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => false,
            'value_per_locale' => false,
            'labels' => [
                'en_US' => []
            ]
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_is_required_for_completeness_is_not_a_boolean()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
            'is_required_for_completeness' => 'foo',
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_is_rich_text_editor_is_not_a_boolean()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
            'is_rich_text_editor' => 'foo',
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_is_textarea_is_not_a_boolean()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
            'is_textarea' => 'foo',
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_max_characters_is_not_a_number()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
            'max_characters' => 'foo',
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_validation_rule_is_not_a_string()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
            'validation_rule' => 1,
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }

    function it_returns_an_error_when_validation_regexp_is_not_a_string()
    {
        $attribute = [
            'code' => 'starck',
            'type' => 'text',
            'value_per_channel' => true,
            'value_per_locale' => true,
            'validation_regexp' => 1,
        ];

        $errors = $this->validate($attribute);
        $errors->shouldBeArray();
        $errors->shouldHaveCount(1);
    }
}
