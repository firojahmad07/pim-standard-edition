import React, {memo, useRef} from 'react';
import {connect} from 'react-redux';
import {Key, Button, SectionTitle, useAutoFocus} from '@akeneo-pim-community/akeneo-design-system';
import {
  useTranslate,
  Translate,
  useSecurity,
  PageHeader,
  LocaleCode,
  LocaleSelector,
  Locale,
  NoDataSection,
  NoDataTitle,
  NoDataText,
  Section,
  PageContent,
} from '@akeneo-pim-community/shared';
import {attributeCreationStart} from 'akeneoassetmanager/domain/event/attribute/create';
import {EditState} from 'akeneoassetmanager/application/reducer/asset-family/edit';
import {CreateState} from 'akeneoassetmanager/application/reducer/attribute/create';
import CreateAttributeModal from 'akeneoassetmanager/application/component/attribute/create';
import ManageOptionsView from 'akeneoassetmanager/application/component/attribute/edit/option';
import AttributeIdentifier from 'akeneoassetmanager/domain/model/attribute/identifier';
import {AssetFamily, getAssetFamilyLabel} from 'akeneoassetmanager/domain/model/asset-family/asset-family';
import {attributeEditionStartByIdentifier} from 'akeneoassetmanager/application/action/attribute/edit';
import AttributeEditForm from 'akeneoassetmanager/application/component/attribute/edit';
import {AssetFamilyBreadcrumb} from 'akeneoassetmanager/application/component/app/breadcrumb';
import denormalizeAttribute from 'akeneoassetmanager/application/denormalizer/attribute/attribute';
import {NormalizedAttribute} from 'akeneoassetmanager/domain/model/attribute/attribute';
import {getAttributeIcon} from 'akeneoassetmanager/application/configuration/attribute';
import ErrorBoundary from 'akeneoassetmanager/application/component/app/error-boundary';
import {EditOptionState} from 'akeneoassetmanager/application/reducer/attribute/type/option';
import {canEditAssetFamily, canEditLocale} from 'akeneoassetmanager/application/reducer/right';
import {catalogLocaleChanged} from 'akeneoassetmanager/domain/event/user';
import {ContextSwitchers} from 'akeneoassetmanager/application/component/app/layout';
import {UserNavigation} from 'akeneoassetmanager/application/component/app/user-navigation';

interface StateProps {
  context: {
    locale: string;
  };
  structure: {
    locales: Locale[];
  };
  rights: {
    locale: {
      edit: boolean;
    };
    assetFamily: {
      edit: boolean;
    };
  };
  assetFamily: AssetFamily;
  createAttribute: CreateState;
  editAttribute: boolean;
  options: EditOptionState;
  attributes: NormalizedAttribute[];
  firstLoading: boolean;
}
interface DispatchProps {
  events: {
    onAttributeCreationStart: () => void;
    onAttributeEdit: (attributeIdentifier: AttributeIdentifier) => void;
    onLocaleChanged: (localeCode: LocaleCode) => void;
  };
}
interface CreateProps extends StateProps, DispatchProps {}

const renderSystemAttribute = (type: string, identifier: string, translate: Translate) => {
  return (
    <div
      className="AknFieldContainer"
      data-placeholder="false"
      data-identifier={`system_asset_${identifier}`}
      data-type={type}
    >
      <div className="AknFieldContainer-header AknFieldContainer-header--light">
        <label
          className="AknFieldContainer-label AknFieldContainer-label--withImage"
          htmlFor={`pim_asset_manager.asset_family.properties.system_asset_${identifier}`}
        >
          <img className="AknFieldContainer-labelImage" src={`bundles/pimui/images/attribute/icon-${type}.svg`} />
          <span>{identifier}</span>
        </label>
      </div>
      <div className="AknFieldContainer-inputContainer">
        <input
          type="text"
          autoComplete="off"
          tabIndex={-1}
          id={`pim_asset_manager.asset_family.properties.system_asset_${identifier}`}
          className="AknTextField AknTextField--light AknTextField--disabled"
          value={translate(`pim_asset_manager.attribute.default.${identifier}`)}
          readOnly
        />
      </div>
    </div>
  );
};

const renderSystemAttributes = (translate: Translate) => {
  return <>{renderSystemAttribute('text', 'code', translate)}</>;
};

