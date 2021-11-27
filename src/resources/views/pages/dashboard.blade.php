@extends('dashboard::layouts.default')

@section('style')
<link rel="stylesheet" href="{{ mix('dashboard-panel/style/dashboard.css') }}">
@endsection

@section('content')
<div class="">
    <div v-if="!_.size(analytics) && state.isLoadingAnalytic" v-loading="state.isLoadingAnalytic" style="min-height: 200px;"></div>
    <el-empty description="Аналитических данных нет. Подключите Google Analytics" v-else-if="!_.size(analytics) && !state.isLoadingAnalytic"></el-empty>
    <div v-else>
        <div class="el-grid el-margin-xs">
            <div class="">
                <h3 class="el-text-bold">Активность пользователей</h3>
            </div>
            <div class="el-width-expand el-text-right">
                <el-date-picker
                    v-model="activityDate"
                    style="width: 250px;"
                    :picker-options="datePickerOptions"
                    type="daterange"
                    align="right"
                    size="small"
                    value-format="yyyy-MM-dd"
                    start-placeholder="Start Date"
                    end-placeholder="End Date"
                    @change="onDatepickerChange"
                >
                </el-date-picker>
            </div>
        </div>
        <div class="el-text-muted el-text-sm el-margin-bottom">данные за период <span v-text="period"></span> по данным <a target="_blank" href="https://analytics.google.com/analytics/web/">Google Analytics</a></div>

        <canvas id="totalVisitorsPageViews" height="120vh" class="el-margin-md-bottom" v-show="showVisitorsAndPageViews"></canvas>
        <el-empty description="No data" v-show="!showVisitorsAndPageViews"></el-empty>

        <div class="el-grid el-width-1-1 el-child-width-1-3 el-margin-md-bottom">
            <div class="">
                <h3 class="el-text-bold el-margin"><i class="el-icon-reply-all el-text-muted el-margin-sm-right" style="font-size: 20px;"></i>Переходы</h3>
                <ul class="el-list" v-if="topReferrers.length">
                    <li class="" style="margin-top: 12px;" v-for="referrer in topReferrers">
                        <span v-text="referrer.url"></span>
                        <span class="el-text-muted el-float-right" v-text="referrer.pageViews"></span>
                    </li>
                </ul>
                <div class="" v-else>Данных нет</div>
            </div>
            <div class="">
                <h3 class="el-text-bold el-margin"><i class="el-icon-users2 el-text-muted el-margin-sm-right" style="font-size: 20px;"></i>Посетители</h3>
                <template v-if="userTypes.length">
                    <div class="el-grid el-grid-sm el-width-1-1 el-flex el-flex-middle" v-for="type in userTypes">
                        <div class="el-width-expand">
                            <div v-text="type.type"></div>
                            <div class="el-text-sm el-text-muted el-margin-xs-top" v-text="type.description"></div>
                        </div>
                        <div class="el-width-auto" style="min-width: 52px;">
                            <span class="el-text-lg el-text-bold" :class="type.class" v-text="type.sessions"></span>
                        </div>
                    </div>
                </template>
                <div class="" v-else>Данных нет</div>
            </div>
            <div class="">
                <h3 class="el-text-bold el-margin"><i class="el-icon-mobile2 el-text-muted el-margin-sm-right" style="font-size: 20px;"></i>Устройства</h3>
                <canvas id="chartDevices" height="140" width="260"></canvas>
            </div>
        </div>

        <h3 class="el-text-bold">Какие страницы просматривают пользователи</h3>
        <el-table stripe class="el-width-1-1 el-margin" :data="mostVisitedPages" v-if="analytics.mostVisitedPages">
            <el-table-column label="View" width="60">
                <template slot-scope="{ row, column, $index }">
                    <el-tag size="mini" type="success" v-text="row.pageViews"></el-tag>
                </template>
            </el-table-column>
            <el-table-column label="Page title">
                <template slot-scope="{ row, column, $index }">
                    <a target="_blank" :href="row.url" v-text="row.pageTitle"></a>
                </template>
            </el-table-column>
            <el-table-column label="Url" width="120">
                <template slot-scope="{ row, column, $index }">
                    <span class="el-text-muted" v-text="row.url"></span>
                </template>
            </el-table-column>
        </el-table>
        <el-empty description="No data" v-else></el-empty>
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript" src="{{ mix('dashboard-panel/script/dashboard.js') }}"></script>
@endsection
