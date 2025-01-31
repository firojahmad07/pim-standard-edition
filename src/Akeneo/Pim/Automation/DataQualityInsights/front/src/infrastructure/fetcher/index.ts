import fetchIgnoreTextIssue from './ProductEditForm/Spellcheck/fetchIgnoreTextIssue';
import fetchTextAnalysis from './Spellcheck/fetchTextAnalysis';
import {fetchSpellcheckSupportedLocales} from './ProductEditForm/Spellcheck/fetchSpellcheckSupportedLocales';
import {fetchLocaleDictionary} from './Locale/Dictionary/fetchLocaleDictionary';
import {deleteWordFromLocaleDictionary} from './Locale/Dictionary/deleteWordFromLocaleDictionary';
import {addWordsToLocaleDictionary} from './Locale/Dictionary/addWordsToLocaleDictionary';
import fetchDqiDashboardData from './Dashboard/fetchDqiDashboardData'

import fetchProductDataQualityEvaluation from './ProductEditForm/fetchProductDataQualityEvaluation';
import fetchFamilyInformation from './ProductEditForm/fetchFamilyInformation';
import fetchProduct from './ProductEditForm/fetchProduct';
import {fetchAllAttributeGroupsDqiStatus} from './AttributeGroup/attributeGroupDqiStatusFetcher';
import {fetchAttributeGroupsByCode} from './AttributeGroup/attributeGroupsFetcher';
import {fetchKeyIndicators} from './Dashboard/fetchKeyIndicators';
import {fetchQualityScoreEvolution} from './Dashboard/fetchQualityScoreEvolution';

export {
  fetchIgnoreTextIssue,
  fetchDqiDashboardData,
  fetchTextAnalysis,
  fetchSpellcheckSupportedLocales,
  fetchLocaleDictionary,
  deleteWordFromLocaleDictionary,
  addWordsToLocaleDictionary,
  fetchProductDataQualityEvaluation,
  fetchFamilyInformation,
  fetchProduct,
  fetchAllAttributeGroupsDqiStatus,
  fetchAttributeGroupsByCode,
  fetchKeyIndicators,
  fetchQualityScoreEvolution,
};
