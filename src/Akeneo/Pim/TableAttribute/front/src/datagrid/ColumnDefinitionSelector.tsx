import React from 'react';
import {getLabel, useTranslate, useUserContext} from '@akeneo-pim-community/shared';
import {SelectInput} from 'akeneo-design-system';
import {ColumnCode, ColumnDefinition} from '../models';
import {useAttributeContext} from '../contexts';

type ColumnDefinitionSelectorProps = {
  onChange: (columnDefinition: ColumnDefinition | undefined) => void;
  value?: ColumnDefinition;
};

const ColumnDefinitionSelector: React.FC<ColumnDefinitionSelectorProps> = ({onChange, value}) => {
  const translate = useTranslate();
  const userContext = useUserContext();
  const catalogLocale = userContext.get('catalogLocale');
  const {attribute} = useAttributeContext();

  const handleChange = (columnDefinitionCode: ColumnCode | null) => {
    attribute &&
      onChange(attribute.table_configuration.find(columnDefinition => columnDefinition.code === columnDefinitionCode));
  };

  return (
    <SelectInput
      clearLabel={translate('pim_common.clear_value')}
      clearable
      emptyResultLabel={translate('pim_common.no_result')}
      onChange={handleChange}
      placeholder={translate('pim_table_attribute.datagrid.select_your_column')}
      value={value?.code || null}
      openLabel={translate('pim_common.open')}
    >
      {(attribute?.table_configuration || []).map(columnDefinition => {
        const label = getLabel(columnDefinition.labels, catalogLocale, columnDefinition.code);
        return (
          <SelectInput.Option title={label} value={columnDefinition.code} key={columnDefinition.code}>
            {label}
          </SelectInput.Option>
        );
      })}
    </SelectInput>
  );
};

export {ColumnDefinitionSelector};
