<template lang="html">
    <component
        :is="currentComponent"
        :modelValue="modelValue"
        @update:modelValue="updateModelValue"
    ></component>
    <!-- v-model="$attrs.model.value" -->
</template>

<script>
import { h, computed } from 'vue';
import {
    ElIcon,
    ElInput,
    ElInputNumber,
    ElRadioButton,
    ElRadioGroup,
    ElSwitch,
    ElUpload,
} from 'element-plus';
import {
    deleteSettingUploadFile,
    postSettingUploadFile,
} from '@dashboard/service/request/settings';

export default {
    inheritAttrs: true,

    setup(props, { attrs }) {
        const modelValue = computed(() => {
            if (attrs.model.type === 'input-number') {
                return +attrs.model.value;
            }
            return attrs.model.value;
        });

        const updateModelValue = function(value) {
            attrs.model.value = value;
        };

        const handleFileSuccess = function(file, files) {
            console.log('[handleFileSuccess]', file, files);
        };

        const beforeFileUpload = function(file, files) {
            console.log('[beforeFileUpload]', file, files);
        };

        /**
         * Upload user avatar
         *
         * @param {Object} options [{action: String, data: Object, file: File, filename: String, headers: Object, method: String, onError: Function, onProgress: Function, onSuccess: Function, withCredentials: Boolean}]
         * @return {void}
         */
        const postSettingFileRequest = function(options) {
            const formData = new FormData();
            formData.append(options.filename, options.file);
            formData.append('id', attrs.model.id);

            postSettingUploadFile(formData, {
                // state: state.value,
                notify: true,
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            }).then((response) => {
                if (response.error === 201) {
                    attrs.model.value = response.data.path;
                }
            });
        };

        const currentComponent = computed(() => {
            if (attrs.model.type === 'textarea') {
                return h(ElInput, {
                    type: 'textarea',
                });
            }
            if (attrs.model.type === 'input-number') {
                return h(ElInputNumber, {
                    controlsPosition: 'right',
                });
            }
            if (attrs.model.type === 'switch') {
                return h(ElSwitch, {
                    style: '--el-switch-on-color: #13ce66;',
                    activeValue: '1',
                    inactiveValue: '0',
                });
            }
            if (attrs.model.type === 'file') {
                return h(ElUpload, {
                    class: 'avatar-uploader',
                    showFileList: false,
                    onSuccess: handleFileSuccess,
                    beforeUpload: beforeFileUpload,
                    httpRequest: postSettingFileRequest,
                }, {
                    default: () => {
                        return attrs.model.value ? [
                            h('img', {
                                class: 'avatar',
                                alt: attrs.model.title,
                                title: attrs.model.value,
                                src: attrs.model.value,
                            }),
                            h('i', {
                                title: 'Delete',
                                class: 'el-upload--delete el-icon-cross2',
                                onClick: function(event) {
                                    event.stopPropagation();
                                    if (!confirm('Are you sure you want delete file?')) {
                                        return false;
                                    }
                                    const payload = { id: attrs.model.id };
                                    deleteSettingUploadFile(payload, {
                                        notify: true,
                                    }).then((response) => {
                                        if (response.error === 200) {
                                            attrs.model.value = null;
                                        }
                                    });
                                },
                            }),
                        ] : h(ElIcon, {
                            class: 'avatar-uploader-icon',
                        }, {
                            default: () => {
                                return h('i', {
                                    class: 'el-icon-plus3',
                                });
                            },
                        });
                    },
                });
            }
            if (attrs.model.type === 'radio-group') {
                return h(ElRadioGroup, {}, {
                    default: () => {
                        return [0, 1, 2].map((index) => {
                            return h(ElRadioButton, {
                                label: 'item-' + index,
                            });
                        });
                    },
                });
            }
            return h(ElInput, {
                size: 'large',
            }, {
                prefix: () => {
                    return attrs.model.icon && h('i', {
                        class: attrs.model.icon,
                    });
                },
            });
        });

        return {
            currentComponent,
            modelValue,
            updateModelValue,
        };
    },
}
</script>

<style lang="scss">
@use '~element-plus/theme-chalk/src/mixins/function' as *;

@use '~element-plus/theme-chalk/src/input-number';
@use '~element-plus/theme-chalk/src/switch';
@use '~element-plus/theme-chalk/src/upload';
@use '~element-plus/theme-chalk/src/radio-group';
@use '~element-plus/theme-chalk/src/radio-button';

.avatar-uploader {
    .el-upload {
        position: relative;
        border: 1px dashed var(--el-border-color);
        border-radius: getCssVar('border-radius', 'base');
        cursor: pointer;
        transition: var(--el-transition-duration-fast);
        max-width: 100px;
        max-height: 100px;
        width: 100%;
        height: 100px;
        &:hover {
            border-color: var(--el-color-primary);
            .avatar-uploader-icon {
                color: var(--el-color-primary);
            }
        }
        .avatar-uploader-icon {
            font-size: 24px;
            color: #8c939d;
        }
        img {
            max-width: 100%;
            max-height: 100%;
        }
        .el-upload--delete {
            position: absolute;
            background-color: #fff;
            padding: 4px;
            top: 0;
            right: 0;
            border-radius: 4px;
            opacity: 0.7;
            transition: var(--el-transition-duration-fast);
            &:hover {
                opacity: 1;
            }
        }
    }
}
</style>
