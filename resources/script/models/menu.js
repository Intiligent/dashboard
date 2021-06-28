import model from '@/core/model.js';

export default function(data) {
    model(this, data, {
        id: null,
        parent_id: null,
        name: null,
        code: null,
        type: 'uri',
        value: null,
        attribute: null,
        icon: null,
        active: true,
        children: [],
    });

    this.active = Boolean(this.active);
};
