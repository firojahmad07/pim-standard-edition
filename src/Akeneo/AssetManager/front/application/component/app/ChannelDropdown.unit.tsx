import React from 'react';
import {screen} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import {renderWithProviders} from '@akeneo-pim-community/legacy-bridge/tests/front/unit/utils';
import Channel from 'akeneoassetmanager/domain/model/channel';
import {ChannelDropdown} from './ChannelDropdown';

const channels: Channel[] = [
  {
    code: 'ecommerce',
    locales: [
      {
        code: 'en_US',
        label: 'English (United States)',
        region: 'United States',
        language: 'English',
      },
      {
        code: 'fr_FR',
        label: 'French (France)',
        region: 'France',
        language: 'French',
      },
    ],
    labels: {
      en_US: 'Ecommerce',
      de_DE: 'Ecommerce',
      fr_FR: 'Ecommerce',
    },
  },
  {
    code: 'mobile',
    locales: [
      {
        code: 'de_DE',
        label: 'German (Germany)',
        region: 'Germany',
        language: 'German',
      },
      {
        code: 'en_US',
        label: 'English (United States)',
        region: 'United States',
        language: 'English',
      },
    ],
    labels: {
      en_US: 'Mobile',
      de_DE: 'Mobil',
      fr_FR: 'Mobile',
    },
  },
  {
    code: 'print',
    locales: [
      {
        code: 'de_DE',
        label: 'German (Germany)',
        region: 'Germany',
        language: 'German',
      },
      {
        code: 'en_US',
        label: 'English (United States)',
        region: 'United States',
        language: 'English',
      },
      {
        code: 'fr_FR',
        label: 'French (France)',
        region: 'France',
        language: 'French',
      },
    ],
    labels: {
      en_US: 'Print',
      de_DE: 'Drucken',
      fr_FR: 'Impression',
    },
  },
];

test('it renders its children properly', () => {
  renderWithProviders(
    <ChannelDropdown uiLocale="en_US" channels={channels} channel="ecommerce" onChange={jest.fn()} />
  );

  expect(screen.getByText('Ecommerce')).toBeInTheDocument();
});

test('it displays all channels when opening the dropdown', async () => {
  const handleOnChange = jest.fn();
  renderWithProviders(
    <ChannelDropdown uiLocale="en_US" channels={channels} channel="ecommerce" onChange={handleOnChange} />
  );

  userEvent.click(screen.getByRole('textbox'));

  expect(await screen.findByText('Mobile')).toBeInTheDocument();
  expect(screen.getByText('Print')).toBeInTheDocument();
  expect(screen.getAllByText('Ecommerce')).toHaveLength(2);
});

test('it does not display the dropdown when read only', () => {
  const handleOnChange = jest.fn();
  renderWithProviders(
    <ChannelDropdown
      readOnly={true}
      uiLocale="en_US"
      channels={channels}
      channel="ecommerce"
      onChange={handleOnChange}
    />
  );

  userEvent.click(screen.getByRole('textbox'));

  expect(screen.queryByText('Mobile')).not.toBeInTheDocument();
  expect(screen.queryByText('Print')).not.toBeInTheDocument();
});

test('it calls onChange handler when clicking on another channel', async () => {
  const handleOnChange = jest.fn();
  renderWithProviders(
    <ChannelDropdown uiLocale="en_US" channels={channels} channel="ecommerce" onChange={handleOnChange} />
  );

  userEvent.click(screen.getByRole('textbox'));
  userEvent.click(await screen.findByText('Mobile'));

  expect(handleOnChange).toHaveBeenCalledWith('mobile');
});
