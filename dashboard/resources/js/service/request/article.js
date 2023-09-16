import axios from 'axios';
import api from '@dashboard/core/api';

export function getArticles(payload, config = {}) {
    return api.get(window.route('api.dashboard.getArticles'), payload, config);
}

export function postArticle(payload, config = {}) {
    return api.post(window.route('api.dashboard.postArticle'), payload, config);
}

export function deleteArticle(payload, config = {}) {
    return api.delete(window.route('api.dashboard.deleteArticle'), payload, config);
}
