import model from '@/core/model.js';

export default function(data) {
    model(this, data, {
        id: null,
        name: null,
        description: null,
        code: null,
        icon: null,
        order: 0,
        settings: [],
        created_at: new Date().toLocaleString(),
        updated_at: new Date().toLocaleString(),
    });
};
