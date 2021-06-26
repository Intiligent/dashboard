import api from '@/core/api/index.js';

/**
 * Получение списка пользователей
 *
 * @param {Object} payload [модель]
 * @param {Object} state
 * @return {Promise}
 */
export function getUsers(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.get(window.route('api.dashboard.getUsers'), payload, Object.assign({
            notify: false,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
            object: 'api.dashboard.postUser',
            state: state,
        }));
    });
};

/**
 * Создание/редактирование пользователя
 *
 * @param {Object} payload [модель]
 * @param {Object} state
 * @return {Promise}
 */
export function postUser(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postUser'), payload, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
            object: 'api.dashboard.postUser',
            state: state,
        }));
    });
};
