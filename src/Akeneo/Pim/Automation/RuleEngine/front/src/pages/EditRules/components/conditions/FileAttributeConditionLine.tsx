import React from 'react';
import {Controller} from 'react-hook-form';
import {ConditionLineProps} from './ConditionLineProps';
import {
  FileAttributeCondition,
  Attribute,
  FileAttributeOperators,
} from '../../../../models';
import {AttributeConditionLine} from './AttributeConditionLine';
import {
  useBackboneRouter,
  useTranslate,
} from '../../../../dependenciesTools/hooks';
import {useControlledFormInputCondition} from '../../hooks';
import {Operator} from '../../../../models/Operator';
import {InputText} from '../../../../components';
import {useGetAttributeAtMount} from '../actions/attribute/attribute.utils';

type FileAttributeConditionLineProps = ConditionLineProps & {
  condition: FileAttributeCondition;
};

const FileAttributeConditionLine: React.FC<FileAttributeConditionLineProps> = ({
  condition,
  lineNumber,
  locales,
  scopes,
  currentCatalogLocale,
}) => {
  const router = useBackboneRouter();
  const translate = useTranslate();

  const {
    valueFormName,
    getValueFormValue,
    isFormFieldInError,
  } = useControlledFormInputCondition<string[]>(lineNumber);

  const [attribute, setAttribute] = React.useState<Attribute | null>();

  useGetAttributeAtMount(condition.field, router, attribute, setAttribute);

  return (
    <AttributeConditionLine
      attribute={attribute}
      availableOperators={FileAttributeOperators}
      currentCatalogLocale={currentCatalogLocale}
      defaultOperator={Operator.CONTAINS}
      field={condition.field}
      lineNumber={lineNumber}
      locales={locales}
      scopes={scopes}>
      <Controller
        as={InputText}
        className={
          isFormFieldInError('value')
            ? 'AknTextField AknTextField--error'
            : undefined
        }
        data-testid={`edit-rules-input-${lineNumber}-value`}
        name={valueFormName}
        label={translate('pimee_catalog_rule.rule.value')}
        hiddenLabel
        defaultValue={getValueFormValue()}
        rules={{
          required: translate('pimee_catalog_rule.exceptions.required'),
        }}
      />
    </AttributeConditionLine>
  );
};

export {FileAttributeConditionLine, FileAttributeConditionLineProps};
