import model from '@/core/model.js';

export default function(data) {
    model(this, data, {
        id: null,
        title: null,
        slug: null,
        text: null,
        active: false,
    });
};
