import React from 'react';
import {screen, fireEvent, within} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import {ColumnList} from './ColumnList';
import {ValidationErrorsContext} from '../../contexts';
import {ColumnConfiguration, ColumnsState} from '../../models/ColumnConfiguration';
import {renderWithProviders} from 'feature/tests';

// @skip: unstable test.
// test('it renders a placeholder when no column is selected', async () => {
//   const columnsConfiguration: ColumnConfiguration[] = [
//     {
//       uuid: '1',
//       target: 'my first column',
//       sources: [],
//       format: {
//         type: 'concat',
//         elements: [],
//         space_between: true,
//       },
//     },
//     {
//       uuid: '2',
//       target: 'my second column',
//       sources: [],
//       format: {
//         type: 'concat',
//         elements: [],
//         space_between: true,
//       },
//     },
//   ];
//
//   await renderWithProviders(
//     <ColumnList
//       onColumnReorder={jest.fn()}
//       columnsState={{columns: columnsConfiguration, selectedColumnUuid: null}}
//       onColumnChange={jest.fn()}
//       onColumnCreated={jest.fn()}
//       onColumnsCreated={jest.fn()}
//       onColumnRemoved={jest.fn()}
//       onColumnSelected={jest.fn()}
//       setColumnsState={jest.fn()}
//     />
//   );
//
//   expect(screen.getByDisplayValue(/my first column/i)).toBeInTheDocument();
//   expect(screen.getByDisplayValue(/my second column/i)).toBeInTheDocument();
//
//   expect(screen.getAllByText(/akeneo.tailored_export.column_list.column_row.no_source/i)).toHaveLength(2);
//
//   const firstInput = screen.getAllByPlaceholderText(
//     'akeneo.tailored_export.column_list.column_row.target_placeholder'
//   )[2];
//   expect(firstInput).toHaveFocus();
// });

