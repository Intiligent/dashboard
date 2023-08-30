import axios from 'axios';

export function postMenu(payload, state = {}) {
    return axios.post(window.route('api.dashboard.postMenu'), payload, {
        headers: {
            'X-CSRF-TOKEN': window.app ? window.app.csrf : null,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
    }).then((response) => {
        return response.data;
    }).catch((error) => {
        console.log(error);
        return error.response.data;
    });
}

export function deleteMenu(payload, state = {}) {
    return axios.delete(window.route('api.dashboard.deleteMenu'), {
        data: payload,
        headers: {
            'X-CSRF-TOKEN': window.app ? window.app.csrf : null,
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
        },
    }).then((response) => {
        return response.data;
    }).catch((error) => {
        console.log(error);
        return error.response.data;
    });
}
