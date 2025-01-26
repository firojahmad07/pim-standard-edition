import Code, {createCode} from 'akeneoreferenceentity/domain/model/product/attribute/code';
import LabelCollection, {
  NormalizedLabelCollection,
  createLabelCollection,
} from 'akeneoreferenceentity/domain/model/label-collection';
import AttributeCode, {NormalizedCode} from 'akeneoreferenceentity/domain/model/product/attribute/code';

type AttributeType = string;
type UseableAsGridFilter = boolean;

type NormalizedAttributeType = string;
type NormalizedReferenceDataName = string;
type ReferenceDataName = string;
type NormalizedUseableAsGridFilter = boolean;

export interface NormalizedAttribute {
  code: NormalizedCode;
  type: NormalizedAttributeType;
  labels: NormalizedLabelCollection;
  reference_data_name: NormalizedReferenceDataName;
  useable_as_grid_filter: NormalizedUseableAsGridFilter;
}

export default interface Attribute {
  getCode: () => AttributeCode;
  getType: () => AttributeType;
  getReferenceDataName: () => ReferenceDataName;
  getLabel: (locale: string, fallbackOnCode?: boolean) => string;
  getLabelCollection: () => LabelCollection;
  getUseableAsGridFilter: () => UseableAsGridFilter;
  equals: (attribute: Attribute) => boolean;
  normalize: () => NormalizedAttribute;
}
class InvalidArgumentError extends Error {}

class AttributeImplementation implements Attribute {
  private constructor(
    private code: Code,
    private type: AttributeType,
    private labelCollection: LabelCollection,
    private referenceDataName: ReferenceDataName,
    private useableAsGridFilter: UseableAsGridFilter
  ) {
    if (!(code instanceof Code)) {
      throw new InvalidArgumentError('Attribute expects an AttributeCode as code argument');
    }
    if (typeof type !== 'string') {
      throw new InvalidArgumentError('Attribute expects a string as type argument');
    }
    if (!(labelCollection instanceof LabelCollection)) {
      throw new InvalidArgumentError('Attribute expects a LabelCollection as labelCollection argument');
    }
    if (typeof referenceDataName !== 'string') {
      throw new InvalidArgumentError('Attribute expects a string as referenceDataName argument');
    }
    if (typeof useableAsGridFilter !== 'boolean') {
      throw new InvalidArgumentError('Attribute expects a boolean as type argument');
    }

    Object.freeze(this);
  }

  public static create(
    code: Code,
    type: AttributeType,
    labelCollection: LabelCollection,
    referenceDataName: ReferenceDataName,
    useableAsGridFilter: UseableAsGridFilter
  ): Attribute {
    return new AttributeImplementation(code, type, labelCollection, referenceDataName, useableAsGridFilter);
  }

  public static createFromNormalized(normalizedAttribute: NormalizedAttribute): Attribute {
    const code = createCode(normalizedAttribute.code);
    const type = normalizedAttribute.type;
    const referenceDataName = normalizedAttribute.reference_data_name;
    const labelCollection = createLabelCollection(normalizedAttribute.labels);
    const useableAsGridFilter = normalizedAttribute.useable_as_grid_filter;

    return AttributeImplementation.create(code, type, labelCollection, referenceDataName, useableAsGridFilter);
  }

  public getCode(): Code {
    return this.code;
  }

  public getType(): AttributeType {
    return this.type;
  }

  public getLabel(locale: string, fallbackOnCode: boolean = true) {
    if (!this.labelCollection.hasLabel(locale)) {
      return fallbackOnCode ? `[${this.getCode().stringValue()}]` : '';
    }

    return this.labelCollection.getLabel(locale);
  }

  public getLabelCollection(): LabelCollection {
    return this.labelCollection;
  }

  public getReferenceDataName(): ReferenceDataName {
    return this.referenceDataName;
  }

  public getUseableAsGridFilter(): UseableAsGridFilter {
    return this.useableAsGridFilter;
  }

  public equals(attribute: Attribute): boolean {
    return attribute.getCode().equals(this.code);
  }

  public normalize(): NormalizedAttribute {
    return {
      code: this.getCode().stringValue(),
      type: this.getType(),
      labels: this.getLabelCollection().normalize(),
      reference_data_name: this.getReferenceDataName(),
      useable_as_grid_filter: this.getUseableAsGridFilter(),
    };
  }
}

export const createAttribute = AttributeImplementation.create;
export const denormalizeAttribute = AttributeImplementation.createFromNormalized;
