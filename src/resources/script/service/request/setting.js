import api from '@dashboard/core/api/index.js';

/**
 * Создание/редактирование настройки
 *
 * @param {Object} data [модель]
 * @param {Object} state
 * @return {Promise}
 */
export function postSetting(data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {

        console.log('api.post');
        api.post(window.route('api.dashboard.postSetting'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
            state: state,
            object: 'api.dashboard.postSetting',
        }));
    });
};

/**
 * Создание/редактирование группы настройки
 *
 * @param {Object} data [модель]
 * @param {Object} state
 * @return {Promise}
 */
export function postSettingGroup(data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postSettingGroup'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
            state: state,
            object: 'api.dashboard.postSettingGroup',
        }));
    });
};

/**
 * Создание/редактирование настройки
 *
 * @param {Object} payload [данные]
 * @param {Number} payload.*.id
 * @param {String} payload.*.key
 * @param {String} payload.*.value
 * @param {Object} state
 * @return {Promise}
 */
export function putSettings(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.put(window.route('api.dashboard.putSettings'), payload, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
            object: 'api.dashboard.putSettings',
            state: state,
        }));
    });
};

/**
 * Удаление настройки
 *
 * @param {Object} payload [{id}]
 * @param {Object} state
 * @return {Promise}
 */
export function deleteSetting(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.delete(window.route('api.dashboard.deleteSetting'), payload, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
            state: state,
        }));
    });
};

/**
 * Удаление группы настроек
 *
 * @param {Object} payload [{id}]
 * @param {Object} state
 * @return {Promise}
 */
export function deleteSettingGroup(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.delete(window.route('api.dashboard.deleteSettingGroup'), payload, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
            state: state,
        }));
    });
};
