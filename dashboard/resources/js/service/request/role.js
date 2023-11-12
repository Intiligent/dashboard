import axios from 'axios';
import api from '@dashboard/core/api';

export function getRoles(payload, config = {}) {
    return api.get(window.route('api.dashboard.getRoles'), payload, config);
}

export function postRole(payload, config = {}) {
    return api.post(window.route('api.dashboard.postRole'), payload, config);
}

export function deleteRole(payload, config = {}) {
    return api.delete(window.route('api.dashboard.deleteRole'), payload, config);
}
