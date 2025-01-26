import {
  ConcreteMediaFileAttribute,
  isMediaFileAttribute,
} from 'akeneoassetmanager/domain/model/attribute/type/media-file';

const normalizedFrontView = {
  identifier: 'front_view',
  asset_family_identifier: 'designer',
  code: 'front_view',
  labels: {en_US: 'Front view'},
  type: 'media_file',
  order: 0,
  value_per_locale: true,
  value_per_channel: false,
  is_required: true,
  is_read_only: true,
  max_file_size: null,
  allowed_extensions: [],
};

describe('akeneo > attribute > domain > model > attribute > type --- MediaFileAttribute', () => {
  test('I can create a ConcreteMediaFileAttribute from normalized', () => {
    expect(ConcreteMediaFileAttribute.createFromNormalized(normalizedFrontView).normalize()).toEqual(
      normalizedFrontView
    );
  });

  test('I can check if it is a media-file attribute', () => {
    expect(isMediaFileAttribute(normalizedFrontView)).toBe(true);
    expect(isMediaFileAttribute({...normalizedFrontView, type: 'noice'})).toBe(false);
  });
});
