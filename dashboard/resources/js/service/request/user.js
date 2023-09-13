import axios from 'axios';
import api from '@dashboard/core/api';

export function getUsers(payload, config = {}) {
    return api.get(window.route('api.dashboard.getUsers'), payload, config);
}

export function postUser(payload, config = {}) {
    return api.post(window.route('api.dashboard.postUser'), payload, config);
}

export function postUserAvatar(payload, config = {}) {
    return api.post(window.route('api.dashboard.postUserAvatar'), payload, config);
}

export function deleteUser(payload, config = {}) {
    return api.delete(window.route('api.dashboard.deleteUser'), payload, config);
}
