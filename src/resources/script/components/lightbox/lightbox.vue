<template>
<div @click.stop="closeLightBox">
    <div v-if="media && media.length > 0" v-show="lightBoxOn" ref="container" class="vue-lb-container">
        <div class="vue-lb-content">
            <div class="vue-lb-header">
                <span />
                <button v-if="closable" type="button" :title="closeText" class="vue-lb-button-close">
                    <slot name="close">
                        <i class="icon-cross"></i>
                    </slot>
                </button>
            </div>
            <div class="vue-lb-figure" @click.stop>
                <transition mode="out-in" name="fade">
                    <img v-if="media[select].type !== 'video'" :key="media[select][srcField]" :src="media[select][srcField]" :srcset="media[select].srcset || ''" class="vue-lb-modal-image" :alt="media[select].caption">
                    <video v-else ref="video" controls :width="media[select].width" :height="media[select].height">
                        <source v-for="source in media[select].sources" :key="source.src" :src="source.src" :type="source.type">
                    </video>
                </transition>

                <slot name="customCaption">
                    <div v-show="showCaption" class="vue-lb-info" v-html="media[select].caption" />
                </slot>

                <div class="vue-lb-footer">
                    <div class="vue-lb-footer-info" />
                    <div class="vue-lb-footer-count">
                        <slot name="footer" :current="select + 1" :total="media.length">
                            {{ select + 1 }} / {{ media.length }}
                        </slot>
                    </div>
                </div>
            </div>
        </div>
        <div class="vue-lb-thumbnail-wrapper">
            <div v-if="showThumbs" class="vue-lb-thumbnail">
                <button v-if="media.length > 1" type="button" class="vue-lb-thumbnail-arrow vue-lb-thumbnail-left" :title="previousThumbText" @click.stop="previousImage()">
                    <slot name="previousThumb">
                        <i class="el-icon-arrow-left"></i>
                    </slot>
                </button>

                <div v-for="(image, index) in imagesThumb" v-show="index >= thumbIndex.begin && index <= thumbIndex.end" :key="typeof image.thumb === 'string' ? `${image.thumb}${index}` : index" v-lazy:background-image="image.thumb" :class="'vue-lb-modal-thumbnail' + (select === index ? '-active' : '')"
                    @click.stop="showImage(index)">
                    <slot v-if="image.type" name="videoIcon">
                        <i>icon-video</i>
                    </slot>
                </div>

                <button v-if="media.length > 1" type="button" class="vue-lb-thumbnail-arrow vue-lb-thumbnail-right" :title="nextThumbText" @click.stop="nextImage()">
                    <slot name="nextThumb">
                        <i class="el-icon-arrow-right"></i>
                    </slot>
                </button>
            </div>
        </div>
        <button v-if="media.length > 1" type="button" class="vue-lb-arrow vue-lb-left" :title="previousText" @click.stop="previousImage()">
            <slot name="previous">
                <i class="el-icon-arrow-left"></i>
            </slot>
        </button>

        <button v-if="media.length > 1" type="button" class="vue-lb-arrow vue-lb-right" :title="nextText" @click.stop="nextImage()">
            <slot name="next">
                <i class="el-icon-arrow-right"></i>
            </slot>
        </button>
    </div>
</div>
</template>

<script>
// https://github.com/pexea12/vue-image-lightbox
let Hammer

if (typeof window !== 'undefined') {
    Hammer = require('hammerjs');
}

