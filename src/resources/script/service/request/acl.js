import api from '@dashboard/core/api/index.js';

/**
 * Создание/редактирование страницы
 *
 * @param {Object} payload [{sort, direction}]
 * @param {Object} state
 * @return {Promise}
 */
// export function getArticles(payload = {}, state = {}, params = {}) {
//     return new Promise((resolve, reject) => {
//         api.get(window.route('api.dashboard.getArticles'), payload, Object.assign({
//             notify: false,
//         }, params, {
//             onSuccess: (response) => resolve(response.data),
//             state: state,
//         }));
//     });
// };

/**
 * Create/update role
 *
 * @param {Object} payload [Role model]
 * @param {Object} state
 * @return {Promise}
 */
export function postRole(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postRole'), payload, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Create/update permission
 *
 * @param {Object} payload [Permission model]
 * @param {Object} state
 * @return {Promise}
 */
export function postPermission(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postPermission'), payload, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Дублирование страницы
 *
 * @param {Object} payload [Article model]
 * @param {Object} state
 * @return {Promise}
 */
// export function postArticleDuplicate(payload = {}, state = {}, params = {}) {
//     return new Promise((resolve, reject) => {
//         api.post(window.route('api.dashboard.postArticleDuplicate'), payload, Object.assign({
//             notify: true,
//         }, params, {
//             onSuccess: (response) => resolve(response.data),
//             state: state,
//         }));
//     });
// };

/**
 * Удаление страницы
 *
 * @param {Object} payload [Article model]
 * @param {Object} state
 * @return {Promise}
 */
// export function deleteArticle(payload = {}, state = {}, params = {}) {
//     return new Promise((resolve, reject) => {
//         api.delete(window.route('api.dashboard.deleteArticle'), payload, Object.assign({
//             notify: true,
//         }, params, {
//             onSuccess: (response) => resolve(response.data),
//             state: state,
//         }));
//     });
// };
