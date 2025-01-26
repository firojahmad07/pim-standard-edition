import React from 'react';
import {filterErrors} from '@akeneo-pim-community/shared';
import {AttributeConfiguratorProps} from '../../../models';
import {isMeasurementSource} from './model';
import {MeasurementSelector} from './MeasurementSelector';
import {InvalidAttributeSourceError} from '../error';
import {DefaultValue, Operations} from '../common';
import {MeasurementConversion} from './MeasurementConversion';
import {MeasurementRounding} from './MeasurementRounding';

const MeasurementConfigurator = ({source, attribute, validationErrors, onSourceChange}: AttributeConfiguratorProps) => {
  if (!isMeasurementSource(source) || undefined === attribute.metric_family) {
    throw new InvalidAttributeSourceError(`Invalid source data "${source.code}" for measurement configurator`);
  }

  return (
    <Operations>
      <DefaultValue
        operation={source.operations.default_value}
        validationErrors={filterErrors(validationErrors, '[operations][default_value]')}
        onOperationChange={updatedOperation =>
          onSourceChange({...source, operations: {...source.operations, default_value: updatedOperation}})
        }
      />
      <MeasurementConversion
        operation={source.operations.measurement_conversion}
        measurementFamilyCode={attribute.metric_family}
        validationErrors={filterErrors(validationErrors, '[operations][measurement_conversion]')}
        onOperationChange={updatedOperation =>
          onSourceChange({...source, operations: {...source.operations, measurement_conversion: updatedOperation}})
        }
      />
      <MeasurementRounding
        operation={source.operations.measurement_rounding}
        validationErrors={filterErrors(validationErrors, '[operations][measurement_rounding]')}
        onOperationChange={updatedOperation =>
          onSourceChange({...source, operations: {...source.operations, measurement_rounding: updatedOperation}})
        }
      />
      <MeasurementSelector
        selection={source.selection}
        validationErrors={filterErrors(validationErrors, '[selection]')}
        onSelectionChange={updatedMeasurementSelection =>
          onSourceChange({...source, selection: updatedMeasurementSelection})
        }
      />
    </Operations>
  );
};

export {MeasurementConfigurator};
