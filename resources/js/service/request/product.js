import api from '@/core/api/index.js';

/**
 * Создание/редактирование товара
 *
 * @param  {Object} data [модель товара]
 * @param  {Object} state
 * @return {Promise}
 */
const getProductSuggest = function (data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.get(window.route('api.dashboard.getProductSuggest'), data, Object.assign({
            // notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Создание/редактирование товара
 *
 * @param  {Object} data [модель товара]
 * @param  {Object} state
 * @return {Promise}
 */
const postProduct = function (data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postProduct'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Удаление товара
 *
 * @param  {Object} data [модель товара]
 * @param  {Object} state
 * @return {Promise}
 */
const deleteProduct = function (data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.delete(window.route('api.dashboard.deleteProduct'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

export {
    getProductSuggest,
    postProduct,
    deleteProduct,
};
