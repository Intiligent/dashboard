import {} from 'lodash';

const model = function ($this, data, structure) {
    function customizer(srcValue, objValue) {
        if (srcValue === undefined || srcValue === null) {
            return _.cloneDeep(objValue);
        }
        if (objValue === undefined || objValue === null) {
            return _.cloneDeep(srcValue);
        }
        if (_.isObject(objValue) || _.isObject(srcValue) || _.isArray(objValue)) {
            if (_.isArray(srcValue) && srcValue.length === 0) {
                return _.cloneDeep(objValue);
            } else {
                return _.mergeWith(_.cloneDeep(srcValue), objValue, customizer);
            }
        }
        return _.cloneDeep(srcValue);
    }

    if (structure === undefined) {
        _.merge($this, structure);
    } else {
        _.mergeWith($this, data, structure, customizer);
    }

    return $this;
};

export default model;
