@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/settings.css') }}">
@endsection

@section('content')
<div class="el-grid el-margin">
    <div class="el-width-expand@md">
        <h2 class="el-text-bold">Настройки</h2>
    </div>
    <div class="el-width-auto@md">
        <el-dropdown trigger="click">
            <el-button size="medium" class="is-narrow" plain type="primary"><i class="el-icon-menu"></i></el-button>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item>Action 1</el-dropdown-item>
                <el-dropdown-item>Action 2</el-dropdown-item>
                <el-dropdown-item>Action 3</el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</div>

<el-tabs class="el-margin" tab-position="left" v-model="currentGroupName" v-cloak>
    <el-tab-pane v-for="group in settings" :key="group.id" :name="group.code">
        <template slot="label"><i :class="group.icon"></i> <span v-text="group.name"></span></template>
        <div class="">
            <h3 class="el-text-bold el-margin-sm-bottom" v-text="group.name"></h3>
            <div class="el-text-muted" v-text="group.description"></div>
            <div class="el-grid el-margin">
                <div class="">
                    <el-link type="primary" @click.prevent="syncSetting(group)" icon="el-icon-plus22">Добавить настройку</el-link>
                </div>
                <div class="">
                    <el-link type="primary" @click.prevent="syncSettingGroup(group)" icon="el-icon-pencil6">Редактировать группу</el-link>
                </div>
            </div>
            <div class="preference-container el-margin-md">
                <form @submit.prevent="onSubmit($event, group)" v-if="group.settings && group.settings.length">
                    <section v-for="setting in group.settings" :key="setting.id">
                        <div class="">
                            <span class="el-text-bold el-margin-sm-right" v-text="setting.name"></span>
                            <el-tooltip content="Скопировать" placement="top">
                                <el-tag type="success" size="mini" class="el-cursor clipboard-copy" v-text="setting.key" :data-clipboard-text="setting.key"></el-tag>
                            </el-tooltip>
                            <el-link type="primary" class="el-float-right" @click.prevent="syncSetting(group, setting)" v-if="!setting.is_system">Редактировать</el-link>
                        </div>
                        <div class="el-margin-sm-top">
                            <template v-if="setting.type === 'input-text'">
                                <el-input size="medium" :prefix-icon="setting.icon" v-model="setting.value"></el-input>
                            </template>
                            <template v-else-if="setting.type === 'textarea'">
                                <el-input type="textarea" size="medium" :icon="setting.icon" v-model="setting.value"></el-input>
                            </template>
                            <template v-else-if="setting.type === 'input-number'">
                                <el-input-number size="medium" v-model="setting.value" style="max-width: 160px;"></el-input-number>
                            </template>
                            <template v-else-if="setting.type === 'toggle'">
                                <el-switch active-value="1" active-color="#13ce66" inactive-color="#ff4949" v-model="setting.value"></el-switch>
                            </template>
                            <template v-else-if="setting.type === RADIO_GROUP">
                                <el-radio-group v-model="setting.value" size="medium">
                                    <el-radio-button v-for="radio in setting.data" :key="radio.value" :label="radio.value">@{{radio.label}}</el-radio-button>
                                </el-radio-group>
                            </template>
                        </div>
                        <div class="el-text-muted el-text-sm el-margin-xs-top" v-html="setting.description"></div>
                    </section>
                    <div class="el-text-right el-margin-md-top">
                        <el-button type="primary" native-type="submit" :loading="state.isLoading">Сохранить</el-button>
                    </div>
                </form>
                <p class="blank-result" v-else>В группе нет настроек</p>
            </div>
        </div>
    </el-tab-pane>
    <div class="el-margin" slot="header">
        <el-button type="warning" icon="el-icon-plus22" class="el-width-1-1" @click="syncSettingGroup()" plain>Добавить группу</el-button>
    </div>
</el-tabs>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard-panel/script/settings.js') }}"></script>
@endsection
