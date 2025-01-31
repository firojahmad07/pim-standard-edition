import {AttributeCreateFormApp, AttributeEditFormApp} from './application';
// import fetchProductDataQualityEvaluation from '@akeneo-pim-community/data-quality-insights/src/infrastructure/fetcher/ProductEditForm/fetchProductDataQualityEvaluation';
// import fetchProductModelEvaluation from '@akeneo-pim-community/data-quality-insights/src/infrastructure/fetcher/ProductEditForm/fetchProductModelEvaluation';
import Rate from './application/component/Rate';
import {Dashboard} from './application/component/Dashboard/Dashboard';
import DashboardHelper from './application/component/Dashboard/DashboardHelper';


import {
  CATALOG_CONTEXT_CHANNEL_CHANGED,
  CATALOG_CONTEXT_LOCALE_CHANGED,
  DATA_QUALITY_INSIGHTS_FILTER_ALL_IMPROVABLE_ATTRIBUTES,
  DATA_QUALITY_INSIGHTS_FILTER_ALL_MISSING_ATTRIBUTES,
  DATA_QUALITY_INSIGHTS_PRODUCT_SAVED,
  DATA_QUALITY_INSIGHTS_PRODUCT_SAVING,
  DATA_QUALITY_INSIGHTS_REDIRECT_TO_DQI_TAB,
  DATA_QUALITY_INSIGHTS_SHOW_ATTRIBUTE,
  PRODUCT_ATTRIBUTES_TAB_LOADED,
  PRODUCT_ATTRIBUTES_TAB_LOADING,
  PRODUCT_MODEL_LEVEL_CHANGED,
  PRODUCT_TAB_CHANGED,
} from './application/listener';

import {
  ATTRIBUTES_TAB_CONTENT_CONTAINER_ELEMENT_ID,
  BACK_LINK_SESSION_STORAGE_KEY,
  PRODUCT_ATTRIBUTES_TAB_NAME,
  PRODUCT_DATA_QUALITY_INSIGHTS_TAB_NAME,
  PRODUCT_MODEL_ATTRIBUTES_TAB_NAME,
  PRODUCT_MODEL_DATA_QUALITY_INSIGHTS_TAB_NAME,
} from './application/constant';

import {CriterionEvaluationResult, ProductEvaluation, TimePeriod} from './domain';

import {DictionaryApp} from './application/component/Locale/DictionaryApp';
import fetchSpellcheckEvaluation from './infrastructure/fetcher/AttributeEditForm/fetchSpellcheckEvaluation';
import ProductEditFormApp from './application/ProductEditFormApp';
import ProductModelEditFormApp from './application/ProductModelEditFormApp';
import {DATA_QUALITY_INSIGHTS_TAB_CONTENT_CONTAINER_ELEMENT_ID} from './application/component/ProductEditForm/TabContent';

import fetchProductDataQualityEvaluation from './infrastructure/fetcher/ProductEditForm/fetchProductDataQualityEvaluation';
import fetchProductModelEvaluation from './infrastructure/fetcher/ProductEditForm/fetchProductModelEvaluation';
import {AttributeGroupDQIActivation} from './application/component/AttributeGroup/AttributeGroupDQIActivation';
import {QualityScoreBar} from './application/component/QualityScoreBar';
import {CONTAINER_ELEMENT_ID as DATA_QUALITY_INSIGHTS_PRODUCT_QUALITY_SCORE_CONTAINER_ELEMENT_ID} from './application/component/ProductEditForm/QualityScorePortal';

// export {CriterionEvaluationResult, ProductEvaluation, TimePeriod} from './domain';

export {BackLinkButton} from './application';
export * from './application/constant';

export {
  Rate,
  TimePeriod,
  Dashboard,
  DashboardHelper,
  CATALOG_CONTEXT_CHANNEL_CHANGED,
  CATALOG_CONTEXT_LOCALE_CHANGED,
  PRODUCT_ATTRIBUTES_TAB_LOADED,
  PRODUCT_ATTRIBUTES_TAB_LOADING,
  PRODUCT_TAB_CHANGED,
  DATA_QUALITY_INSIGHTS_SHOW_ATTRIBUTE,
  DATA_QUALITY_INSIGHTS_FILTER_ALL_MISSING_ATTRIBUTES,
  DATA_QUALITY_INSIGHTS_FILTER_ALL_IMPROVABLE_ATTRIBUTES,
  DATA_QUALITY_INSIGHTS_PRODUCT_SAVING,
  DATA_QUALITY_INSIGHTS_PRODUCT_SAVED,
  PRODUCT_MODEL_LEVEL_CHANGED,
  ProductEditFormApp,
  ProductModelEditFormApp,
  DATA_QUALITY_INSIGHTS_TAB_CONTENT_CONTAINER_ELEMENT_ID,
  fetchProductDataQualityEvaluation,
  fetchProductModelEvaluation,
  AttributeGroupDQIActivation,
  QualityScoreBar,
  DATA_QUALITY_INSIGHTS_PRODUCT_QUALITY_SCORE_CONTAINER_ELEMENT_ID,
  DATA_QUALITY_INSIGHTS_REDIRECT_TO_DQI_TAB,
  DictionaryApp,
  fetchSpellcheckEvaluation,
  ATTRIBUTES_TAB_CONTENT_CONTAINER_ELEMENT_ID,
  PRODUCT_ATTRIBUTES_TAB_NAME,
  PRODUCT_MODEL_ATTRIBUTES_TAB_NAME,
  PRODUCT_DATA_QUALITY_INSIGHTS_TAB_NAME,
  PRODUCT_MODEL_DATA_QUALITY_INSIGHTS_TAB_NAME,
  ProductEvaluation,
  CriterionEvaluationResult,
  AttributeEditFormApp,
  AttributeCreateFormApp,
  BACK_LINK_SESSION_STORAGE_KEY,
};