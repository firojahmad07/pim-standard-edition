import promisify from 'akeneoassetmanager/tools/promisify';
import {AttributeGroupCode} from 'akeneoassetmanager/platform/model/structure/attribute';
import {CategoryCode} from 'akeneopimenrichmentassetmanager/enrich/domain/model/product';
import {isObject, isArray, isString, isBoolean} from 'akeneoassetmanager/domain/model/utils';
import {LocaleCode} from 'akeneoassetmanager/domain/model/locale';
import LocaleReference from 'akeneoassetmanager/domain/model/locale-reference';
const fetcherRegistry = require('pim/fetcher-registry');

export type AttributeGroupPermission = {
  code: AttributeGroupCode;
  edit: boolean;
  view: boolean;
};
export const isAttributeGroupEditable = (
  attributeGroupPermissions: AttributeGroupPermission[],
  attributeGroupCode: AttributeGroupCode
) => {
  const permission = attributeGroupPermissions.find(
    (attributeGroupPermission: AttributeGroupPermission) => attributeGroupPermission.code === attributeGroupCode
  );

  return undefined === permission || permission.edit;
};

export type LocalePermission = {
  code: LocaleCode;
  edit: boolean;
  view: boolean;
};
export const isLocaleEditable = (localePermissions: LocalePermission[], locale: LocaleReference): boolean => {
  if (null === locale) {
    return true;
  }

  const permission = localePermissions.find((localePermission: LocalePermission) => localePermission.code === locale);

  return undefined === permission || permission.edit;
};

export type CategoryPermissions = {
  EDIT_ITEMS: CategoryCode[];
};

export type Permissions = {
  attributeGroups: AttributeGroupPermission[];
  locales: LocalePermission[];
  categories: CategoryPermissions;
};

/**
 * Need to export this function in a variable to be able to mock it in our tests.
 * We couldn't require the pim/fetcher-registry in our test stack. We need to mock the legacy fetcher used.
 */
export const permissionFetcher = () => fetcherRegistry.getFetcher('permission');
export const fetchPermissions = (permissionFetcher: any) => async (): Promise<Permissions> => {
  const permissions = await promisify(permissionFetcher.fetchAll());

  return denormalizePermissionCollection(permissions);
};

const denormalizePermissionCollection = (permissions: any): Permissions => {
  if (!isAttributeGroupPermissions(permissions.attribute_groups)) {
    throw Error('The attribute_group permissions are not well formated');
  }
  if (!isLocalePermissions(permissions.locales)) {
    throw Error('The locale permissions are not well formated');
  }
  if (!isCategoryPermissions(permissions.categories)) {
    throw Error('The category permissions are not well formated');
  }

  return {
    attributeGroups: permissions.attribute_groups,
    locales: permissions.locales,
    categories: permissions.categories,
  };
};

const isAttributeGroupPermissions = (
  attributeGroupPermissions: any
): attributeGroupPermissions is AttributeGroupPermission[] => {
  return (
    isArray(attributeGroupPermissions) &&
    !attributeGroupPermissions.some(
      ({code, edit, view}: any) => !isString(code) || !isBoolean(edit) || !isBoolean(view)
    )
  );
};

const isLocalePermissions = (localePermissions: any): localePermissions is LocalePermission[] => {
  return (
    isArray(localePermissions) &&
    !localePermissions.some(({code, edit, view}: any) => !isString(code) || !isBoolean(edit) || !isBoolean(view))
  );
};
const isCategoryPermissions = (categoryPermissions: any): categoryPermissions is CategoryPermissions => {
  return (
    isObject(categoryPermissions) &&
    (!isArray(categoryPermissions.EDIT_ITEMS) ||
      !categoryPermissions.EDIT_ITEMS.some((categoryCode: any) => !isString(categoryCode)))
  );
};