export default {
    data() {
        return {
            select: this.startAt,
            lightBoxOn: this.showLightBox,
            timer: null,
        }
    },

    props: {
        media: {
            type: Array,
            required: true,
        },

        disableScroll: {
            type: Boolean,
            default: true,
        },

        showLightBox: {
            type: Boolean,
            default: true,
        },

        closable: {
            type: Boolean,
            default: true,
        },

        startAt: {
            type: Number,
            default: 0,
        },

        nThumbs: {
            type: Number,
            default: 7,
        },

        showThumbs: {
            type: Boolean,
            default: true,
        },

        // Mode
        autoPlay: {
            type: Boolean,
            default: false,
        },

        autoPlayTime: {
            type: Number,
            default: 3000,
        },

        siteLoading: {
            type: Object,
            default: null,
        },

        showCaption: {
            type: Boolean,
            default: false,
        },

        lengthToLoadMore: {
            type: Number,
            default: 0
        },

        closeText: {
            type: String,
            default: 'Close (Esc)'
        },

        previousText: {
            type: String,
            default: 'Previous',
        },

        nextText: {
            type: String,
            default: 'Next',
        },

        previousThumbText: {
            type: String,
            default: 'Previous'
        },

        nextThumbText: {
            type: String,
            default: 'Next'
        },

        srcField: {
            type: String,
            default: 'src',
        },

        thumbField: {
            type: String,
            default: 'thumb',
        },

        typeField: {
            type: String,
            default: 'type',
        },
    },

    mounted() {
        if (this.autoPlay) {
            this.timer = setInterval(() => {
                this.nextImage()
            }, this.autoPlayTime)
        }

        this.onToggleLightBox(this.lightBoxOn)

        if (this.$refs.container) {
            const hammer = new Hammer(this.$refs.container)

            hammer.on('swiperight', () => {
                this.previousImage()
            })

            hammer.on('swipeleft', () => {
                this.nextImage()
            })
        }
    },

    beforeDestroy() {
        document.removeEventListener('keydown', this.addKeyEvent)

        if (this.autoPlay) {
            clearInterval(this.timer)
        }
    },

    watch: {
        lightBoxOn(value) {
            if (document != null) {
                this.onToggleLightBox(value)
            }
        },

        select() {
            if (this.select >= this.media.length - this.lengthToLoadMore - 1)
                this.$emit('onLoad')

            if (this.select === this.media.length - 1)
                this.$emit('onLastIndex')

            if (this.select === 0)
                this.$emit('onFirstIndex')

            if (this.select === this.startAt)
                this.$emit('onStartIndex')
        },
    },

    methods: {
        onToggleLightBox(value) {
            if (this.disableScroll) {
                if (value) {
                    document.querySelector('html').classList.add('no-scroll')
                } else {
                    document.querySelector('html').classList.remove('no-scroll')
                }
            }

            if (value) {
                document.querySelector('body').classList.add('vue-lb-open')
            } else {
                document.querySelector('body').classList.remove('vue-lb-open')
            }

            this.$emit('opened', value, this.select, this.media[this.select]);

            if (value) {
                document.addEventListener('keydown', this.addKeyEvent)
            } else {
                document.removeEventListener('keydown', this.addKeyEvent)
            }
        },

        showImage(index) {
            this.$set(this, 'lightBoxOn', true)
            this.$set(this, 'select', index)
        },

        addKeyEvent(event) {
            if (event.keyCode === 37) this.previousImage() // left arrow
            if (event.keyCode === 39) this.nextImage() // right arrow
            if (event.keyCode === 27) this.closeLightBox() // esc
        },

        closeLightBox() {
            if (this.$refs.video)
                this.$refs.video.pause();
            if (!this.closable) return;
            this.$set(this, 'lightBoxOn', false);
            this.$emit('closed');
        },

        nextImage() {
            this.$set(this, 'select', (this.select + 1) % this.media.length);
            this.$emit('changed', this.select, this.media[this.select]);
        },

        previousImage() {
            this.$set(this, 'select', (this.select + this.media.length - 1) % this.media.length);
            this.$emit('changed', this.select, this.media[this.select]);
        },
    },

    computed: {
        thumbIndex() {
            const halfDown = Math.floor(this.nThumbs / 2)

            if (this.select >= halfDown && this.select < this.media.length - halfDown)
                return {
                    begin: this.select - halfDown + (1 - this.nThumbs % 2),
                    end: this.select + halfDown,
                }

            if (this.select < halfDown)
                return {
                    begin: 0,
                    end: this.nThumbs - 1,
                }

            return {
                begin: this.media.length - this.nThumbs,
                end: this.media.length - 1,
            }
        },

        imagesThumb() {
            if (this.siteLoading) {
                return this.media.map(({
                    thumb,
                    type
                }) => ({
                    src: thumb,
                    type,
                    loading: this.siteLoading,
                    error: this.siteLoading,
                }))
            }

            return this.media.map((row) => {
                return {
                    thumb: row[this.thumbField],
                    type: row[this.typeField],
                };
            });
        },
    },
}
</script>

<style lang="scss">
.vue-lb-box {
    width: 100%;
}

.vue-lb-container {
    align-items: center;
    background-color: rgba(0, 0, 0, 0.8);
    box-sizing: border-box;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    height: 100%;
    justify-content: center;
    left: 0px;
    padding: 10px;
    position: fixed;
    top: 0px;
    width: 100%;
    z-index: 2000;
    -webkit-align-items: center;
    -moz-box-sizing: border-box;
    -webkit-justify-content: center;
    -ms-flex-align: center;
    -webkit-box-align: center;
    -ms-flex-pack: center;
    -webkit-box-pack: center;
}

