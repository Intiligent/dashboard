import axios from 'axios';
import api from '@dashboard/core/api';

export function postSetting(payload, config = {}) {
    return api.post(window.route('api.dashboard.postSetting'), payload, config);
}

export function postSettingUploadFile(payload, config = {}) {
    return api.post(window.route('api.dashboard.postSettingUploadFile'), payload, config);
}

export function putSettings(payload, config = {}) {
    return api.put(window.route('api.dashboard.putSettings'), payload, config);
}

export function deleteSetting(payload, config = {}) {
    return api.delete(window.route('api.dashboard.deleteSetting'), payload, config);
}

export function deleteSettingUploadFile(payload, config = {}) {
    return api.delete(window.route('api.dashboard.deleteSettingUploadFile'), payload, config);
}
