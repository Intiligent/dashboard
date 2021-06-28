import api from '@/core/api/index.js';

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
 * Получение всех медиа файлов библиотеки (пагинация)
 *
 * @param {Object} data [модель меню]
 * @param {Object} state
 * @return {Promise}
 */
// export function postMenu(data = {}, state = {}, params = {}) {
//     return new Promise((resolve, reject) => {
//         api.post(window.route('api.dashboard.postMenu'), data, Object.assign({
//             notify: true,
//         }, params, {
//             onSuccess: (response) => resolve(response.data),
//             state: state,
//         }));
//     });
// };
