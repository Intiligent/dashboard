import {tap, guid} from '@dashboard/core/helper.js';
import { MessageBox } from 'element-ui';

const boundResolve = Promise.resolve.bind(Promise);
const DEFAULT_CONFIRM_OPTION = {
    modalCallback: boundResolve,
    customConfirm: boundResolve,
    onCancelOrReject: boundResolve,
    onConfirmSuccess: boundResolve,
};

/**
 * Create DOM tree for error description
 *
 * @param {Object} param
 * @param {String} param.errorText
 * @param {Number} param.rows
 * @returns DocumentFragment
 */
export const createErrorDescriptionDOM = function(param) {
    return tap(document.createDocumentFragment(), (descriptionFragment) => {
        if (param.descriptionText) {
            descriptionFragment.appendChild(tap(document.createElement('div'), (descriptionParagraphEl) => {
                descriptionParagraphEl.appendChild(document.createTextNode(param.descriptionText));
            }));
        }
        if (param.descriptionHTML) {
            descriptionFragment.appendChild(tap(document.createElement('div'), (descriptionParagraphEl) => {
                descriptionParagraphEl.appendChild(param.descriptionHTML);
            }));
        }
    });
};

/**
 * Get tree with the warning text for the modal window
 *
 * @param {Object} error
 * @param {String, Number} error.response.data.code
 * @param {String, Number} error.response.data.data.code
 *
 * @param {Array} groups
 *
 * @param {Object} props
 * @param {String} props.description
 * @param {String} props.title
 * @param {String} props.icon
 * @param {String} props.className
 *
 * @returns DocumentFragment
 */
export const getRequestWarningText = function(error, groups, props = {}) {
    let code = _.get(error, ['response', 'data', 'error', 'code']);
    let detailsCode = _.get(error, ['response', 'data', 'data', 'code']);
    let errorCode = detailsCode ? `${code}::${detailsCode}` : code;

    let descriptionEl = props.description || document.createTextNode(`Oops! Something unknown was happened and we were unable to save field change.
    The field value in DB will probably retain its original value. Unsaved field(s):`);
    let parts = [];

    if (props.title) {
        parts.push(tap(document.createElement('h3'), (headerEl) => {
            headerEl.setAttribute('class', 'uk-text-global');
            headerEl.appendChild(document.createTextNode(props.title));
        }).outerHTML);
    }

    if (props.icon) {
        parts.push(tap(document.createElement('i'), (iconEl) => {
            iconEl.setAttribute('class', `tm-modal-icon ${props.icon}`);
        }).outerHTML);
    }

    parts.push(tap(document.createElement('div'), (warningEl) => {
        warningEl.setAttribute('class', 'tm-modal-message-warning');

        // error description
        warningEl.appendChild(descriptionEl);

        // list of groups for retry
        warningEl.appendChild(tap(document.createElement('ul'), (groupsList) => {
            groups.forEach((group) => {
                groupsList.appendChild(tap(document.createElement('li'), (listItem) => {
                    listItem.appendChild(document.createTextNode(group));
                }));
            });
        }));

        // error code block
        warningEl.appendChild(tap(document.createElement('div'), (codeBlockEl) => {
            codeBlockEl.appendChild(tap(document.createElement('strong'), (codeBlockStrongEl) => {
                codeBlockStrongEl.appendChild(document.createTextNode('Code'));
            }));

            codeBlockEl.appendChild(document.createTextNode(': '));
            codeBlockEl.appendChild(tap(document.createElement('tt'), (codeBlockErrorCodeEl) => {
                codeBlockErrorCodeEl.setAttribute('class', 'uk-text-danger');
                codeBlockErrorCodeEl.appendChild(document.createTextNode(errorCode || '-'));
            }));
        }));

        // error time block
        warningEl.appendChild(tap(document.createElement('div'), (timeBlockEl) => {
            timeBlockEl.appendChild(tap(document.createElement('strong'), (timeBlockStrongEl) => {
                timeBlockStrongEl.appendChild(document.createTextNode('Time'));
            }));

            timeBlockEl.appendChild(document.createTextNode(': '));
            timeBlockEl.appendChild(tap(document.createElement('tt'), (timeBlockTimeEl) => {
                timeBlockTimeEl.appendChild(document.createTextNode(new Date())); // .format('YYYY/MM/DD HH:mm:ss Z')
            }));
        }));
    }).outerHTML);

    return parts.join('');
};

