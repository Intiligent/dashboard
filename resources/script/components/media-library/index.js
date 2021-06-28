import ElMediaPreview from './src/preview';

ElMediaPreview.install = function(Vue) {
    Vue.component(ElMediaPreview.name, ElMediaPreview);
};

export default ElMediaPreview;
