import ElMediaPreview from './src/preview.vue';

ElMediaPreview.install = function(Vue) {
    Vue.component(ElMediaPreview.name, ElMediaPreview);
};

export default ElMediaPreview;
