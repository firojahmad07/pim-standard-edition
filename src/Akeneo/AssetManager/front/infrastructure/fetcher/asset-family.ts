import {SearchResult} from 'akeneoassetmanager/domain/fetcher/fetcher';
import {AssetFamily} from 'akeneoassetmanager/domain/model/asset-family/asset-family';
import hydrator from 'akeneoassetmanager/application/hydrator/asset-family';
import hydrateAll from 'akeneoassetmanager/application/hydrator/hydrator';
import {getJSON} from 'akeneoassetmanager/tools/fetch';
import AssetFamilyIdentifier from 'akeneoassetmanager/domain/model/asset-family/identifier';
import errorHandler from 'akeneoassetmanager/infrastructure/tools/error-handler';
import {Attribute} from 'akeneoassetmanager/domain/model/attribute/attribute';
import hydrateAttribute from 'akeneoassetmanager/application/hydrator/attribute';
import {AssetFamilyPermission} from 'akeneoassetmanager/domain/model/permission/asset-family';
import {validateBackendAssetFamily} from 'akeneoassetmanager/infrastructure/validator/asset-family';
import {
  AssetFamilyListItem,
  createAssetFamilyListItemFromNormalized,
} from 'akeneoassetmanager/domain/model/asset-family/list';
import {AssetFamilyFetcher} from 'akeneoassetmanager/domain/fetcher/asset-family';

const routing = require('routing');

export type AssetFamilyResult = {
  assetFamily: AssetFamily;
  assetCount: number;
  attributes: Attribute[];
  permission: AssetFamilyPermission;
};

export class AssetFamilyFetcherImplementation implements AssetFamilyFetcher {
  async fetch(identifier: AssetFamilyIdentifier): Promise<AssetFamilyResult> {
    const data = await getJSON(
      routing.generate('akeneo_asset_manager_asset_family_get_rest', {identifier: identifier})
    ).catch(errorHandler);

    const backendAssetFamily = validateBackendAssetFamily(data);

    return {
      assetFamily: hydrator(backendAssetFamily),
      assetCount: backendAssetFamily.asset_count,
      attributes: backendAssetFamily.attributes.map(hydrateAttribute),
      permission: {
        assetFamilyIdentifier: identifier,
        edit: backendAssetFamily.permission.edit,
      },
    };
  }

  async fetchAll(): Promise<AssetFamilyListItem[]> {
    const backendAssetFamilies = await getJSON(routing.generate('akeneo_asset_manager_asset_family_index_rest')).catch(
      errorHandler
    );

    return hydrateAll<AssetFamilyListItem>(createAssetFamilyListItemFromNormalized)(backendAssetFamilies.items);
  }

  async search(): Promise<SearchResult<AssetFamilyListItem>> {
    const backendAssetFamilies = await getJSON(routing.generate('akeneo_asset_manager_asset_family_index_rest')).catch(
      errorHandler
    );

    const items = hydrateAll<AssetFamilyListItem>(createAssetFamilyListItemFromNormalized)(backendAssetFamilies.items);

    return {
      items,
      matchesCount: backendAssetFamilies.matchesCount,
      totalCount: backendAssetFamilies.matchesCount,
    };
  }
}

export default new AssetFamilyFetcherImplementation();
