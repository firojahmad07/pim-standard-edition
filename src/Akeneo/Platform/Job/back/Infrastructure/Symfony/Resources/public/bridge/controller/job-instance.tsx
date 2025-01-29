import BaseView = require('pimui/js/view/base');

import {JobInstanceDetail, JobInstanceDetailProps} from '@akeneo-pim-community/process-tracker/src';

class JobInstance extends BaseView {
  /**
   * {@inheritdoc}
   */
  render(): BaseView {
    const {code, type} = this.getFormData();

    this.renderReact<JobInstanceDetailProps>(JobInstanceDetail, {code, type}, this.el);

    return this;
  }
}

export default JobInstance;
