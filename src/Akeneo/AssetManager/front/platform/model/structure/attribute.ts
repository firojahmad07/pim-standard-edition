import {getLabel, LocaleCode} from '@akeneo-pim-community/shared';

export type AttributeCode = string;
export type AttributeGroupCode = string;
export type ReferenceDataName = string;
export type Attribute = {
  code: AttributeCode;
  labels: {
    [locale: string]: string;
  };
  group: AttributeGroupCode;
  isReadOnly: boolean;
  referenceDataName: ReferenceDataName;
  sort_order: number;
  availableLocales: string[];
};

export const getAttributeLabel = (attribute: Attribute, locale: LocaleCode) => {
  return getLabel(attribute.labels, locale, attribute.code);
};
