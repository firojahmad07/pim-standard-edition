import {Channel} from '../../../../../vendor/akeneo/pim-community-dev/front-packages/shared/lib';
import {Attribute} from './Attribute';
import {
  createColumn,
  addColumn,
  removeColumn,
  updateColumn,
  addAttributeSource,
  addPropertySource,
  updateSource,
  removeSource,
  addAssociationTypeSource,
  filterEmptyOperations,
  filterColumns,
} from './ColumnConfiguration';
import {Source} from './Source';
import {AssociationType} from './AssociationType';

const channels: Channel[] = [
  {
    category_tree: '',
    conversion_units: [],
    currencies: [],
    meta: {
      created: '',
      form: '',
      id: 1,
      updated: '',
    },
    code: 'ecommerce',
    locales: [
      {
        code: 'en_US',
        label: 'English (United States)',
        region: 'US',
        language: 'en',
      },
      {
        code: 'fr_FR',
        label: 'French (France)',
        region: 'FR',
        language: 'fr',
      },
    ],
    labels: {
      fr_FR: 'Ecommerce',
    },
  },
  {
    category_tree: '',
    conversion_units: [],
    currencies: [],
    meta: {
      created: '',
      form: '',
      id: 1,
      updated: '',
    },
    code: 'mobile',
    locales: [
      {
        code: 'de_DE',
        label: 'German (Germany)',
        region: 'DE',
        language: 'de',
      },
      {
        code: 'en_US',
        label: 'English (United States)',
        region: 'US',
        language: 'en',
      },
    ],
    labels: {
      fr_FR: 'Mobile',
    },
  },
  {
    category_tree: '',
    conversion_units: [],
    currencies: [],
    meta: {
      created: '',
      form: '',
      id: 1,
      updated: '',
    },
    code: 'print',
    locales: [
      {
        code: 'de_DE',
        label: 'German (Germany)',
        region: 'DE',
        language: 'de',
      },
      {
        code: 'en_US',
        label: 'English (United States)',
        region: 'US',
        language: 'en',
      },
      {
        code: 'fr_FR',
        label: 'French (France)',
        region: 'FR',
        language: 'fr',
      },
    ],
    labels: {
      fr_FR: 'Impression',
    },
  },
];

const attribute: Attribute = {
  type: 'pim_catalog_text',
  code: 'name',
  labels: {
    fr_FR: 'Nom',
  },
  scopable: true,
  localizable: true,
  is_locale_specific: false,
  available_locales: [],
};

test('it creates a column', () => {
  expect(createColumn('Identifier', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df')).toEqual({
    format: {
      elements: [],
      type: 'concat',
      space_between: true,
    },
    sources: [],
    target: 'Identifier',
    uuid: 'fbf9cff9-e95c-4e7d-983b-2947c7df90df',
  });

  expect(() => {
    createColumn('Identifier', 'invalid_uuid');
  }).toThrowError('Column configuration creation requires a valid uuid: "invalid_uuid"');
});

