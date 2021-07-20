import {
    Notification,
    MessageBox,
} from 'element-ui';

/**
 * Получить текст сообщения из ответа запроса
 *
 * @param  {Object} error [description]
 * @return {String}       [description]
 */
export const getErrorMessage = function(error) {
    return _.get(error, ['response', 'message']) || _.get(error, ['response', 'data', 'message']) || _.get(error, 'message');
};

/**
 * Получить сообщение об ошибке
 *
 * @param  {Object} error [description]
 * @return {String}       [description]
 */
export const getErrorText = function(error) {
    let errorParts = [];
    let status = _.get(error, ['response', 'status']);
    let errorMessage = getErrorMessage(error);
    let trace = _.get(error, ['response', 'data', 'debug']);

    if (status) {
        errorParts.push(`HTTP Status: ${status}`);
    }
    if (errorMessage) {
        errorParts.push(errorMessage);
    } else {
        errorParts.push('unidentified error');
    }
    if (trace) {
        errorParts.push(JSON.stringify(trace, null, '   '));
    }
    return errorParts.join('\n');
};

export default function Notice() {
    /**
     * Вывод всплывающего уведомления
     *
     * @param  {String} message [текст сообщения]
     * @param  {Object} params [параметры уведомления]
     * @return {void}
     */
    this.notification = function(message, params = {}) {
        let options = Object.assign({
            type: 'info',
            title: 'Info',
            message: 'default error message',
            offset: 70,
            duration: 3000,
        }, params, {
            message: message,
            title: params.title || params.type.ucfirst(),
        });

        Notification(options);
    };

    /**
     * Вывод уведомлений валидации
     *
     * @param  {Object} error [Error response]
     * @return {void}
     */
    this.validation = function(error) {
        let response = _.get(error, ['response', 'data'], []);
        let messages = _.flatMap(_.get(response, 'data', []), (n) => n).map((msg) => {
            return '• ' + msg.message;
        }).join('\n');

        Notification({
            type: 'warning',
            title: response.message,
            message: messages,
            offset: 70,
            duration: 5000,
        });
    };

    /**
     * Вызов модального окна с текстом сообщения
     *
     * @param  {String} message [текст сообщения]
     * @return {[type]}         [description]
     */
    this.dialog = function(message) {
        console.log('fire dialog', message);

        return MessageBox({
            message: message || 'Waiting for pending requests...',
            showCancelButton: false,
            center: true,
        })
    };
};
