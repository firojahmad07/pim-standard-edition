import React from 'react';
import {ReactController} from '@akeneo-pim-community/legacy-bridge/src/bridge/react';
import {DependenciesProvider} from '@akeneo-pim-community/legacy-bridge';
import {ThemeProvider} from 'styled-components';
import {pimTheme} from '@akeneo-pim-community/akeneo-design-system';
import {SettingsIndex} from '@akeneo-pim-community/settings-ui';

const mediator = require('oro/mediator');

class SettingsController extends ReactController {
  reactElementToMount() {
    return (
      <DependenciesProvider>
        {/* @ts-ignore */}
        <ThemeProvider theme={pimTheme}>
          <SettingsIndex />
        </ThemeProvider>
      </DependenciesProvider>
    );
  }

  routeGuardToUnmount() {
    return /pim_settings_index/;
  }

  renderRoute() {
    mediator.trigger('pim_menu:highlight:tab', {extension: 'pim-menu-settings'});
    mediator.trigger('pim_menu:hide', 'pim-menu-settings-column');

    return super.renderRoute();
  }
}

export = SettingsController;
