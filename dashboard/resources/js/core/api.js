import axios from 'axios';
import { API_DEFAULT_TIMEOUT } from '@dashboard/config/constant';
import { ElNotification } from 'element-plus';

// allow use http client without Vue instance
export const http = axios.create({
    headers: {
        'X-CSRF-TOKEN': window.app?.csrf
            ?? document.head.querySelector('[name=csrf]')?.content
            ?? null,
        'X-Requested-With': 'XMLHttpRequest',
    },
    timeout: API_DEFAULT_TIMEOUT,
});

const requestInterceptorSuccess = function(request) {
    console.log('[requestInterceptorSuccess]', request);
    if (request.state) {
        request.state.isLoading = true;
    }
    return request;
};

const requestInterceptorError = function(error) {
    console.log('[requestInterceptorError]', error);
    // Do something with request error
    return Promise.reject(error);
};

// Add a request interceptor
http.interceptors.request.use(requestInterceptorSuccess, requestInterceptorError);

const responseInterceptorSuccess = function(response) {
    console.log('[responseInterceptorSuccess]', response);
    if (response.config.state) {
        response.config.state.isLoading = false;
    }
    if (response.config.notify) {
        ElNotification({
            title: 'Success',
            message: response.data.message,
            type: 'success',
        });
    }
    // Any status code that lie within the range of 2xx cause this function to trigger
    // Do something with response data
    return response;
};

const responseInterceptorError = function(error) {
    console.log('[responseInterceptorError]', error);
    // Any status codes that falls outside the range of 2xx cause this function to trigger
    // Do something with response error
    return Promise.reject(error);
};

// Add a response interceptor
http.interceptors.response.use(responseInterceptorSuccess, responseInterceptorError);

const api = {
    get: function(url, payload, config = {}) {
        config.params = payload || null;
        return http.get(url, config).then((response) => {
            return response.data;
        }).catch((error) => {
            return error.response.data;
        });
    },

    post: function(url, payload, config = {}) {
        return http.post(url, payload, config).then((response) => {
            return response.data;
        }).catch((error) => {
            return error.response.data;
        });
    },

    put: function(url, payload, config = {}) {
        return http.put(url, payload, config).then((response) => {
            return response.data;
        }).catch((error) => {
            return error.response.data;
        });
    },

    delete: function(url, payload, config = {}) {
        config.data = payload || null;
        return http.delete(url, config).then((response) => {
            return response.data;
        }).catch((error) => {
            return error.response.data;
        });
    },
};

export default api;
