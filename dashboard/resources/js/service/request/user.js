import axios from 'axios';
import api from '@dashboard/core/api';

export function getUsers(payload, config = {}) {
    return api.get(window.route('api.dashboard.getUsers'), payload, config);
}

// export function postMenu(payload, config = {}) {
//     return api.post(window.route('api.dashboard.postMenu'), payload, config);
// }

// export function deleteMenu(payload, state = {}) {
//     return api.delete(window.route('api.dashboard.deleteMenu'), payload, {
//         state,
//     });
// }
