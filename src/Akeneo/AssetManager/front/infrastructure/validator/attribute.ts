import {validateAgainstSchema} from '@akeneo-pim-community/legacy-bridge';
import {BackendAttribute} from '../../infrastructure/model/attribute';
import schema from '../../infrastructure/model/attribute.schema.json';

export const validateBackendAttribute = (data: any): BackendAttribute =>
  validateAgainstSchema<BackendAttribute>(data, schema);
