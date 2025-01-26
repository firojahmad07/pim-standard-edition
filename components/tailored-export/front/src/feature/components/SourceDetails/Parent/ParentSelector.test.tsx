import React from 'react';
import {screen} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import {ValidationError} from '@akeneo-pim-community/shared';
import {ParentSelector} from './ParentSelector';
import {renderWithProviders} from 'feature/tests';

test('it displays a type dropdown when the selection type is code', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <ParentSelector validationErrors={[]} selection={{type: 'code'}} onSelectionChange={onSelectionChange} />
  );

  expect(screen.getByText('pim_common.type')).toBeInTheDocument();
  expect(screen.getByText('pim_common.code')).toBeInTheDocument();
  expect(screen.queryByText('pim_common.locale')).not.toBeInTheDocument();
});

test('it displays a locale dropdown when the selection type is label', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <ParentSelector
      validationErrors={[]}
      selection={{type: 'label', locale: 'en_US', channel: 'ecommerce'}}
      onSelectionChange={onSelectionChange}
    />
  );

  expect(screen.getByText('pim_common.type')).toBeInTheDocument();
  expect(screen.getByText('pim_common.locale')).toBeInTheDocument();

  userEvent.click(screen.getByLabelText('pim_common.locale'));
  userEvent.click(screen.getByText('Français'));

  expect(onSelectionChange).toHaveBeenCalledWith({type: 'label', locale: 'fr_FR', channel: 'ecommerce'});
});

test('it displays a channel dropdown when the selection type is label', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <ParentSelector
      validationErrors={[]}
      selection={{type: 'label', locale: 'en_US', channel: 'ecommerce'}}
      onSelectionChange={onSelectionChange}
    />
  );

  expect(screen.getByText('pim_common.type')).toBeInTheDocument();
  expect(screen.getByText('pim_common.channel')).toBeInTheDocument();

  userEvent.click(screen.getByLabelText('pim_common.channel'));
  userEvent.click(screen.getByText('[print]'));

  expect(onSelectionChange).toHaveBeenCalledWith({type: 'label', locale: 'en_US', channel: 'print'});
});

test('it can select a label selection type', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <ParentSelector validationErrors={[]} selection={{type: 'code'}} onSelectionChange={onSelectionChange} />
  );

  userEvent.click(screen.getByText('pim_common.type'));
  userEvent.click(screen.getByTitle('pim_common.label'));

  expect(onSelectionChange).toHaveBeenCalledWith({type: 'label', locale: 'en_US', channel: 'ecommerce'});
});

test('it can select a code selection type', async () => {
  const onSelectionChange = jest.fn();

  await renderWithProviders(
    <ParentSelector
      validationErrors={[]}
      selection={{type: 'label', locale: 'en_US', channel: 'ecommerce'}}
      onSelectionChange={onSelectionChange}
    />
  );

  userEvent.click(screen.getByText('pim_common.type'));
  userEvent.click(screen.getByTitle('pim_common.code'));

  expect(onSelectionChange).toHaveBeenCalledWith({type: 'code'});
});

test('it displays validation errors', async () => {
  const onSelectionChange = jest.fn();
  const validationErrors: ValidationError[] = [
    {
      messageTemplate: 'error.key.locale',
      invalidValue: '',
      message: 'this is a locale error',
      parameters: {},
      propertyPath: '[locale]',
    },
    {
      messageTemplate: 'error.key.channel',
      invalidValue: '',
      message: 'this is a channel error',
      parameters: {},
      propertyPath: '[channel]',
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
    <ParentSelector
      validationErrors={validationErrors}
      selection={{type: 'label', locale: 'en_US', channel: 'ecommerce'}}
      onSelectionChange={onSelectionChange}
    />
  );

  expect(screen.getByText('error.key.locale')).toBeInTheDocument();
  expect(screen.getByText('error.key.channel')).toBeInTheDocument();
  expect(screen.getByText('error.key.type')).toBeInTheDocument();
});