/**
 * Display a nice explaination for a user of what the hell of weird shit has just happened with the application
 *
 * @param {Object} props
 * @param {String} props.modal
 * @param {Object} props.labels
 * @param {String} props.labels.ok
 * @param {String} props.labels.cancel
 * @param {Function} props.onConfirm
 * @param {Function} props.onCancel
 * @param {String} props.description
 * @param {String} props.title
 * @param {String} props.icon
 * @param {String} props.className
 *
 * @param {Object} confirmOption
 * @param {Function} confirmOption.modalCallback
 * @param {Function} confirmOption.customConfirm
 * @param {Function} confirmOption.onConfirmSuccess
 * @param {Function} confirmOption.onCancelOrReject
 *
 * @param {Object} error
 * @param {String, Number} error.response.data.code
 * @param {String, Number} error.response.data.data.code
 *
 * @param {Array} groups
 *
 * @returns DocumentFragment
 */
export const warningModal = function (props, confirmOption = {}, error = {}, groups = []) {
    confirmOption = _.extend(DEFAULT_CONFIRM_OPTION, confirmOption);
    let modalOptions = _.extend({
        confirmButtonText: 'Retry',
        cancelButtonText: 'Cancel',
        dangerouslyUseHTMLString: true,
        closeOnClickModal: false,
        showClose: false,
    }, props.messageBoxOptions);

    const content = getRequestWarningText(error, groups, props);
    console.log('modalOptions', modalOptions);
    console.log('props', props);

    // let modal = MessageBox[props.modal || 'confirm'](getRequestWarningText(error, groups, props), props.name || 'Retry', modalOptions);
    let modal = MessageBox({
        title: props.name || 'Retry',
        type: props.modal || 'confirm',
        message: getRequestWarningText(error, groups, props),
        dangerouslyUseHTMLString: true,
        stack: true,
        showClose: true,
        showCancelButton: true,
        callback: (action) => {
            console.log('action then callback', action);

            return Promise.resolve();

            // return Promise.resolve(props.onConfirm && props.onConfirm())
            //     .then(confirmOption.onConfirmSuccess)
            //     .catch(confirmOption.onCancelOrReject);
        },
    }).then((action) => {
        console.log('modal then action', action);
    }).catch((action) => {
        console.log('action catch', action);

        // return 222;

        return Promise.resolve(props.onCancel && props.onCancel())
            .then(confirmOption.onCancelOrReject);
    });
    // let promptValue = null;

    console.log('modal', modal);

    // console.log('confirmOption', confirmOption);
    // console.log('modalCallback', confirmOption.modalCallback(modal));

    // props.onOpen && props.onOpen();

    return confirmOption.modalCallback(modal)
        .then((action) => {
            console.log('fff', action);
            console.log('action then', action);
            return Promise.resolve(props.onConfirm && props.onConfirm())
                .then(confirmOption.onConfirmSuccess)
                .catch(confirmOption.onCancelOrReject);
        }).catch((action) => {
            console.log('action catch', action);
            return Promise.resolve(props.onCancel && props.onCancel())
                .then(confirmOption.onCancelOrReject);
        });

    // return confirmOption.modalCallback(modal)
    //     .then(({value}) => {
    //         promptValue = value;
    //         return true;
    //     }, () => {
    //         return false;
    //     })
    //     .then(confirmOption.customConfirm)
    //     .then((isConfirm) => {
    //         if (isConfirm) {
    //             return Promise.resolve(props.onConfirm && props.onConfirm(promptValue))
    //                 .then(confirmOption.onConfirmSuccess)
    //                 .catch(confirmOption.onCancelOrReject);
    //         } else {
    //             return Promise.resolve(props.onCancel && props.onCancel())
    //                 .then(confirmOption.onCancelOrReject);
    //         }
    //     });
};
