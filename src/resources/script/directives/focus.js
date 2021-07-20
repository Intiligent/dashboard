import Vue from 'vue';

export default Vue.directive('focus', {
    inserted: function(el) {
        let input = el;
        setTimeout(function() {
            if (el.tagName !== 'INPUT') {
                input = el.getElementsByTagName('input')[0];
            }
            input.focus();
        }, 200);
    },

    // bind(el, binding, context) {
    //     console.log('el, binding', el, binding);
    //     console.log(context.componentInstance);
    //
    //     let input = el;
    //     if (el.tagName !== 'INPUT') {
    //         input = el.getElementsByTagName('input')[0];
    //     }
    //     context.componentInstance.$once('hook:attached', () => {
    //         console.log(input);
    //         input.focus();
    //     });
    // },
});
