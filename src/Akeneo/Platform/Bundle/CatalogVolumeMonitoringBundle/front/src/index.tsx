import React from 'react';
import ReactDOM from 'react-dom';
import {ThemeProvider} from 'styled-components';
import {pimTheme} from '@akeneo-pim-community/akeneo-design-system';
import {MicroFrontendDependenciesProvider, Routes, Translations} from '@akeneo-pim-community/shared';
import {routes} from './routes.json';
import translations from './translations.json';
import {CatalogVolumeMonitoringApp} from './feature';
import {FakePIM} from './FakePIM';
import {getMockCatalogVolume} from './feature';

