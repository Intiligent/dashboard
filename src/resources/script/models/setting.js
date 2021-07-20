import model from '@dashboard/core/model.js';
import '@dashboard/core/utilities/datetime.js';

export default function(data) {
    model(this, data, {
        id: null,
        group_id: null,
        key: null,
        value: null,
        name: null,
        description: null,
        type: 'input-text',
        icon: null,
        created_at: new Date().format('isoDateTime'),
        updated_at: new Date().format('isoDateTime'),
    });
};
