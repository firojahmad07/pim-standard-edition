import useFetchProductDataQualityEvaluation from '@akeneo-pim-community/data-quality-insights/src/infrastructure/hooks/ProductEditForm/useFetchProductDataQualityEvaluation';
import useProductFamily from '@akeneo-pim-community/data-quality-insights/src/infrastructure/hooks/ProductEditForm/useProductFamily';
import useCatalogContext from '@akeneo-pim-community/data-quality-insights/src/infrastructure/hooks/ProductEditForm/useCatalogContext';
import useProduct from '@akeneo-pim-community/data-quality-insights/src/infrastructure/hooks/ProductEditForm/useProduct';
import usePageContext from './ProductEditForm/usePageContext';

import useProductEvaluation from '@akeneo-pim-community/data-quality-insights/src/infrastructure/hooks/ProductEditForm/useProductEvaluation';

import useGetWidgetsList from './EditorHighlight/useGetWidgetsList';
import useGetEditorBoundingRect from './EditorHighlight/useGetEditorBoundingRect';
import useGetEditorScroll from './EditorHighlight/useGetEditorScroll';
import useGetHighlights from './EditorHighlight/useGetHighlights';
import useFetchTextAnalysis from './EditorHighlight/Spellcheck/useFetchTextAnalysis';
import useGetPopover from './EditorHighlight/useGetPopover';
import useGetWidget from './EditorHighlight/useGetWidget';
import useFetchIgnoreTextIssue from './EditorHighlight/Spellcheck/useFetchIgnoreTextIssue';
import {useGetSpellcheckSupportedLocales} from './Common/useGetSpellcheckSupportedLocales';
import {useLocaleDictionary, DictionaryState} from './Locale/Dictionary/useLocaleDictionary';
import {useDictionaryState} from './Locale/Dictionary/useDictionaryState';

import useFetchDqiDashboardData from './Dashboard/useFetchDqiDashboardData'
import useGetChartScalingSizeRatio from './Dashboard/useGetChartScalingSizeRatio';
import {useFetchProductQualityScore} from './ProductEditForm/useFetchProductQualityScore';
import {useFetchKeyIndicators} from './Dashboard/useFetchKeyIndicators';
import {RawScoreEvolutionData, useFetchQualityScoreEvolution} from './Dashboard/useFetchQualityScoreEvolution';

export * from './AttributeGroup';


export {
  useFetchProductDataQualityEvaluation,
  useGetChartScalingSizeRatio as useGetDashboardChartScalingSizeRatio,
  useFetchProductQualityScore,
  useFetchKeyIndicators,
  RawScoreEvolutionData,
  useFetchQualityScoreEvolution,
  useProductFamily,
  useCatalogContext,
  useProduct,
  usePageContext,
  useFetchDqiDashboardData,
  useGetWidgetsList as useGetEditorHighlightWidgetsList,
  useGetEditorBoundingRect as useGetEditorHighlightBoundingRect,
  useGetEditorScroll as useGetEditorHighlightScroll,
  useGetHighlights as useGetEditorHighlights,
  useFetchTextAnalysis as useFetchSpellcheckTextAnalysis,
  useGetPopover as useGetEditorHighlightPopover,
  useGetWidget as useGetSpellcheckWidget,
  useFetchIgnoreTextIssue,
  useProductEvaluation,
  useGetSpellcheckSupportedLocales,
  useLocaleDictionary,
  DictionaryState,
  useDictionaryState,
};
