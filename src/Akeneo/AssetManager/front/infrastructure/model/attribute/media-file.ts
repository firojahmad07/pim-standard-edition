/* tslint:disable */
/**
 * This file was automatically generated by json-schema-to-typescript.
 * DO NOT MODIFY IT BY HAND. Instead, modify the source JSONSchema file,
 * and run json-schema-to-typescript to regenerate this file.
 */

export type BackendLabels =
  | {
      /**
       * This interface was referenced by `undefined`'s JSON-Schema definition
       * via the `patternProperty` ".+".
       */
      [k: string]: string;
    }
  | [];

export interface BackendAttributeMediaFile {
  type: string;
  identifier: string;
  asset_family_identifier: string;
  code: string;
  labels: BackendLabels;
  is_required: boolean;
  is_read_only: boolean;
  order: number;
  value_per_locale: boolean;
  value_per_channel: boolean;
  media_type: string;
  max_file_size: string | null;
  allowed_extensions: string[];
}