.vue-lb-content {
    margin-bottom: 60px;
    max-width: 1024px;
    position: relative;
}

.vue-lb-header {
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    justify-content: space-between;
    height: 40px;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    -webkit-box-pack: justify;
}

.vue-lb-button-close {
    color: #fff;
    background: none;
    border: none;
    cursor: pointer;
    outline: none;
    position: relative;
    top: 0px;
    vertical-align: bottom;
    height: 40px;
    margin-right: -10px;
    padding: 10px;
    width: 40px;
}

.vue-lb-figure {
    margin: 0px;
    display: block;
    position: relative;
}

img.vue-lb-modal-image {
    cursor: pointer;
    max-height: calc(100vh - 140px);
    cursor: pointer;
    display: block;
    height: auto;
    margin: 0 auto;
    max-width: 100%;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.vue-lb-info {
    visibility: initial;
    position: absolute;
    bottom: 25px;
    color: white;
    background-color: rgba(0, 0, 0, 0.7);
    height: 40px;
    width: 100%;
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    text-align: center;
}

.vue-lb-footer {
    box-sizing: border-box;
    color: white;
    cursor: auto;
    display: -moz-box;
    display: -ms-flexbox;
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    justify-content: space-between;
    left: 0px;
    line-height: 1.3;
    padding-bottom: 5px;
    padding-left: 0px;
    padding-right: 0px;
    padding-top: 5px;
    -moz-box-sizing: border-box;
    -webkit-justify-content: space-between;
    -ms-flex-pack: justify;
    -webkit-box-pack: justify;
}

.vue-lb-footer-info {
    display: block;
    flex: 1 1 0;
    -webkit-flex: 1 1 0;
    -ms-flex: 1 1 0;
}

.vue-lb-footer-count {
    color: rgba(255, 255, 255, 0.75);
    font-size: 0.85em;
    padding-left: 1em;
}

.vue-lb-thumbnail {
    bottom: 10px;
    height: 50px;
    padding: 0 50px;
    text-align: center;
    white-space: nowrap;
    display: inline-block;
    position: relative;
}

.vue-lb-modal-thumbnail {
    background-position: center;
    background-size: cover;
    border-radius: 2px;
    box-shadow: inset 0 0 0 1px hsla(0, 0%, 100%, .2);
    cursor: pointer;
    display: inline-block;
    height: 50px;
    margin: 2px;
    overflow: hidden;
    width: 50px;
}

.vue-lb-modal-thumbnail-active {
    background-position: center;
    background-size: cover;
    border-radius: 2px;
    box-shadow: inset 0 0 0 2px white;
    cursor: pointer;
    display: inline-block;
    height: 50px;
    margin: 2px;
    overflow: hidden;
    width: 50px;
}

.vue-lb-thumbnail-arrow {
    color: #fff;
    font-size: 150%;
    height: 54px;
    width: 40px;
    background: none;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    outline: none;
    padding: 10px;
    position: absolute;
    top: 50%;
    -webkit-touch-callout: none;
    user-select: none;
    height: 50px;
    margin-top: -25px;
    width: 30px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.vue-lb-thumbnail-left {
    left: 10px;
}

.vue-lb-thumbnail-right {
    right: 10px;
}

.vue-lb-arrow {
    font-size: 250%;
    color: #fff;
    background: none;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    outline: none;
    padding: 10px;
    position: absolute;
    top: 50%;
    -webkit-touch-callout: none;
    user-select: none;
    height: 120px;
    margin-top: -60px;
    width: 40px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.vue-lb-left {
    left: 10px;
}

.vue-lb-right {
    right: 10px;
}

.vue-lb-open {
    overflow: hidden;
}

.vue-lb-thumbnail-wrapper {
    bottom: 10px;
    height: 50px;
    left: 0;
    margin: 0 auto;
    position: absolute;
    right: 0;
    text-align: center;
    top: auto;
}

@media (min-width: 500px) {
    .vue-lb-thumbnail-arrow {
        width: 40px;
    }
}

@media (min-width: 768px) {
    .vue-lb-arrow {
        width: 70px;
    }
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity .2s ease;
}

.fade-enter, .fade-leave-to {
    opacity: 0;
}

.no-scroll {
    overflow-y: hidden;
}
</style>
