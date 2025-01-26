import React from 'react';
import 'jest-fetch-mock';
import {createAttribute, locales, scopes} from '../../../../factories';
import {Operator} from '../../../../../../src/models/Operator';
import {FileAttributeConditionLine} from '../../../../../../src/pages/EditRules/components/conditions/FileAttributeConditionLine';
import {
  renderWithProviders,
  screen,
  waitForElementToBeRemoved,
  act,
} from '../../../../../../test-utils';
import userEvent from '@testing-library/user-event';

describe('FileAttributeConditionLine', () => {
  beforeEach(() => {
    fetchMock.resetMocks();
  });

  it('should display the locale and scope selectors', async () => {
    fetchMock.mockResponses([
      JSON.stringify(
        createAttribute({
          localizable: true,
          scopable: true,
          code: 'localizableScopableFile',
          type: 'pim_catalog_file',
        })
      ),
      {status: 200},
    ]);

    const defaultValues = {
      content: {
        conditions: [
          {
            operator: Operator.NOT_EQUAL,
            scope: 'mobile',
            value: 'mobile_us_file.pdf',
            locale: 'en_US',
          },
        ],
      },
    };

    const toRegister = [
      {name: 'content.conditions[0].value', type: 'custom'},
      {name: 'content.conditions[0].operator', type: 'custom'},
      {name: 'content.conditions[0].locale', type: 'custom'},
      {name: 'content.conditions[0].scope', type: 'custom'},
    ];

    renderWithProviders(
      <FileAttributeConditionLine
        condition={{
          field: 'localizableScopableFile',
          operator: Operator.NOT_EQUAL,
        }}
        lineNumber={0}
        locales={locales}
        scopes={scopes}
        currentCatalogLocale={'fr_FR'}
      />,
      {all: true},
      {defaultValues, toRegister}
    );

    await waitForElementToBeRemoved(() => document.querySelector('img')).then(
      () => {
        expect(screen.getByText('Nom')).toBeInTheDocument();
        const operatorSelector = screen.getByTestId(
          'edit-rules-input-0-operator'
        );
        expect(operatorSelector).toBeInTheDocument();
        expect(operatorSelector).toHaveValue('!=');
        expect(
          screen.getByTestId('edit-rules-input-0-scope')
        ).toBeInTheDocument();
        expect(screen.getByTestId('edit-rules-input-0-scope')).toHaveValue(
          'mobile'
        );
        expect(
          screen.getByTestId('edit-rules-input-0-locale')
        ).toBeInTheDocument();
        expect(screen.getByTestId('edit-rules-input-0-locale')).toHaveValue(
          'en_US'
        );
      }
    );
  });

  it('should not display the locale and scope selectors', async () => {
    fetchMock.mockResponses([
      JSON.stringify(
        createAttribute({
          localizable: false,
          scopable: false,
          code: 'localizableScopableFile',
          type: 'pim_catalog_file',
        })
      ),
      {status: 200},
    ]);
    const defaultValues = {
      content: {
        conditions: [
          {
            operator: Operator.NOT_EQUAL,
            value: 'file.pdf',
          },
        ],
      },
    };

    const toRegister = [
      {name: 'content.conditions[0].value', type: 'custom'},
      {name: 'content.conditions[0].operator', type: 'custom'},
    ];
    renderWithProviders(
      <FileAttributeConditionLine
        condition={{
          field: 'conditionWithNonLocalizableScopableFile',
          operator: Operator.NOT_EQUAL,
        }}
        lineNumber={1}
        locales={locales}
        scopes={scopes}
        currentCatalogLocale={'fr_FR'}
      />,
      {all: true},
      {defaultValues, toRegister}
    );
    await waitForElementToBeRemoved(() => document.querySelector('img')).then(
      () => {
        expect(screen.getByText('Nom')).toBeInTheDocument();
        const operatorSelector = screen.getByTestId(
          'edit-rules-input-1-operator'
        );
        expect(operatorSelector).toBeInTheDocument();
        expect(screen.queryByTestId('edit-rules-input-1-scope')).toBeNull();
        expect(screen.queryByTestId('edit-rules-input-1-locale')).toBeNull();
      }
    );
  });

  it('handles values option appearance based on selected operator', async () => {
    fetchMock.mockResponses([
      JSON.stringify(
        createAttribute({
          localizable: false,
          scopable: false,
          code: 'localizableScopableFile',
          type: 'pim_catalog_file',
        })
      ),
      {status: 200},
    ]);

    renderWithProviders(
      <FileAttributeConditionLine
        condition={{
          field: 'localizableScopableFile',
          operator: Operator.NOT_EQUAL,
        }}
        lineNumber={1}
        locales={locales}
        scopes={scopes}
        currentCatalogLocale={'en_US'}
      />,
      {all: true}
    );
    await waitForElementToBeRemoved(() => document.querySelector('img')).then(
      () => {
        expect(screen.getByText('Name')).toBeInTheDocument();
        const operatorSelector = screen.getByTestId(
          'edit-rules-input-1-operator'
        );
        expect(operatorSelector).toBeInTheDocument();
        expect(screen.getByTestId('edit-rules-input-1-value')).toBeDefined();
        act(() => {
          userEvent.selectOptions(operatorSelector, Operator.IS_NOT_EMPTY);
        });
        expect(screen.queryByTestId('edit-rules-input-1-value')).toBeNull();
        act(() => {
          userEvent.selectOptions(operatorSelector, Operator.NOT_EQUAL);
        });
        expect(screen.getByTestId('edit-rules-input-1-value')).toBeDefined();
      }
    );
  });

  it('displays the matching locales regarding the scope', async () => {
    fetchMock.mockResponses([
      JSON.stringify(
        createAttribute({
          localizable: true,
          scopable: true,
          code: 'localizableScopableFile',
          type: 'pim_catalog_file',
        })
      ),
      {status: 200},
    ]);
    renderWithProviders(
      <FileAttributeConditionLine
        condition={{
          field: 'localizableScopableFile',
          operator: Operator.NOT_EQUAL,
        }}
        lineNumber={1}
        locales={locales}
        scopes={scopes}
        currentCatalogLocale={'en_US'}
      />,
      {all: true}
    );
    await waitForElementToBeRemoved(() => document.querySelector('img')).then(
      () => {
        expect(screen.getByText('Name')).toBeInTheDocument();
        const operatorSelector = screen.getByTestId(
          'edit-rules-input-1-operator'
        );
        expect(operatorSelector).toBeInTheDocument();
        expect(screen.getByTestId('edit-rules-input-1-value')).toBeDefined();
        userEvent.selectOptions(
          screen.getByTestId('edit-rules-input-1-scope'),
          'ecommerce'
        );
        expect(screen.getByText('German')).toBeInTheDocument();
        expect(screen.getByText('French')).toBeInTheDocument();
        expect(screen.getByText('English')).toBeInTheDocument();
        userEvent.selectOptions(
          screen.getByTestId('edit-rules-input-1-scope'),
          'mobile'
        );
        expect(screen.getByText('German')).toBeInTheDocument();
        expect(screen.queryByText('French')).not.toBeInTheDocument();
        expect(screen.getByText('English')).toBeInTheDocument();
      }
    );
  });
});
