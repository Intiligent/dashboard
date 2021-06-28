import api from '@/core/api/index.js';

/**
 * Создание/редактирование категории
 *
 * @param {Object} data
 * @param {Object} state
 * @return {Promise}
 */
const postCategory = function (data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postCategory'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Изменение позиции категории
 *
 * @param {Object} data
 * @param {Object} state
 * @return {Promise}
 */
const putCategoryOrder = function (data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.put(window.route('api.dashboard.putCategoryOrder'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Создание/редактирование меню
 *
 * @param  {Object} data [модель меню]
 * @param  {Object} state
 * @return {Promise}
 */
const deleteCategory = function (data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.delete(window.route('api.dashboard.deleteCategory'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

export {
    postCategory,
    putCategoryOrder,
    deleteCategory,
};
