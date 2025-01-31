import ReactDOM from 'react-dom';
import React from 'react';
import {DashboardHelper} from '@akeneo-pim-community/data-quality-insights';
import {Dashboard} from '@akeneo-pim-community/data-quality-insights';
import {TimePeriod} from '@akeneo-pim-community/data-quality-insights';

const UserContext = require('pim/user-context');
const BaseDashboard = require('akeneo/data-quality-insights/view/dqi-dashboard/base-dashboard');

class SectionView extends BaseDashboard {
  render() {
    const catalogLocale: string = UserContext.get('catalogLocale');
    const catalogChannel: string = UserContext.get('catalogScope');

    ReactDOM.render(
      <div>
        <DashboardHelper />
        <Dashboard
          timePeriod={this.timePeriod as TimePeriod}
          catalogLocale={catalogLocale}
          catalogChannel={catalogChannel}
          familyCode={this.familyCode}
          categoryCode={this.categoryCode}
          categoryId={this.categoryId}
          rootCategoryId={this.rootCategoryId}
          axes={this.axes}
        />
      </div>,
      this.el
    );
  }
}

export = SectionView;
