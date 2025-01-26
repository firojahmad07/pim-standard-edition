import React from 'react';
import {screen} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import {ValidationError} from '@akeneo-pim-community/shared';
import {CodeLabelSelector} from './CodeLabelSelector';
import {renderWithProviders} from 'feature/tests';

test('it displays a type dropdown when the selection type is code', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <CodeLabelSelector selection={{type: 'code'}} validationErrors={[]} onSelectionChange={onSelectionChange} />
  );

  expect(screen.getByText('pim_common.type')).toBeInTheDocument();
  expect(screen.getByText('pim_common.code')).toBeInTheDocument();
  expect(screen.queryByText('pim_common.locale')).not.toBeInTheDocument();
});

test('it displays a locale dropdown when the selection type is label', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <CodeLabelSelector
      selection={{type: 'label', locale: 'en_US'}}
      validationErrors={[]}
      onSelectionChange={onSelectionChange}
    />
  );

  expect(screen.getByText('pim_common.type')).toBeInTheDocument();
  expect(screen.getByText('pim_common.locale')).toBeInTheDocument();

  userEvent.click(screen.getByLabelText('pim_common.locale'));
  userEvent.click(screen.getByText('Français'));

  expect(onSelectionChange).toHaveBeenCalledWith({type: 'label', locale: 'fr_FR'});
});

test('it can select a label selection type', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <CodeLabelSelector selection={{type: 'code'}} validationErrors={[]} onSelectionChange={onSelectionChange} />
  );

  userEvent.click(screen.getByText('pim_common.type'));
  userEvent.click(screen.getByTitle('pim_common.label'));

  expect(onSelectionChange).toHaveBeenCalledWith({type: 'label', locale: 'en_US'});
});

test('it can select a code selection type', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <CodeLabelSelector
      selection={{type: 'label', locale: 'en_US'}}
      validationErrors={[]}
      onSelectionChange={onSelectionChange}
    />
  );

  userEvent.click(screen.getByText('pim_common.type'));
  userEvent.click(screen.getByTitle('pim_common.code'));

  expect(onSelectionChange).toHaveBeenCalledWith({type: 'code'});
});

test('it displays validation errors', async () => {
  const validationErrors: ValidationError[] = [
    {
      messageTemplate: 'error.key.locale',
      invalidValue: '',
      message: 'this is a locale error',
      parameters: {},
      propertyPath: '[locale]',
    },
    {
      messageTemplate: 'error.key.type',
      invalidValue: '',
      message: 'this is a type error',
      parameters: {},
      propertyPath: '[type]',
    },
  ];

  await renderWithProviders(
    <CodeLabelSelector
      validationErrors={validationErrors}
      selection={{type: 'label', locale: 'en_US'}}
      onSelectionChange={jest.fn()}
    />
  );

  expect(screen.getByText('error.key.locale')).toBeInTheDocument();
  expect(screen.getByText('error.key.type')).toBeInTheDocument();
  expect(screen.getByRole('alert')).toBeInTheDocument();
});