test('it appends a column', () => {
  const existingColumn = createColumn('The first column', 'abf9cff9-e95c-4e7d-983b-2947c7df90df');
  const columnToAdd = createColumn('Identifier', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  expect(addColumn([], columnToAdd)).toEqual([columnToAdd]);
  expect(addColumn([existingColumn], columnToAdd)).toEqual([existingColumn, columnToAdd]);
});

test('it removes a column', () => {
  const existingColumn = createColumn('The first column', 'abf9cff9-e95c-4e7d-983b-2947c7df90df');
  const columnToRemove = createColumn('Identifier', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  expect(removeColumn([], 'abf9cff9-e95c-4e7d-983b-2947c7df90df')).toEqual([]);
  expect(removeColumn([existingColumn, columnToRemove], 'fbf9cff9-e95c-4e7d-983b-2947c7df90df')).toEqual([
    existingColumn,
  ]);
});

test('it updates a column', () => {
  const existingColumn = createColumn('The first column', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  const anotherColumn = createColumn('Another', 'abf9cff9-e95c-4e7d-983b-2947c7df90df');
  const columnToUpdate = createColumn('Identifier', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  expect(updateColumn([], columnToUpdate)).toEqual([]);
  expect(updateColumn([existingColumn], columnToUpdate)).toEqual([columnToUpdate]);
  expect(updateColumn([anotherColumn, existingColumn], columnToUpdate)).toEqual([anotherColumn, columnToUpdate]);
});

test('it adds attribute source', () => {
  const columnConfiguration = createColumn('The first column', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  const newColumnConfiguration = addAttributeSource(columnConfiguration, attribute, channels);
  const firstSourceUuid = newColumnConfiguration.sources[0].uuid;

  expect(newColumnConfiguration).toEqual({
    uuid: columnConfiguration.uuid,
    target: 'The first column',
    sources: [
      {
        uuid: firstSourceUuid,
        type: 'attribute',
        code: 'name',
        channel: 'ecommerce',
        locale: 'en_US',
        operations: {},
        selection: {
          type: 'code',
        },
      },
    ],
    format: {
      type: 'concat',
      elements: [
        {
          type: 'source',
          uuid: firstSourceUuid,
          value: firstSourceUuid,
        },
      ],
      space_between: true,
    },
  });
});

test('it adds a locale specific attribute source', () => {
  const columnConfiguration = createColumn('The first column', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  const localeSpecificAttribute: Attribute = {
    type: 'pim_catalog_text',
    code: 'name',
    labels: {
      fr_FR: 'Nom',
    },
    scopable: true,
    localizable: true,
    is_locale_specific: true,
    available_locales: ['fr_FR'],
  };

  const newColumnConfiguration = addAttributeSource(columnConfiguration, localeSpecificAttribute, channels);
  const firstSourceUuid = newColumnConfiguration.sources[0].uuid;

  expect(newColumnConfiguration).toEqual({
    uuid: columnConfiguration.uuid,
    target: 'The first column',
    sources: [
      {
        uuid: firstSourceUuid,
        type: 'attribute',
        code: 'name',
        channel: 'ecommerce',
        locale: 'fr_FR',
        operations: {},
        selection: {
          type: 'code',
        },
      },
    ],
    format: {
      type: 'concat',
      elements: [
        {
          type: 'source',
          uuid: firstSourceUuid,
          value: firstSourceUuid,
        },
      ],
      space_between: true,
    },
  });
});

test('it adds property source', () => {
  const columnConfiguration = createColumn('The first column', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  const newColumnConfiguration = addPropertySource(columnConfiguration, 'categories');
  const firstSourceUuid = newColumnConfiguration.sources[0].uuid;

  expect(newColumnConfiguration).toEqual({
    uuid: columnConfiguration.uuid,
    target: 'The first column',
    sources: [
      {
        uuid: firstSourceUuid,
        type: 'property',
        code: 'categories',
        channel: null,
        locale: null,
        operations: {},
        selection: {
          type: 'code',
          separator: ',',
        },
      },
    ],
    format: {
      type: 'concat',
      elements: [
        {
          type: 'source',
          uuid: firstSourceUuid,
          value: firstSourceUuid,
        },
      ],
      space_between: true,
    },
  });
});

test('it adds association type source', () => {
  const columnConfiguration = createColumn('The first column', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  const associationType: AssociationType = {
    code: 'UPSELL',
    labels: {},
    is_quantified: false,
  };

  const newColumnConfiguration = addAssociationTypeSource(columnConfiguration, associationType);
  const firstSourceUuid = newColumnConfiguration.sources[0].uuid;
  expect(newColumnConfiguration).toEqual({
    uuid: columnConfiguration.uuid,
    target: 'The first column',
    sources: [
      {
        uuid: firstSourceUuid,
        type: 'association_type',
        code: 'UPSELL',
        channel: null,
        locale: null,
        operations: {},
        selection: {
          type: 'code',
          entity_type: 'products',
          separator: ',',
        },
      },
    ],
    format: {
      type: 'concat',
      elements: [
        {
          type: 'source',
          uuid: firstSourceUuid,
          value: firstSourceUuid,
        },
      ],
      space_between: true,
    },
  });
});

test('it does nothing when update an nonexistent source', () => {
  const columnConfiguration = createColumn('The first column', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  const updatedSource: Source = {
    uuid: 'abf9cff9-e95c-4e7d-983b-2947c7df90df',
    type: 'attribute',
    code: 'description',
    channel: null,
    locale: null,
    operations: {},
    selection: {
      type: 'code',
      separator: ',',
    },
  };

  const updatedConfiguration = updateSource(columnConfiguration, updatedSource);

  expect(updatedConfiguration).toEqual(columnConfiguration);
});

test('it updates a source', () => {
  const columnConfiguration = createColumn('The first column', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  const columnConfigurationWithSource = addAttributeSource(columnConfiguration, attribute, channels);
  const firstSourceUuid = columnConfigurationWithSource.sources[0].uuid;
  const updatedSource: Source = {
    uuid: firstSourceUuid,
    type: 'attribute',
    code: 'name',
    channel: 'mobile',
    locale: 'fr_FR',
    operations: {},
    selection: {
      type: 'code',
    },
  };

  const updatedConfiguration = updateSource(columnConfigurationWithSource, updatedSource);

  expect(updatedConfiguration).toEqual({
    uuid: columnConfiguration.uuid,
    target: 'The first column',
    sources: [
      {
        uuid: firstSourceUuid,
        type: 'attribute',
        code: 'name',
        channel: 'mobile',
        locale: 'fr_FR',
        operations: {},
        selection: {
          type: 'code',
        },
      },
    ],
    format: {
      type: 'concat',
      elements: [
        {
          type: 'source',
          uuid: firstSourceUuid,
          value: firstSourceUuid,
        },
      ],
      space_between: true,
    },
  });
});

test('it removes a source', () => {
  const columnConfiguration = createColumn('The first column', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df');
  const columnConfigurationWithSource = addAttributeSource(columnConfiguration, attribute, channels);

  const updatedConfiguration = removeSource(columnConfigurationWithSource, columnConfigurationWithSource.sources[0]);

  expect(updatedConfiguration).toEqual({
    uuid: columnConfiguration.uuid,
    target: 'The first column',
    sources: [],
    format: {
      type: 'concat',
      elements: [],
      space_between: true,
    },
  });
});

test('it filters empty operations', () => {
  const operations = {
    replacement: {
      type: 'replacement',
      mapping: {
        true: 'vrai',
        false: 'faux',
      },
    },
    empty: undefined,
    another: {not: 'empty'},
  };

  expect(filterEmptyOperations(operations)).toEqual({
    replacement: {
      type: 'replacement',
      mapping: {
        true: 'vrai',
        false: 'faux',
      },
    },
    another: {not: 'empty'},
  });
});

test('it filters columns based on a search value', () => {
  const columns = [
    createColumn('FIRST', 'fbf9cff9-e95c-4e7d-983b-2947c7df90df'),
    createColumn('first', 'fbf9cff9-e95c-4e7d-983b-2947c7df90de'),
    createColumn('fir', 'fbf9cff9-e95c-4e7d-983b-2947c7df90dd'),
  ];

  expect(filterColumns(columns, '')).toHaveLength(3);
  expect(filterColumns(columns, 'fir')).toHaveLength(3);
  expect(filterColumns(columns, 'FIR')).toHaveLength(3);
  expect(filterColumns(columns, 'ir')).toHaveLength(3);
  expect(filterColumns(columns, 'st')).toHaveLength(2);
  expect(filterColumns(columns, 'first')).toHaveLength(2);
  expect(filterColumns(columns, 'FIRST')).toHaveLength(2);
  expect(filterColumns(columns, 'firsttt')).toHaveLength(0);
});
