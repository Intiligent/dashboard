import model from '@dashboard/core/model.js';

export default function(data) {
    model(this, data, {
        id: null,
        parent_id: null,
        name: null,
        slug: null,
        active: false,
    });
};
