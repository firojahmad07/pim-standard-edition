import {ConcreteOptionAttribute, isOptionAttribute} from 'akeneoassetmanager/domain/model/attribute/type/option';
import {createOptionFromNormalized} from 'akeneoassetmanager/domain/model/attribute/type/option/option';

const normalizedFavoriteColor = {
  identifier: 'favorite_color',
  asset_family_identifier: 'designer',
  code: 'favorite_color',
  labels: {en_US: 'Favorite color'},
  type: 'option',
  order: 0,
  value_per_locale: true,
  value_per_channel: false,
  is_required: true,
  is_read_only: true,
  options: [
    {
      code: 'red',
      labels: {en_US: 'Red'},
    },
    {
      code: 'green',
      labels: {en_US: 'Green'},
    },
  ],
};

describe('akeneo > attribute > domain > model > attribute > type --- OptionAttribute', () => {
  test('I can create a ConcreteOptionAttribute from normalized', () => {
    expect(ConcreteOptionAttribute.createFromNormalized(normalizedFavoriteColor).normalize()).toEqual(
      normalizedFavoriteColor
    );
  });

  test('I can set options', () => {
    const newOption = createOptionFromNormalized({code: 'new_option', labels: {}});
    const optionAttribute = ConcreteOptionAttribute.createFromNormalized(normalizedFavoriteColor).setOptions([
      newOption,
    ]);
    expect(optionAttribute.normalize()).toEqual({
      ...normalizedFavoriteColor,
      options: [{code: 'new_option', labels: {}}],
    });
  });

  test('I get the options', () => {
    const options = ConcreteOptionAttribute.createFromNormalized(normalizedFavoriteColor).getOptions();
    expect(options[0]).toEqual({
      code: 'red',
      labels: {en_US: 'Red'},
    });
    expect(options[1]).toEqual({
      code: 'green',
      labels: {en_US: 'Green'},
    });
  });

  test('I can check if it is a option attribute', () => {
    expect(isOptionAttribute(normalizedFavoriteColor)).toBe(true);
    expect(isOptionAttribute({...normalizedFavoriteColor, type: 'noice'})).toBe(false);
  });

  test('I can test if it has an option', () => {
    const option = ConcreteOptionAttribute.createFromNormalized(normalizedFavoriteColor);
    expect(option.hasOption('red')).toEqual(true);
    expect(option.hasOption('unknown')).toEqual(false);
  });
});