const AttributePlaceholders = () => {
  const translate = useTranslate();

  return (
    <>
      {Array(8)
        .fill('placeholder')
        .map((attributeIdentifier, key) => (
          <div key={key} className="AknFieldContainer" data-placeholder="true">
            <div className="AknFieldContainer-header AknFieldContainer-header--light">
              <label
                className="AknFieldContainer-label AknFieldContainer-label--withImage AknLoadingPlaceHolder"
                htmlFor={`pim_asset_manager.asset_family.properties.${attributeIdentifier}_${key}`}
              >
                <img className="AknFieldContainer-labelImage" src={`bundles/pimui/images/attribute/icon-text.svg`} />
                <span>
                  {translate(`pim_asset_manager.attribute.type.text`)}{' '}
                  {`(${translate('pim_asset_manager.attribute.is_required')})`}
                </span>
              </label>
            </div>
            <div className="AknFieldContainer-inputContainer AknLoadingPlaceHolder">
              <input
                type="text"
                autoComplete="off"
                id={`pim_asset_manager.asset_family.properties.${attributeIdentifier}_${key}`}
                className="AknTextField AknTextField--transparent"
              />
              <button className="AknIconButton AknIconButton--trash" />
              <button className="AknIconButton AknIconButton--edit" />
            </div>
          </div>
        ))}
    </>
  );
};

interface AttributeViewProps {
  normalizedAttribute: NormalizedAttribute;
  onAttributeEdit: (attributeIdentifier: AttributeIdentifier) => void;
  locale: string;
  rights: {
    locale: {
      edit: boolean;
    };
    assetFamily: {
      edit: boolean;
    };
  };
}

const AttributeView = memo(({normalizedAttribute, onAttributeEdit, locale, rights}: AttributeViewProps) => {
  const translate = useTranslate();
  const {isGranted} = useSecurity();

  const canEditAttribute = isGranted('akeneo_assetmanager_attribute_edit') && rights.assetFamily.edit;
  const attribute = denormalizeAttribute(normalizedAttribute);
  const icon = getAttributeIcon(attribute.getType());

  return (
    <div
      className="AknFieldContainer"
      data-placeholder="false"
      data-identifier={attribute.getCode()}
      data-type={attribute.getType()}
    >
      <div className="AknFieldContainer-header AknFieldContainer-header--light">
        <label
          className="AknFieldContainer-label AknFieldContainer-label--withImage"
          htmlFor={`pim_asset_manager.asset_family.properties.${attribute.getCode()}`}
        >
          <img className="AknFieldContainer-labelImage" src={icon} />
          <span>
            {attribute.getCode()}{' '}
            {attribute.isRequired ? `(${translate('pim_asset_manager.attribute.is_required')})` : ''}
          </span>
        </label>
      </div>
      <div className="AknFieldContainer-inputContainer">
        <input
          type="text"
          autoComplete="off"
          id={`pim_asset_manager.asset_family.properties.${attribute.getCode()}`}
          className="AknTextField AknTextField--light AknTextField--disabled"
          value={attribute.getLabel(locale)}
          readOnly
          tabIndex={-1}
        />
        {canEditAttribute ? (
          <button
            className="AknIconButton AknIconButton--edit"
            onClick={() => onAttributeEdit(attribute.getIdentifier())}
            onKeyPress={(event: React.KeyboardEvent<HTMLButtonElement>) => {
              if (Key.Space === event.key) onAttributeEdit(attribute.getIdentifier());
            }}
          />
        ) : (
          <button
            className="AknIconButton AknIconButton--view"
            onClick={() => onAttributeEdit(attribute.getIdentifier())}
            onKeyPress={(event: React.KeyboardEvent<HTMLButtonElement>) => {
              if (Key.Space === event.key) onAttributeEdit(attribute.getIdentifier());
            }}
          />
        )}
      </div>
    </div>
  );
});

