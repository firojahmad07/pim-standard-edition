<?php

namespace spec\Akeneo\AssetManager\Application\Attribute\EditAttribute\AttributeUpdater;

use Akeneo\AssetManager\Application\Attribute\EditAttribute\AttributeUpdater\OptionsUpdater;
use Akeneo\AssetManager\Application\Attribute\EditAttribute\CommandFactory\EditLabelsCommand;
use Akeneo\AssetManager\Application\Attribute\EditAttribute\CommandFactory\EditOptionsCommand;
use Akeneo\AssetManager\Domain\Model\Attribute\MediaFileAttribute;
use Akeneo\AssetManager\Domain\Model\Attribute\OptionAttribute;
use Akeneo\AssetManager\Domain\Model\Attribute\OptionCollectionAttribute;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class OptionsUpdaterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(OptionsUpdater::class);
    }

    function it_only_supports_edit_options_for_option_collection_attribute_and_option_attribute(
        OptionCollectionAttribute $optionCollectionAttribute,
        OptionAttribute $optionAttribute,
        MediaFileAttribute $mediaFileAttribute
    ) {
        $optionsEditCommand = new EditOptionsCommand(
            'color',
            []
        );
        $labelEditCommand = new EditLabelsCommand(
            'name',
            []
        );

        $this->supports($optionCollectionAttribute, $optionsEditCommand)->shouldReturn(true);
        $this->supports($optionAttribute, $optionsEditCommand)->shouldReturn(true);
        $this->supports($mediaFileAttribute, $optionsEditCommand)->shouldReturn(false);
        $this->supports($optionCollectionAttribute, $labelEditCommand)->shouldReturn(false);
    }

    function it_edits_the_options_of_option_attribute(OptionAttribute $optionAttribute)
    {
        $optionsEditCommand = new EditOptionsCommand(
            'color',
            [
                [
                    'code'   => 'green',
                    'labels' => ['en_US' => 'Green'],
                ],
                [
                    'code'   => 'red',
                    'labels' => ['en_US' => 'Red'],
                ]
            ]
        );

        $optionAttribute->setOptions(Argument::that(function ($collaborator) {
            $expectedGreen = json_encode(['code' => 'green', 'labels' => ['en_US' => 'Green']]);
            $expectedRed = json_encode(['code' => 'red', 'labels' => ['en_US' => 'Red']]);
            $actualGreen = json_encode($collaborator[0]->normalize());
            $actualRed = json_encode($collaborator[1]->normalize());

            return $expectedGreen === $actualGreen && $expectedRed === $actualRed;
        }))->shouldBeCalled();

        $this->__invoke($optionAttribute, $optionsEditCommand)->shouldReturn($optionAttribute);
    }

    function it_empties_the_options(OptionAttribute $optionAttribute)
    {
        $editMaxLength = new EditOptionsCommand('color', []);
        $optionAttribute->setOptions([])->shouldBeCalled();
        $this->__invoke(
            $optionAttribute,
            $editMaxLength
        )->shouldReturn($optionAttribute);
    }

    function it_throws_if_it_cannot_update_the_attribute(
        OptionAttribute $rightAttribute1,
        OptionCollectionAttribute $rightAttribute2,
        MediaFileAttribute $wrongAttribute
    ) {
        $rightCommand = new EditOptionsCommand('color', []);
        $wrongCommand = new EditLabelsCommand(
            'name',
            []
        );
        $this->shouldThrow(\RuntimeException::class)->during('__invoke', [$rightAttribute1, $wrongCommand]);
        $this->shouldThrow(\RuntimeException::class)->during('__invoke', [$rightAttribute2, $wrongCommand]);
        $this->shouldThrow(\RuntimeException::class)->during('__invoke', [$wrongAttribute, $rightCommand]);
        $this->shouldThrow(\RuntimeException::class)->during('__invoke', [$wrongAttribute, $wrongCommand]);
    }
}
