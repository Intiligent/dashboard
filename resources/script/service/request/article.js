import api from '@/core/api/index.js';

/**
 * Создание/редактирование страницы
 *
 * @param {Object} payload [{sort, direction}]
 * @param {Object} state
 * @return {Promise}
 */
export function getArticles(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.get(window.route('api.dashboard.getArticles'), payload, Object.assign({
            notify: false,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Создание/редактирование страницы
 *
 * @param {Object} payload [Article model]
 * @param {Object} state
 * @return {Promise}
 */
export function postArticle(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postArticle'), payload, Object.assign({
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
export function postArticleDuplicate(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postArticleDuplicate'), payload, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Удаление страницы
 *
 * @param {Object} payload [Article model]
 * @param {Object} state
 * @return {Promise}
 */
export function deleteArticle(payload = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.delete(window.route('api.dashboard.deleteArticle'), payload, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};
