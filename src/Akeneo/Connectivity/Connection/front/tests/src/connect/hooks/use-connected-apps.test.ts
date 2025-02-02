import {renderHook} from '@testing-library/react-hooks';
import {mockFetchResponses} from '../../../test-utils';
import {useConnectedApps} from '@src/connect/hooks/use-connected-apps';
import {useFeatureFlags} from '@src/shared/feature-flags/use-feature-flags';
import {useNotify} from '@src/shared/notify';
import {NotificationLevel} from '@src/shared/notify';

jest.mock('@src/shared/feature-flags/use-feature-flags');
jest.mock('@src/shared/notify');

const notify = jest.fn();

beforeEach(() => {
    jest.clearAllMocks();
});

test('it returns an empty list if the feature flag is disabled', () => {
    (useFeatureFlags as jest.Mock).mockImplementation(() => ({isEnabled: () => false}));

    const {result} = renderHook(() => useConnectedApps());
    expect(result.current).toEqual([]);
});

test('it notifies if it cannot retrieve connected apps', async () => {
    (useFeatureFlags as jest.Mock).mockImplementation(() => ({isEnabled: () => true}));
    (useNotify as jest.Mock).mockImplementation(() => notify);

    const {result, waitForNextUpdate} = renderHook(() => useConnectedApps());
    await waitForNextUpdate();
    expect(result.current).toEqual(false);
    expect(notify).toBeCalledWith(
        NotificationLevel.ERROR,
        'akeneo_connectivity.connection.connect.connected_apps.list.flash.error'
    );
});

test('it does not fetch the marketplace apps if there is no connected apps', async () => {
    (useFeatureFlags as jest.Mock).mockImplementation(() => ({isEnabled: () => true}));

    mockFetchResponses({
        akeneo_connectivity_connection_apps_rest_get_all_connected_apps: {
            json: [],
        },
    });

    const {result, waitForNextUpdate} = renderHook(() => useConnectedApps());
    expect(result.current).toEqual(null);
    await waitForNextUpdate();
    expect(result.current).toEqual([]);
});

test('it does not fail if it cannot retrieve marketplace apps', async () => {
    (useFeatureFlags as jest.Mock).mockImplementation(() => ({isEnabled: () => true}));

    const connectedApp = {
        id: '0dfce574-2238-4b13-b8cc-8d257ce7645b',
        name: 'App A',
        scopes: ['scope A1'],
        connection_code: 'connectionCodeA',
        logo: 'http://www.example.com/path/to/logo/a',
        author: 'author A',
        user_group_name: 'app_123456abcde',
        categories: ['category A1', 'category A2'],
        certified: false,
        partner: 'partner A',
    };

    mockFetchResponses({
        akeneo_connectivity_connection_apps_rest_get_all_connected_apps: {
            json: [connectedApp],
        },
        akeneo_connectivity_connection_marketplace_rest_get_all_apps: {
            reject: true,
            json: {},
        },
    });

    const {result, waitForNextUpdate} = renderHook(() => useConnectedApps());
    expect(result.current).toEqual(null);
    await waitForNextUpdate();
    expect(result.current).toEqual([connectedApp]);
});

test('it fetches connected apps', async () => {
    (useFeatureFlags as jest.Mock).mockImplementation(() => ({isEnabled: () => true}));

    const connectedApp = {
        id: '0dfce574-2238-4b13-b8cc-8d257ce7645b',
        name: 'App A',
        scopes: ['scope A1'],
        connection_code: 'connectionCodeA',
        logo: 'http://www.example.com/path/to/logo/a',
        author: 'author A',
        user_group_name: 'app_123456abcde',
        categories: ['category A1', 'category A2'],
        certified: false,
        partner: 'partner A',
    };

    const marketplaceApp = {
        id: '0dfce574-2238-4b13-b8cc-8d257ce7645b',
        name: 'Extension 1',
        logo: 'http://www.example.com/path/to/logo/a',
        author: 'Partner 1',
        partner: 'Akeneo Partner',
        description: 'Our Akeneo Connector',
        url: 'https://marketplace.akeneo.com/extension/extension_1',
        categories: ['E-commerce'],
        certified: false,
        activate_url: 'https://example.com/activate',
        callback_url: 'https://example.com/oauth2',
    };

    const expectedApp = {
        ...connectedApp,
        activate_url: marketplaceApp.activate_url,
    };

    mockFetchResponses({
        akeneo_connectivity_connection_apps_rest_get_all_connected_apps: {
            json: [connectedApp],
        },
        akeneo_connectivity_connection_marketplace_rest_get_all_apps: {
            json: {
                total: 1,
                apps: [marketplaceApp],
            },
        },
    });

    const {result, waitForNextUpdate} = renderHook(() => useConnectedApps());
    expect(result.current).toEqual(null);
    await waitForNextUpdate();
    expect(result.current).toEqual([expectedApp]);
});
