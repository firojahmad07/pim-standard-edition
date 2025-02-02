import React, {FC, useRef} from 'react';
import {ArrowSimpleUpIcon, getColor, IconButton, SectionTitle} from 'akeneo-design-system';
import {useTranslate} from '../../../shared/translate';
import styled from 'styled-components';
import {useDisplayScrollTopButton} from '../../../shared/scroll/hooks/useDisplayScrollTopButton';
import findScrollParent from '../../../shared/scroll/utils/findScrollParent';
import {ConnectedApp} from '../../../model/Apps/connected-app';
import {useFeatureFlags} from '../../../shared/feature-flags';
import ConnectedAppsContainerHelper from './ConnectedAppsContainerHelper';
import {ConnectedAppCard} from './ConnectedAppCard';
import {NoConnectedApps} from './NoConnectedApps';
import {CardGrid} from '../Section';

const ScrollToTop = styled(IconButton)`
    position: fixed;
    bottom: 40px;
    right: 40px;
    width: 38px;
    height: 38px;
    border-radius: 50%;

    background-color: ${getColor('brand', 100)};
    color: ${getColor('white')};

    &:hover:not([disabled]) {
        background-color: ${getColor('brand', 120)};
    }
`;

type Props = {
    connectedApps: ConnectedApp[];
};

export const ConnectedAppsContainer: FC<Props> = ({connectedApps}) => {
    const translate = useTranslate();
    const featureFlag = useFeatureFlags();
    const ref = useRef(null);
    const scrollContainer = findScrollParent(ref.current);
    const displayScrollButton = useDisplayScrollTopButton(ref);
    const connectedAppCards = connectedApps.map((connectedApp: ConnectedApp) => (
        <ConnectedAppCard key={connectedApp.id} item={connectedApp} />
    ));
    const handleScrollTop = () => {
        scrollContainer.scrollTo(0, 0);
    };

    return (
        <>
            <div ref={ref} />
            <ConnectedAppsContainerHelper count={connectedApps.length} />

            {featureFlag.isEnabled('marketplace_activate') && (
                <>
                    <SectionTitle>
                        <SectionTitle.Title>
                            {translate('akeneo_connectivity.connection.connect.connected_apps.list.apps.title')}
                        </SectionTitle.Title>
                        <SectionTitle.Spacer />
                        <SectionTitle.Information>
                            {translate(
                                'akeneo_connectivity.connection.connect.connected_apps.list.apps.total',
                                {
                                    total: connectedApps.length.toString(),
                                },
                                connectedApps.length
                            )}
                        </SectionTitle.Information>
                    </SectionTitle>

                    {0 === connectedAppCards.length && <NoConnectedApps />}
                    {connectedAppCards.length > 0 && <CardGrid>{connectedAppCards}</CardGrid>}
                </>
            )}

            {displayScrollButton && (
                <ScrollToTop
                    onClick={handleScrollTop}
                    title={translate('akeneo_connectivity.connection.connect.marketplace.scroll_to_top')}
                    icon={<ArrowSimpleUpIcon />}
                />
            )}
        </>
    );
};
