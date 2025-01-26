import React from 'react';
import {useUserCatalogLocale} from '../../../../../dependenciesTools/hooks';
import {InputMeasurement} from '../../../../../components/Inputs';
import {InputValueProps} from './AttributeValue';
import {getAttributeLabel} from '../../../../../models';
import {
  isMeasurementAmountFilled,
  isMeasurementUnitFilled,
  parseMeasurementValue,
} from '../../../../../models/Measurement';

const isMeasurementValueFilled = (value: any) => {
  return isMeasurementAmountFilled(value) && isMeasurementUnitFilled(value);
};

const MeasurementValue: React.FC<InputValueProps> = ({
  id,
  attribute,
  value,
  label,
  onChange,
}) => {
  const catalogLocale = useUserCatalogLocale();
  return (
    <InputMeasurement
      id={id}
      attribute={attribute}
      onChange={onChange}
      label={label || getAttributeLabel(attribute, catalogLocale)}
      value={value}
    />
  );
};

export {isMeasurementValueFilled};

const render: (props: InputValueProps) => JSX.Element = props => {
  return (
    <MeasurementValue {...props} value={parseMeasurementValue(props.value)} />
  );
};

export default render;
