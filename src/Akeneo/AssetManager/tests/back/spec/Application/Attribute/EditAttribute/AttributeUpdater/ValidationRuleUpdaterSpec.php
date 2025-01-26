<?php

namespace spec\Akeneo\AssetManager\Application\Attribute\EditAttribute\AttributeUpdater;

use Akeneo\AssetManager\Application\Attribute\EditAttribute\AttributeUpdater\ValidationRuleUpdater;
use Akeneo\AssetManager\Application\Attribute\EditAttribute\CommandFactory\EditLabelsCommand;
use Akeneo\AssetManager\Application\Attribute\EditAttribute\CommandFactory\EditValidationRuleCommand;
use Akeneo\AssetManager\Domain\Model\Attribute\AttributeValidationRule;
use Akeneo\AssetManager\Domain\Model\Attribute\MediaFileAttribute;
use Akeneo\AssetManager\Domain\Model\Attribute\TextAttribute;
use PhpSpec\ObjectBehavior;

class ValidationRuleUpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ValidationRuleUpdater::class);
    }

    function it_only_supports_edit_validation_rule_command_for_text_attributes(
        TextAttribute $textAttribute,
        MediaFileAttribute $mediaFileAttribute
    ) {
        $labelEditCommand = new EditLabelsCommand('name', []);
        $isRequiredEditCommand = new EditValidationRuleCommand('name', 'email');

        $this->supports($textAttribute, $isRequiredEditCommand)->shouldReturn(true);
        $this->supports($mediaFileAttribute, $isRequiredEditCommand)->shouldReturn(false);
        $this->supports($textAttribute, $labelEditCommand)->shouldReturn(false);
    }

    function it_edits_the_validation_rule_of_a_text_attribute(TextAttribute $textAttribute)
    {
        $editRequired = new EditValidationRuleCommand('name', 'email');
        $textAttribute->setValidationRule(AttributeValidationRule::fromString('email'))->shouldBeCalled();
        $this->__invoke($textAttribute, $editRequired)->shouldReturn($textAttribute);
    }

    function it_sets_the_validation_rule_to_none(TextAttribute $textAttribute)
    {
        $editRequired = new EditValidationRuleCommand('name', null);
        $textAttribute->setValidationRule(AttributeValidationRule::none())->shouldBeCalled();
        $this->__invoke($textAttribute, $editRequired)->shouldReturn($textAttribute);
    }

    function it_throws_if_it_cannot_update_the_attribute(TextAttribute $textAttribute)
    {
        $wrongCommand = new EditLabelsCommand('name', []);
        $this->shouldThrow(\RuntimeException::class)->during('__invoke', [$textAttribute, $wrongCommand]);
    }
}
