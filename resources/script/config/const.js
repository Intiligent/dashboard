// menu types
const TYPE_URI = 'uri';
const TYPE_ROUTE = 'route';
const TYPE_ARTICLE = 'article';

const EDITOR_CONFIG_DEFAULT = {
    height: 320,
    toolbarGroups: [
        { name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        { name: 'clipboard', groups: ['clipboard', 'undo']},
        { name: 'links'},
        { name: 'insert'},
        { name: 'editing', groups: ['find', 'selection']},
        { name: 'forms'},
        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi']},
        { name: 'document', groups: ['mode', 'document', 'doctools']},
        { name: 'styles'},
        { name: 'colors'},
        { name: 'others'},
        { name: 'tools'},
    ],
};

export {
    TYPE_URI,
    TYPE_ROUTE,
    TYPE_ARTICLE,

    EDITOR_CONFIG_DEFAULT,
};
