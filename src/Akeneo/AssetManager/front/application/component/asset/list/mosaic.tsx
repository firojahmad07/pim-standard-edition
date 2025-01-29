import React, {RefObject, useRef} from 'react';
import styled from 'styled-components';
import {AssetsIllustration, CardGrid, Helper, Information} from '@akeneo-pim-community/akeneo-design-system';
import {useTranslate} from '@akeneo-pim-community/shared';
import {Context} from 'akeneoassetmanager/domain/model/context';
import AssetCard from 'akeneoassetmanager/application/component/asset/list/mosaic/asset-card';
import EmptyResult from 'akeneoassetmanager/application/component/asset/list/mosaic/empty-result';
import ListAsset, {ASSET_COLLECTION_LIMIT} from 'akeneoassetmanager/domain/model/asset/list-asset';
import AssetCode from 'akeneoassetmanager/domain/model/asset/code';

const MAX_DISPLAYED_ASSETS = 500;

const Container = styled.div`
  height: 100%;
  overflow-y: auto;
  flex: 1;
  padding-top: 20px;
`;

const MoreResults = styled.div`
  margin-top: 20px;
`;

type MosaicProps = {
  scrollContainerRef?: RefObject<HTMLDivElement>;
  assetCollection: ListAsset[];
  context: Context;
  resultCount: number | null;
  hasReachMaximumSelection: boolean;
  selectionState: 'mixed' | boolean;
  assetWithLink?: boolean;
  onSelectionChange?: (assetCode: AssetCode, newValue: boolean) => void;
  isItemSelected: (assetCode: AssetCode) => boolean;
  onAssetClick?: (asset: AssetCode) => void;
};

const Mosaic = ({
  scrollContainerRef = useRef<null | HTMLDivElement>(null),
  context,
  onSelectionChange,
  isItemSelected,
  assetCollection,
  hasReachMaximumSelection,
  resultCount,
  onAssetClick,
  selectionState,
  assetWithLink = false,
}: MosaicProps) => {
  const translate = useTranslate();
  const shouldDisplayMoreResultsHelper =
    null !== resultCount && resultCount >= MAX_DISPLAYED_ASSETS && assetCollection.length === MAX_DISPLAYED_ASSETS;

  return (
    <>
      {hasReachMaximumSelection && (
        <Helper>
          {translate('pim_asset_manager.asset_collection.notification.limit', {limit: ASSET_COLLECTION_LIMIT})}
        </Helper>
      )}
      {assetCollection.length > 0 ? (
        <Container data-container="mosaic" ref={scrollContainerRef}>
          <CardGrid>
            {assetCollection.map((asset: ListAsset) => {
              const isSelected = isItemSelected(asset.code);

              return (
                <AssetCard
                  key={asset.code}
                  asset={asset}
                  context={context}
                  isSelected={isSelected}
                  isDisabled={hasReachMaximumSelection && !isSelected}
                  onSelectionChange={onSelectionChange}
                  onClick={!selectionState ? onAssetClick : undefined}
                  assetWithLink={assetWithLink}
                />
              );
            })}
          </CardGrid>
          <MoreResults>
            {shouldDisplayMoreResultsHelper && (
              <Information
                illustration={<AssetsIllustration />}
                title={translate('pim_asset_manager.asset.grid.more_result.title')}
              >
                {translate('pim_asset_manager.asset.grid.more_result.description', {
                  total: resultCount,
                  maxDisplayedAssets: MAX_DISPLAYED_ASSETS,
                })}
              </Information>
            )}
          </MoreResults>
        </Container>
      ) : (
        <EmptyResult />
      )}
    </>
  );
};

export default Mosaic;