const AttributesView = ({
  assetFamily,
  rights,
  events,
  context,
  attributes,
  firstLoading,
  createAttribute,
  editAttribute,
  options,
  structure,
}: CreateProps) => {
  const translate = useTranslate();
  const {isGranted} = useSecurity();
  const canCreateAttribute = isGranted('akeneo_assetmanager_attribute_create') && rights.assetFamily.edit;
  const assetFamilyLabel = getAssetFamilyLabel(assetFamily, context.locale);
  const buttonRef = useRef<HTMLButtonElement>(null);

  useAutoFocus(buttonRef);

  return (
    <>
      <PageHeader>
        <PageHeader.Breadcrumb>
          <AssetFamilyBreadcrumb assetFamilyLabel={assetFamilyLabel} />
        </PageHeader.Breadcrumb>
        <PageHeader.UserActions>
          <UserNavigation />
        </PageHeader.UserActions>
        <PageHeader.Actions>
          {canCreateAttribute && (
            <Button level="secondary" onClick={events.onAttributeCreationStart} ref={buttonRef}>
              {translate('pim_asset_manager.attribute.button.add')}
            </Button>
          )}
        </PageHeader.Actions>
        <PageHeader.Title>{translate('pim_asset_manager.asset_family.tab.attribute')}</PageHeader.Title>
        <PageHeader.Content>
          {0 < structure.locales.length && (
            <ContextSwitchers>
              <LocaleSelector value={context.locale} values={structure.locales} onChange={events.onLocaleChanged} />
            </ContextSwitchers>
          )}
        </PageHeader.Content>
      </PageHeader>
      <PageContent>
        <Section>
          <SectionTitle sticky={0}>
            <SectionTitle.Title>{translate('pim_asset_manager.asset_family.attribute.title')}</SectionTitle.Title>
          </SectionTitle>
          {firstLoading || 0 < attributes.length ? (
            <div className="AknSubsection-container">
              <div className="AknFormContainer">
                {renderSystemAttributes(translate)}
                {firstLoading ? (
                  <AttributePlaceholders />
                ) : (
                  <>
                    {attributes.map((attribute: NormalizedAttribute) => (
                      <ErrorBoundary
                        key={attribute.identifier}
                        errorMessage={translate('pim_asset_manager.asset_family.attribute.error.render_list')}
                      >
                        <AttributeView
                          normalizedAttribute={attribute}
                          onAttributeEdit={events.onAttributeEdit}
                          locale={context.locale}
                          rights={rights}
                        />
                      </ErrorBoundary>
                    ))}
                  </>
                )}
              </div>
              {editAttribute && <AttributeEditForm rights={rights} />}
            </div>
          ) : (
            <>
              <div className="AknFormContainer">{renderSystemAttributes(translate)}</div>
              <NoDataSection>
                <NoDataTitle>
                  {translate('pim_asset_manager.attribute.no_data.title', {entityLabel: assetFamilyLabel})}
                </NoDataTitle>
                <NoDataText>{translate('pim_asset_manager.attribute.no_data.subtitle')}</NoDataText>
                <NoDataTitle>
                  <Button level="secondary" onClick={events.onAttributeCreationStart}>
                    {translate('pim_asset_manager.attribute.button.add')}
                  </Button>
                </NoDataTitle>
              </NoDataSection>
            </>
          )}
          {createAttribute.active && <CreateAttributeModal />}
          {options.isActive && <ManageOptionsView rights={rights} />}
        </Section>
      </PageContent>
    </>
  );
};

export default connect(
  (state: EditState): StateProps => {
    const locale = state.user.catalogLocale;

    return {
      context: {
        locale: locale,
      },
      structure: {
        locales: state.structure.locales,
      },
      rights: {
        locale: {
          edit: canEditLocale(state.right.locale, locale),
        },
        assetFamily: {
          edit: canEditAssetFamily(state.right.assetFamily, state.form.data.identifier),
        },
      },
      assetFamily: state.form.data,
      createAttribute: state.createAttribute,
      editAttribute: state.attribute.isActive,
      options: state.options,
      firstLoading: null === state.attributes.attributes,
      attributes: null !== state.attributes.attributes ? state.attributes.attributes : [],
    };
  },
  (dispatch: any): DispatchProps => {
    return {
      events: {
        onAttributeCreationStart: () => {
          dispatch(attributeCreationStart());
        },
        onAttributeEdit: (attributeIdentifier: AttributeIdentifier) => {
          dispatch(attributeEditionStartByIdentifier(attributeIdentifier));
        },
        onLocaleChanged: (localeCode: LocaleCode) => {
          dispatch(catalogLocaleChanged(localeCode));
        },
      },
    };
  }
)(AttributesView);
