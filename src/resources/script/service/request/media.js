import api from '@dashboard/core/api/index.js';

/**
 * Получение всех медиа файлов библиотеки (пагинация)
 *
 * @return {Promise}
 */
export function getMediaLibrary(data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.get(window.route('api.dashboard.getMediaLibrary'), data, Object.assign({
            notify: false,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
            state: state,
        }));
    });
};

/**
 * Загрузка файла по ссылке
 *
 * @param {Object} payload
 * @param {Numbe} payload.model_id [model id]
 * @param {String} payload.model_type [model name]
 * @param {String} payload.entity
 * @param {Boolean} payload.multiple
 * @param {String} payload.value
 * @return {Promise} (response)
 */
export function postMediaUploadFromUrl(payload = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(route('api.dashboard.postMediaUploadFromUrl').url(), payload, Object.assign({
            notify: false,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
        }));
    });
};

/**
 * Удаление медиа файла
 *
 * @param {Object} payload
 * @param {Number|Array} payload.id [model id]
 * @return {Promise} (response)
 */
export function deleteMedia(payload = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.delete(route('api.dashboard.deleteMedia').url(), payload, Object.assign({
            notify: false,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onAbort: (response) => reject(response.data),
        }));
    });
};
