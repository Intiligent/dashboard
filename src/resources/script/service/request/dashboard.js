import api from '@dashboard/core/api/index.js';

/**
 * Fetch analytics data
 *
 * @param {Object} payload
 * @param {Object} state
 * @return {Promise}
 */
export function getAnalyticData(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.get(window.route('api.dashboard.getAnalyticData'), payload, Object.assign({
            notify: false,
        }, params, {
            onSuccess: ({data: response}) => resolve(response),
            onAbort: ({data: response}) => reject(response),
            state: state,
        }));
    });
};
