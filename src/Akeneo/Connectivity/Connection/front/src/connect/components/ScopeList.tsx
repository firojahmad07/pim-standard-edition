import React, {FC} from 'react';
import {useTranslate} from '../../shared/translate';
import styled from 'styled-components';
import {
    AddAttributeIcon,
    AssociateIcon,
    CategoryIcon,
    getColor,
    getFontSize,
    GroupsIcon,
    LocaleIcon,
    ProductIcon,
    ShopIcon,
    CheckRoundIcon,
    EntityIcon,
    AssetsIcon,
    AkeneoThemedProps,
    FontSize,
} from 'akeneo-design-system';
import ScopeMessage from '../../model/Apps/scope-message';

export const ScopeItem = styled.li.attrs((props: {fontSize?: keyof FontSize} & AkeneoThemedProps) => ({
    fontSize: props.fontSize || 'bigger',
}))`
    color: ${getColor('grey', 140)};
    font-size: ${props => getFontSize(props.fontSize)};
    font-weight: normal;
    line-height: 24px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;

    & > svg {
        margin-right: 10px;
        color: ${getColor('grey', 100)};
    }
`;

const iconsMap: {[key: string]: React.ElementType} = {
    catalog_structure: GroupsIcon,
    attribute_options: AddAttributeIcon,
    categories: CategoryIcon,
    channel_settings: ShopIcon,
    channel_localization: LocaleIcon,
    association_types: AssociateIcon,
    products: ProductIcon,
    reference_entity: EntityIcon,
    reference_entity_record: EntityIcon,
    asset_families: AssetsIcon,
    assets: AssetsIcon,
};

interface Props {
    scopeMessages: ScopeMessage[];
    itemFontSize?: string;
}

export const ScopeList: FC<Props> = ({scopeMessages, itemFontSize}) => {
    const translate = useTranslate();

    return (
        <ul data-testid={'scope-list'}>
            {scopeMessages.map((scopeMessage, key) => {
                const entities = translate(
                    `akeneo_connectivity.connection.connect.apps.scope.entities.${scopeMessage.entities}`
                );
                const Icon = iconsMap[scopeMessage.icon] ?? CheckRoundIcon;

                return (
                    <ScopeItem key={key} fontSize={itemFontSize}>
                        <Icon title={entities} size={24} />
                        <div
                            dangerouslySetInnerHTML={{
                                __html: translate(
                                    `akeneo_connectivity.connection.connect.apps.scope.type.${scopeMessage.type}`,
                                    {
                                        entities: `<span class='AknConnectivityConnection-helper--highlight'>
                                                    ${entities}
                                                </span>`,
                                    }
                                ),
                            }}
                        />
                    </ScopeItem>
                );
            })}
        </ul>
    );
};
