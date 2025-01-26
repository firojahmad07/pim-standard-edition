import {TEMPLATES, TemplateVariation} from './Template';
import {SelectOption, TableConfiguration} from './TableConfiguration';
import {LocaleCode} from '@akeneo-pim-community/shared/';

type TranslationMessages = {[messageKey: string]: string};
type TranslationMessagesByLocale = {[localeCode: string]: TranslationMessages};

const getTranslatedTableConfigurationFromVariationTemplate = async (
  variationTemplate: string,
  localeCodes: LocaleCode[]
): Promise<TableConfiguration> => {
  const templateVariation = ([] as TemplateVariation[])
    .concat(...TEMPLATES.map(template => template.template_variations))
    .find(template => template.code === variationTemplate);

  if (!templateVariation) {
    return [];
  }

  const messages = await getMessages(localeCodes);

  const tableConfiguration = templateVariation.tableConfiguration;
  return tableConfiguration.map(columnDefinition => {
    localeCodes.forEach(localeCode => {
      const key = `jsmessages:table_attribute_template.${templateVariation.code}.${columnDefinition.code}.label`;
      const label = messages[localeCode][key];
      if (label) {
        columnDefinition.labels[localeCode] = label;
      }
    });

    if ('options' in columnDefinition && typeof columnDefinition.options !== 'undefined') {
      columnDefinition.options = columnDefinition.options.map((option: SelectOption) => {
        localeCodes.forEach(localeCode => {
          const key = `jsmessages:table_attribute_template.${templateVariation.code}.${columnDefinition.code}.options.${option.code}`;
          const label = messages[localeCode][key];
          if (label) {
            option.labels[localeCode] = label;
          }
        });

        return option;
      });
    }

    return columnDefinition;
  });
};

const getMessages: (localeCodes: LocaleCode[]) => Promise<TranslationMessagesByLocale> = async localeCodes => {
  const responses: TranslationMessagesByLocale = {};
  for await (const localeCode of localeCodes) {
    const response = await fetch(`js/translation/${localeCode}.js`);
    const json: {messages: TranslationMessages} = await response.json();
    const messages: TranslationMessages = {};
    Object.keys(json.messages).forEach(messageKey => {
      if (/^jsmessages:table_attribute_template\./.exec(messageKey)) {
        messages[messageKey] = json.messages[messageKey];
      }
    });
    responses[localeCode] = messages;
  }
  return responses;
};

export {getTranslatedTableConfigurationFromVariationTemplate};
