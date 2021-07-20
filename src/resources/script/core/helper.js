/**
 * Выполнение с задержкой
 *
 * создать функцию исполнения операции operation
 * которая запускает operation, но не чаще чем задержка delay
 * и не раньше, чем завершился прошлый вызов operation
 * и в случае ожидания исполнения operation все последующие попытки объединяет в одну
 * и возвращает promise на текущую попытку
 *
 * если operation завершила своё последнее выполнение никогда или больше чем delay миллисекунд назад,
 * то операция будет выполнена немедленно
 *
 * если operation завершила своё последнее выполнение меньше, чем delay миллисекунд назад,
 * следующее выполнение и любые дополнительные запросы на него в течение delay будут отложены на оставшуюся часть delay
 *
 * этот хелпер отличается от Promise тем, что задаёт минимальный интервал запусков
 * этот хелпер отличается от _.debounce тем, что обязательно ожидает завершения последней запущенной операции
 *
 * flush возвращает Promise на последнюю запланированную операцию и убирает ожидание timeout по ней
 *
 * @param  {[type]} operation [description]
 * @param  {Number} [delay=0] [description]
 * @return {[type]}           [description]
 */
export function debouncePromise(operation, delay = 0) {
    let calledFirst = false;
    let calledAgain = false;
    let afterCalledFirst;
    let firstResultPromise;
    let delayer;
    let flush = function() {};

    if (delay === 0) {
        delayer = (...results) => {
            return Promise.resolve(...results);
        };
    } else {
        delayer = (...results) => {
            // сохраняет resolve от Promise на таймер.
            // при вызове этого резолв будет запущен последний Promise на операцию
            let deferred;
            // запоминаем id таймера чтобы можно было отменить ожидание
            // и немедленно запустить последний Promise на операцию
            let timerId;
            let promise = new Promise((resolve) => {
                deferred = resolve;
                timerId = setTimeout(() => {
                    deferred = undefined;
                    timerId = undefined;
                    resolve(...results);
                }, delay);
            });
            // реализуем свой аналог lodash debounce flush который работает с Promise
            // при вызове немедленно запустит последний ожидающий в очереди Promise
            // но только если он ждет timeout, иначе запустить после предыдущего Promise в очереди
            flush = function() {
                if (timerId) {
                    clearTimeout(timerId);
                    deferred(...results);
                }
            };
            return promise;
        };
    }

   // function is re-used inside itself - but asyncronously, not recursively
   function executor(bound, args) {
      if (calledFirst === false) {
          // on the first call execute immediately and make any next call considered first repeat
          calledAgain = false;

          // remember the unmodified result promise which doesn't have throttling delay
          firstResultPromise = Promise.resolve(operation.apply(bound, args));

          // after the execution place a delay so next calls within that delay will have to wait for it
          calledFirst = firstResultPromise.then(delayer);

          // after the first call resolved, make the next call first again
          afterCalledFirst = calledFirst.then(() => {
              calledFirst = false;
          });

          // on the first call return the promise without delay on it
          return firstResultPromise;
      } else if (calledAgain === false) {
          // it is not the first call and the first call is not yet resolved, but it is the first repeat
          // we need to make sure we won't lose it, and that we won't repeat two calls instead of one
          // so we set calledAgain and the next repeats will just return the first repeat promise again

          // after the first call is resolved, we call the executor again, thus first repeated call will become first call
          calledAgain = afterCalledFirst.then(() => executor(bound, args));
      }

      // otherwise it is a repeat, but not the first one - return the promise of the first repeat
      return calledAgain;
    };

    let executable = function(...args) {
        let bound = this;
        return executor(bound, args);
    };

    executable.flush = function() {
        flush();
        return calledAgain || afterCalledFirst;
    };

    return executable;
};

/**
 * Generate guid
 *
 * @return {String} [description]
 */
export function guid() {
    function s4() {
        return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
    }
    return s4() + s4() + '-' + s4() + '-' + s4() + '-' + s4() + '-' + s4() + s4() + s4();
};

export function tap(argument, callback) {
    callback(argument);
    return argument;
};

/**
 * First letter of a string uppercase
 *
 * @see https://flaviocopes.com/how-to-uppercase-first-letter-javascript/
 * @return {String} [modified string]
 */
String.prototype.ucfirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1)
}

// первый символ в верхнем регистре
// export function ucfirst(string) {
//     return string.charAt(0).toUpperCase() + string.slice(1);
// }

const library = {
    'Ё': 'Yo',
    'Й': 'I',
    'Ц': 'Ts',
    'У': 'U',
    'К': 'K',
    'Е': 'E',
    'Н': 'N',
    'Г': 'G',
    'Ш': 'Sh',
    'Щ': 'Sch',
    'З': 'Z',
    'Х': 'H',
    'Ъ': '',
    'ё': 'yo',
    'й': 'i',
    'ц': 'ts',
    'у': 'u',
    'к': 'k',
    'е': 'e',
    'н': 'n',
    'г': 'g',
    'ш': 'sh',
    'щ': 'sch',
    'з': 'z',
    'х': 'h',
    'ъ': '',
    'Ф': 'F',
    'Ы': 'I',
    'В': 'V',
    'А': 'a',
    'П': 'P',
    'Р': 'R',
    'О': 'O',
    'Л': 'L',
    'Д': 'D',
    'Ж': 'Zh',
    'Э': 'E',
    'ф': 'f',
    'ы': 'i',
    'в': 'v',
    'а': 'a',
    'п': 'p',
    'р': 'r',
    'о': 'o',
    'л': 'l',
    'д': 'd',
    'ж': 'zh',
    'э': 'e',
    'Я': 'Ya',
    'Ч': 'Ch',
    'С': 'S',
    'М': 'M',
    'И': 'I',
    'Т': 'T',
    'Ь': '',
    'Б': 'B',
    'Ю': 'Yu',
    'я': 'ya',
    'ч': 'ch',
    'с': 's',
    'м': 'm',
    'и': 'i',
    'т': 't',
    'ь': '',
    'б': 'b',
    'ю': 'yu',
    ' ': '-',
    '(': '',
    ')': '',
    '[': '',
    ']': '',
    '"': '',
    "'": '',
};

/**
 * [transliterate description]
 *
 * @param {String} text [description]
 * @param {String} [delimiter='-'] [description]
 * @return {String} [description]
 */
export function transliterate(text, delimiter = '-') {
    if (!text) {
        return '';
    }
    return text.split('').map((char) => {
        if (char === ' ') {
            return delimiter;
        }
        return library.hasOwnProperty(char) ? library[char] : char;
    }).join('');
};
