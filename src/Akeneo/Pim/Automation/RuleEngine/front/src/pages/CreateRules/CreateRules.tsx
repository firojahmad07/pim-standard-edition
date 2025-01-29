import React from 'react';
import {ThemeProvider} from 'styled-components';
import {
  generateAndRedirect,
  generateUrl,
  NotificationLevel,
  redirectToUrl,
  useBackboneRouter,
  useNotify,
  useTranslate,
  useUserContext,
} from '../../dependenciesTools/hooks';
import {useDocumentEscapeKey} from '../../hooks';
import {CrossLink} from './components/CrossLink';
import {CreateRulesForm, FormDataInput} from './components/CreateRulesForm';
import {httpPost} from '../../fetch';
import {AkeneoSpinner} from '../../components';
import {Payload} from '../../rules.types';
import {LocaleCode} from '../../models';
import {pimTheme} from '@akeneo-pim-community/akeneo-design-system';
import {dependencies} from '../../dependenciesTools/provider/dependencies';

const transformFormData = (
  formData: FormDataInput,
  currentCatalogLocale: LocaleCode
): Payload => {
  return {
    labels: {[currentCatalogLocale]: formData.label},
    priority: 0,
    enabled: false,
    code: formData.code,
    content: {
      conditions: {},
      actions: {},
    },
  };
};

type Props = {
  originalRuleCode?: string;
};

const CreateRules: React.FC<Props> = ({originalRuleCode}) => {
  const [pending, setPending] = React.useState(false);
  const translate = useTranslate();
  const router = useBackboneRouter();
  const userContext = useUserContext();
  const [urlRules, handleRulesRoute] = generateAndRedirect(
    router,
    originalRuleCode
      ? 'pimee_catalog_rule_edit'
      : 'pimee_catalog_rule_rule_index',
    originalRuleCode ? {code: originalRuleCode} : undefined
  );
  useDocumentEscapeKey(handleRulesRoute);
  const currentCatalogLocale = userContext.get('catalogLocale');
  const notify = useNotify();
  const onSubmit = async (formData: FormDataInput): Promise<any> => {
    const url = originalRuleCode
      ? generateUrl(router, 'pimee_enrich_rule_definition_duplicate', {
          originalRuleCode,
        })
      : generateUrl(router, 'pimee_enrich_rule_definition_create');
    setPending(true);
    let result: any;
    try {
      result = await httpPost(url, {
        body: transformFormData(formData, currentCatalogLocale),
      });
    } catch (error) {
      setPending(false);
      notify(
        NotificationLevel.ERROR,
        translate('pimee_catalog_rule.form.creation.notification.failed')
      );
      return error;
    }
    if (result.ok) {
      dependencies.ruleManager.familyAttributesRulesNumberPromise = null;
      notify(
        NotificationLevel.SUCCESS,
        translate('pimee_catalog_rule.form.creation.notification.success')
      );
      redirectToUrl(
        router,
        generateUrl(router, 'pimee_catalog_rule_edit', {code: formData.code})
      );
    } else {
      setPending(false);
      notify(
        NotificationLevel.ERROR,
        translate('pimee_catalog_rule.form.creation.notification.failed')
      );
    }
    return result;
  };

  return (
    <ThemeProvider theme={pimTheme}>
      <div className='AknFullPage'>
        {pending && <AkeneoSpinner />}
        <div className='AknFullPage-content AknFullPage-content--withIllustration'>
          <div>
            <div className='AknFullPage-image AknFullPage-illustration AknFullPage-illustration--rules' />
          </div>
          <div>
            <div className='AknFullPage-titleContainer'>
              <div className='AknFullPage-subTitle'>{`${translate(
                'pim_menu.item.rule'
              )} /`}</div>
              <div className='AknFullPage-title'>
                {originalRuleCode
                  ? translate('pimee_catalog_rule.form.edit.duplicate.title', {
                      originalRuleCode,
                    })
                  : translate('pimee_catalog_rule.form.creation.title')}
              </div>
            </div>
            <CreateRulesForm
              onSubmit={onSubmit}
              translate={translate}
              locale={currentCatalogLocale}
            />
          </div>
        </div>
      </div>
      <CrossLink
        data-testid='leave-page-button'
        href={`#${urlRules}`}
        onClick={handleRulesRoute}>
        {translate('pimee_catalog_rule.form.creation.cross_link')}
      </CrossLink>
    </ThemeProvider>
  );
};

export {CreateRules};
