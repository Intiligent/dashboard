import Vue from 'vue';

Vue.config.debug = true;
// должен ли Vue позволять Vue-devtools проводить инспекцию
Vue.config.devtools = true;
// Установите в false, чтобы отключить предупреждение о работе в режиме разработки при запуске Vue
Vue.config.productionTip = false;
// Устанавливает обработчик для ошибок, не пойманных во время рендеринга компонентов и в наблюдателях. Обработчик получит в параметрах ошибку и действующий экземпляр Vue.
Vue.config.errorHandler = function (err, vm, info) {
    // обработка ошибки
    // `info` это информация Vue-специфичной ошибки, например в каком хуке жизненного цикла
    // была найдена ошибка. Доступно только в версиях 2.2.0+
    console.error(err);
    console.log(vm);
    console.log(info);

    // add notify
};
// Назначает пользовательский обработчик предупреждений Vue во время выполнения
Vue.config.warnHandler = function (msg, vm, trace) {
    console.warn(msg);
    console.log(trace);
    // `trace` — это трассировка иерархии компонентов
};
