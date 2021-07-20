import model from '@dashboard/core/model.js';

export default function(data) {
    model(this, data, {
        id: null,
        name: null,
        email: null,
        phone: null,
        password: null,
    });
};
