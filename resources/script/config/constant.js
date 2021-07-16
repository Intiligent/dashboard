
export const DEBUG_STYLE = {
    success: 'background: #e3f4ed; color: #247855; padding: 2px 5px; border-radius: 3px; margin-bottom: 2px;',
    warning: 'background: #ffeabf; color: #805600; padding: 2px 5px; border-radius: 3px; margin-bottom: 2px;',
    danger: 'background: #d9534f; color: #fff; padding: 2px 5px; border-radius: 3px; margin-bottom: 2px;',
    juice: 'background: #6554C0; color: #fff; padding: 2px 5px; border-radius: 3px; margin-bottom: 2px;',
    dark: 'background: #333; color: #fff; padding: 2px 5px; border-radius: 3px; margin-bottom: 2px;',
    default: 'background: #EBECF0; color: #172B4D; padding: 2px 5px; border-radius: 3px; margin-bottom: 2px',
};

// code number App\Exceptions\Handler
export const CODE_EXCEPTION_VALIDATION = 140001;
export const CODE_EXCEPTION_SQL_DEADLOCK = 140002;
export const CODE_EXCEPTION_INCOMPLETE_POST = 140004;
export const CODE_EXCEPTION_CSRF = 140101;
export const CODE_EXCEPTION_AUTHORIZATION = 140301;
export const CODE_EXCEPTION_UNAUTHORIZED = 140302;
export const CODE_EXCEPTION_ACCESS_DENIED = 140303;
export const CODE_EXCEPTION_LOCK_PROBLEMS = 140304;
export const CODE_EXCEPTION_WITH_MESSAGE = 140305;

// menu types
export const TYPE_URI = 'uri';
export const TYPE_ROUTE = 'route';
export const TYPE_ARTICLE = 'article';

export const EDITOR_CONFIG_DEFAULT = {
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
