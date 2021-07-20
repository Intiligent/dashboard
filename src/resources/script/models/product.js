import model from '@dashboard/core/model.js';

export default function(data) {
    model(this, data, {
        id: null,
        category_id: null,
        name: null,
        price: null,
        active: false,
    });
};
