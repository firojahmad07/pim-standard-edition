import {channelsReceived} from 'akeneoreferenceentity/domain/event/channel';
import Channel from 'akeneoreferenceentity/domain/model/channel';
import hydrator from 'akeneoreferenceentity/application/hydrator/channel';
import hydrateAll from 'akeneoreferenceentity/application/hydrator/hydrator';
const fetcherRegistry = require('pim/fetcher-registry');

export const updateChannels = () => async (dispatch: any): Promise<any> => {
  return new Promise((resolve: any, reject: any) => {
    fetcherRegistry
      .getFetcher('channel')
      .fetchAll({filter_locales: false})
      .then((backendChannels: any[]) => {
        const channels = hydrateAll<Channel>(hydrator)(backendChannels);

        dispatch(channelsReceived(channels));
        resolve();
      })
      .fail(reject);
  });
};
