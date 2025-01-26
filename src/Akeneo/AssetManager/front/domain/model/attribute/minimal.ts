import AssetFamilyIdentifier, {
  denormalizeAssetFamilyIdentifier,
  assetFamilyIdentifierStringValue,
} from 'akeneoassetmanager/domain/model/asset-family/identifier';
import LabelCollection, {
  denormalizeLabelCollection,
  getLabelInCollection,
} from 'akeneoassetmanager/domain/model/label-collection';
import AttributeCode, {
  denormalizeAttributeCode,
  attributeCodeStringValue,
} from 'akeneoassetmanager/domain/model/attribute/code';

/**
 * @api
 */
export interface MinimalNormalizedAttribute {
  asset_family_identifier: string;
  type: string;
  code: string;
  labels: LabelCollection;
  value_per_locale: boolean;
  value_per_channel: boolean;
}

/**
 * @api
 */
export default interface MinimalAttribute {
  assetFamilyIdentifier: AssetFamilyIdentifier;
  code: AttributeCode;
  labelCollection: LabelCollection;
  type: string;
  valuePerLocale: boolean;
  valuePerChannel: boolean;
  getCode: () => AttributeCode;
  getAssetFamilyIdentifier: () => AssetFamilyIdentifier;
  getType(): string;
  getLabel: (locale: string, fallbackOnCode?: boolean) => string;
  getLabelCollection: () => LabelCollection;
  normalize(): MinimalNormalizedAttribute;
}

class InvalidArgumentError extends Error {}

/**
 * @api
 */
export class MinimalConcreteAttribute implements MinimalAttribute {
  protected constructor(
    readonly assetFamilyIdentifier: AssetFamilyIdentifier,
    readonly code: AttributeCode,
    readonly labelCollection: LabelCollection,
    readonly type: string,
    readonly valuePerLocale: boolean,
    readonly valuePerChannel: boolean
  ) {
    if (typeof type !== 'string') {
      throw new InvalidArgumentError('Attribute expects a string as attribute type');
    }
    if (typeof valuePerLocale !== 'boolean') {
      throw new InvalidArgumentError('Attribute expects a boolean as valuePerLocale');
    }
    if (typeof valuePerChannel !== 'boolean') {
      throw new InvalidArgumentError('Attribute expects a boolean as valuePerChannel');
    }
  }

  public static createFromNormalized(minimalNormalizedAttribute: MinimalNormalizedAttribute) {
    return new MinimalConcreteAttribute(
      denormalizeAssetFamilyIdentifier(minimalNormalizedAttribute.asset_family_identifier),
      denormalizeAttributeCode(minimalNormalizedAttribute.code),
      denormalizeLabelCollection(minimalNormalizedAttribute.labels),
      minimalNormalizedAttribute.type,
      minimalNormalizedAttribute.value_per_locale,
      minimalNormalizedAttribute.value_per_channel
    );
  }

  public getAssetFamilyIdentifier(): AssetFamilyIdentifier {
    return this.assetFamilyIdentifier;
  }

  public getCode(): AttributeCode {
    return this.code;
  }

  public getType(): string {
    return this.type;
  }

  public getLabel(locale: string, fallbackOnCode: boolean = true) {
    return getLabelInCollection(this.labelCollection, locale, fallbackOnCode, attributeCodeStringValue(this.getCode()));
  }

  public getLabelCollection(): LabelCollection {
    return this.labelCollection;
  }

  public normalize(): MinimalNormalizedAttribute {
    return {
      asset_family_identifier: assetFamilyIdentifierStringValue(this.assetFamilyIdentifier),
      code: this.code,
      type: this.getType(),
      labels: this.labelCollection,
      value_per_locale: this.valuePerLocale,
      value_per_channel: this.valuePerChannel,
    };
  }
}

export const denormalizeMinimalAttribute = (normalizedAttribute: MinimalNormalizedAttribute) => {
  return MinimalConcreteAttribute.createFromNormalized(normalizedAttribute);
};