test('it can remove a column', async () => {
  const columnsConfiguration: ColumnConfiguration[] = [
    {
      uuid: '1',
      target: 'my column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
  ];

  const handleRemove = jest.fn();

  await renderWithProviders(
    <ColumnList
      onColumnReorder={jest.fn()}
      columnsState={{columns: columnsConfiguration, selectedColumnUuid: null}}
      onColumnChange={jest.fn()}
      onColumnCreated={jest.fn()}
      onColumnsCreated={jest.fn()}
      onColumnRemoved={handleRemove}
      onColumnSelected={jest.fn()}
      setColumnsState={jest.fn()}
    />
  );

  fireEvent.click(screen.getByTitle('akeneo.tailored_export.column_list.column_row.remove'));
  fireEvent.click(screen.getByText('pim_common.confirm'));

  expect(handleRemove).toBeCalled();
});

test('it can create a new column', async () => {
  const columnsConfiguration: ColumnConfiguration[] = [
    {
      uuid: '1',
      target: 'my column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
  ];

  const handleCreate = jest.fn();

  await renderWithProviders(
    <ColumnList
      onColumnReorder={jest.fn()}
      columnsState={{columns: columnsConfiguration, selectedColumnUuid: null}}
      onColumnChange={jest.fn()}
      onColumnCreated={handleCreate}
      onColumnsCreated={handleCreate}
      onColumnRemoved={jest.fn()}
      onColumnSelected={jest.fn()}
      setColumnsState={jest.fn()}
    />
  );

  const lastInput = screen.getAllByPlaceholderText(
    'akeneo.tailored_export.column_list.column_row.target_placeholder'
  )[1];
  userEvent.type(lastInput, 'm');

  expect(handleCreate).toBeCalledWith('m');
});

test('it can handle paste events', async () => {
  const columnsConfiguration: ColumnConfiguration[] = [
    {
      uuid: '1',
      target: 'my column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
  ];

  const handleCreate = jest.fn();

  await renderWithProviders(
    <ColumnList
      onColumnReorder={jest.fn()}
      columnsState={{columns: columnsConfiguration, selectedColumnUuid: null}}
      onColumnChange={jest.fn()}
      onColumnCreated={handleCreate}
      onColumnsCreated={handleCreate}
      onColumnRemoved={jest.fn()}
      onColumnSelected={jest.fn()}
      setColumnsState={jest.fn()}
    />
  );

  const lastInput = screen.getAllByPlaceholderText(
    'akeneo.tailored_export.column_list.column_row.target_placeholder'
  )[1];
  const clipboardEvent = new Event('paste', {
    bubbles: true,
    cancelable: true,
    composed: true,
  });
  clipboardEvent['clipboardData'] = {
    getData: () => 'test\tof\tpasted\tdata',
  };
  lastInput.dispatchEvent(clipboardEvent);

  expect(handleCreate).toBeCalledWith(['test', 'of', 'pasted', 'data']);
});

test('it can update a column', async () => {
  const columnsConfiguration: ColumnConfiguration[] = [
    {
      uuid: '1',
      target: 'my column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
  ];

  const handleColumnChange = jest.fn();

  await renderWithProviders(
    <ColumnList
      onColumnReorder={jest.fn()}
      columnsState={{columns: columnsConfiguration, selectedColumnUuid: null}}
      onColumnChange={handleColumnChange}
      onColumnCreated={jest.fn()}
      onColumnsCreated={jest.fn()}
      onColumnRemoved={jest.fn()}
      onColumnSelected={jest.fn()}
      setColumnsState={jest.fn()}
    />
  );

  const firstInput = screen.getAllByPlaceholderText(
    'akeneo.tailored_export.column_list.column_row.target_placeholder'
  )[0];
  fireEvent.change(firstInput, {target: {value: 'my new column name'}});

  expect(handleColumnChange).toBeCalledWith({
    uuid: '1',
    target: 'my new column name',
    sources: [],
    format: {
      type: 'concat',
      elements: [],
      space_between: true,
    },
  });
});

test('it displays validation errors', async () => {
  const columnsConfiguration: ColumnConfiguration[] = [
    {
      uuid: '1',
      target: 'my column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
  ];

  const handleColumnChange = jest.fn();

  await renderWithProviders(
    <ValidationErrorsContext.Provider
      value={[
        {
          messageTemplate: 'akeneo.tailored_export.validation.columns.target.max_length_reached',
          parameters: {
            '{{ value }}': 'way too long',
            '{{ limit }}': 255,
          },
          message: 'akeneo.tailored_export.validation.columns.target.max_length_reached',
          propertyPath: '[columns][1][target]',
          invalidValue: 'way too long',
        },
        {
          messageTemplate: 'akeneo.tailored_export.validation.columns.max_column_count',
          parameters: {},
          message: 'akeneo.tailored_export.validation.columns.max_column_count',
          propertyPath: '[columns]',
          invalidValue: '',
        },
      ]}
    >
      <ColumnList
        onColumnReorder={jest.fn()}
        columnsState={{columns: columnsConfiguration, selectedColumnUuid: null}}
        onColumnChange={handleColumnChange}
        onColumnCreated={jest.fn()}
        onColumnsCreated={jest.fn()}
        onColumnRemoved={jest.fn()}
        onColumnSelected={jest.fn()}
        setColumnsState={jest.fn()}
      />
    </ValidationErrorsContext.Provider>
  );

  const validationError = screen.getByText('akeneo.tailored_export.validation.columns.target.max_length_reached');
  expect(validationError).toBeInTheDocument();

  const globalError = screen.getByText('akeneo.tailored_export.validation.columns.max_column_count');
  expect(globalError).toBeInTheDocument();
});

test('it moves to next line when user type enter', async () => {
  const columnsConfiguration: ColumnConfiguration[] = [
    {
      uuid: '1',
      target: 'my column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
    {
      uuid: '2',
      target: 'another column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
  ];

  const handleColumnSelected = jest.fn();
  const handleSetColumnsState = jest.fn(
    (dispatch: (columnsState: ColumnsState) => ColumnsState): void =>
      void dispatch({columns: columnsConfiguration, selectedColumnUuid: columnsConfiguration[0].uuid})
  );

  await renderWithProviders(
    <ColumnList
      onColumnReorder={jest.fn()}
      columnsState={{columns: columnsConfiguration, selectedColumnUuid: columnsConfiguration[0].uuid}}
      onColumnChange={jest.fn()}
      onColumnCreated={jest.fn()}
      onColumnsCreated={jest.fn()}
      onColumnRemoved={jest.fn()}
      onColumnSelected={handleColumnSelected}
      setColumnsState={handleSetColumnsState}
    />
  );

  const firstInput = screen.getByDisplayValue('my column');
  userEvent.type(firstInput, '{enter}');

  expect(handleColumnSelected).toHaveBeenCalledWith('1');
  expect(handleSetColumnsState).toHaveBeenCalled();
});

test('it focuses the selected column', async () => {
  jest.useFakeTimers();

  const columnsConfiguration: ColumnConfiguration[] = [
    {
      uuid: '1',
      target: 'my column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
    {
      uuid: '2',
      target: 'another column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
  ];

  await renderWithProviders(
    <ColumnList
      onColumnReorder={jest.fn()}
      columnsState={{columns: columnsConfiguration, selectedColumnUuid: columnsConfiguration[0].uuid}}
      onColumnChange={jest.fn()}
      onColumnCreated={jest.fn()}
      onColumnsCreated={jest.fn()}
      onColumnRemoved={jest.fn()}
      onColumnSelected={jest.fn()}
      setColumnsState={jest.fn()}
    />
  );

  jest.runAllTimers();

  const firstInput = screen.getAllByPlaceholderText(
    'akeneo.tailored_export.column_list.column_row.target_placeholder'
  )[0];

  expect(firstInput).toHaveFocus();
});

test('it displays the sources labels on the row', async () => {
  const columnsConfiguration: ColumnConfiguration[] = [
    {
      uuid: '1',
      target: 'my column',
      sources: [
        {
          uuid: '1234',
          code: 'name',
          type: 'attribute',
          locale: null,
          channel: null,
          operations: [],
          selection: {type: 'code'},
        },
        {
          uuid: '1235',
          code: 'parent',
          type: 'property',
          locale: null,
          channel: null,
          operations: {},
          selection: {type: 'code'},
        },
        {
          uuid: '31d80b71-b169-4275-81a3-c788690a5470',
          code: 'XSELL',
          type: 'association_type',
          locale: null,
          channel: null,
          operations: {},
          selection: {type: 'code', separator: ',', entity_type: 'products'},
        },
        {
          uuid: '296d487e-294b-4e42-a8d0-08e66f78a84d',
          code: 'UPSELL',
          type: 'association_type',
          locale: null,
          channel: null,
          operations: {},
          selection: {type: 'code', separator: ',', entity_type: 'products'},
        },
      ],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
    {
      uuid: '2',
      target: 'another column',
      sources: [],
      format: {
        type: 'concat',
        elements: [],
        space_between: true,
      },
    },
  ];

  await renderWithProviders(
    <ColumnList
      onColumnReorder={jest.fn()}
      columnsState={{columns: columnsConfiguration, selectedColumnUuid: columnsConfiguration[0].uuid}}
      onColumnChange={jest.fn()}
      onColumnCreated={jest.fn()}
      onColumnsCreated={jest.fn()}
      onColumnRemoved={jest.fn()}
      onColumnSelected={jest.fn()}
      setColumnsState={jest.fn()}
    />
  );

  const [, firstRow, secondRow] = screen.getAllByRole('row');

  expect(within(firstRow).getByText('English name, pim_common.parent, Cross sell, [UPSELL]')).toBeInTheDocument();
  expect(within(secondRow).getByText('akeneo.tailored_export.column_list.column_row.no_source')).toBeInTheDocument();
});
