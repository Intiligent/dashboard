import api from '@/core/api/index.js';

/**
 * Создание/редактирование меню
 *
 * @param  {Object} data [модель меню]
 * @param  {Object} state
 * @return {Promise}
 */
export function postMenu(data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postMenu'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Создание/редактирование группы меню
 *
 * @param  {Object} data [модель меню]
 * @param  {Object} state
 * @return {Promise}
 */
export function postMenuGroup(data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.post(window.route('api.dashboard.postMenuGroup'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            onError: (response) => reject(response.data),
            state: state,
        }));
    });
};

/**
 * Изменение позиции меню
 *
 * @param  {Object} data [модель меню]
 * @param  {Object} state
 * @return {Promise}
 */
export function putMenuOrder(data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.put(window.route('api.dashboard.putMenuOrder'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};

/**
 * Удаление пункта меню меню
 *
 * @param  {Object} data [модель меню]
 * @param  {Object} state
 * @return {Promise}
 */
export function deleteMenu(data = {}, state = {}, params = {}) {
    return new Promise((resolve, reject) => {
        api.delete(window.route('api.dashboard.deleteMenu'), data, Object.assign({
            notify: true,
        }, params, {
            onSuccess: (response) => resolve(response.data),
            state: state,
        }));
    });
};
