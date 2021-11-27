import Vue from 'vue';
import api from '@dashboard/core/api/index.js';
import {
    ButtonGroup,
    Breadcrumb,
    BreadcrumbItem,
    DatePicker,
    Empty,
    Loading,
    Table,
    TableColumn,
    Tag,
    Tooltip,
    Avatar,
} from 'element-ui';
import {
    ArcElement,
    Chart,
    BarController,
    BarElement,
    CategoryScale,
    DoughnutController,
    Legend,
    LinearScale,
    Tooltip as TooltipChart,
} from 'chart.js';
import baseDashboard from '@dashboard/mixins/baseDashboard.js';
import { getAnalyticData } from '@dashboard/service/request/dashboard.js';

Vue.use(Loading.directive);
Chart.register(
    ArcElement,
    BarElement,
    BarController,
    CategoryScale,
    DoughnutController,
    Legend,
    LinearScale,
    TooltipChart,
);

new Vue({
    el: '#root',

    mixins: [baseDashboard],

    components: {
        ElButtonGroup: ButtonGroup,
        ElBreadcrumb: Breadcrumb,
        ElBreadcrumbItem: BreadcrumbItem,
        ElDatePicker: DatePicker,
        ElEmpty: Empty,
        ElTable: Table,
        ElTableColumn: TableColumn,
        ElTag: Tag,
        ElTooltip: Tooltip,
        ElAvatar: Avatar,
    },

    data: function() {
        return {
            activityDate: null,
            analytics: {},
            state: {
                isLoading: false,
                isLoadingAnalytic: false,
            },
        }
    },

    created() {
        this.chartVisitors;
        this.chartDevices;
        this.fetchAnalyticData();
    },

    methods: {
        onDatepickerChange: function(value) {
            const payload = value ? {
                from: value[0],
                to: value[1],
            } : null;
            this.fetchAnalyticData(payload);
        },

        /**
         * Fetch analytics data
         *
         * @param {Object} payload
         * @param {Boolean} value [value after changing]
         * @return {Promise}
         */
        fetchAnalyticData: function(payload = {}) {
            this.state.isLoading = true;
            return getAnalyticData(payload, this.state).then((response) => {
                this.state.isLoading = false;
                this.analytics = response.data || {};
            });
        },

        drawChartVisitorsPageView: function(value) {
            if (!value) return;
            if (this.chartVisitors) {
                this.chartVisitors.destroy();
            }
            const ctx = document.getElementById('totalVisitorsPageViews');
            const gradientView = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
            gradientView.addColorStop(0, 'rgba(46, 142, 255, 1)');
            gradientView.addColorStop(1, 'rgba(46, 142, 255, 0.3)');
            const gradientUser = ctx.getContext('2d').createLinearGradient(0, 0, 0, 350);
            gradientUser.addColorStop(0, 'rgba(144, 147, 153, 1)');
            gradientUser.addColorStop(1, 'rgba(144, 147, 153, 0.3)');
            const chartOption = {
                type: 'bar',
                data: {
                    labels: _.map(value, 'date'),
                    datasets: [
                        {
                            label: 'Посетители',
                            data: _.map(value, 'visitors'),
                            backgroundColor: gradientUser, // "#909399",
                            borderWidth: 0,
                        },
                        {
                            label: 'Просмотры',
                            data: _.map(value, 'pageViews'),
                            backgroundColor: gradientView, // "#2e8eff",
                            borderWidth: 0,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1000,
                    },
                    plugins: {
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            cornerRadius: 4,
                            boxPadding: 3,
                            usePointStyle: true,
                        },
                        legend: {
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                font: {
                                    fontFamily: '-apple-system, BlinkMacSystemFont, Segoe UI, "Helvetica Neue", Arial',
                                    size: 14,
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: {
                                color: '#f1f1f1',
                            },
                        },
                        y: {
                            grid: {
                                color: '#f1f1f1',
                            },
                        },
                    },
                },
            };
            this.chartVisitors = new Chart(ctx, chartOption);
        },

        drawChartDevices: function(value) {
            if (!value) return;
            if (this.chartDevices) {
                this.chartDevices.destroy();
            }
            const ctx = document.getElementById('chartDevices');
            const gradient1 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 150);
            gradient1.addColorStop(0, 'rgba(46, 142, 255, 1)');
            gradient1.addColorStop(1, 'rgba(46, 142, 255, 0.3)');
            const gradient2 = ctx.getContext('2d').createLinearGradient(0, 0, 0, 150);
            gradient2.addColorStop(0, 'rgba(144, 147, 153, 1)');
            gradient2.addColorStop(1, 'rgba(144, 147, 153, 0.3)');
            const CHART_COLORS = [
                gradient1,
                gradient2,
                '#ffce56',
            ];
            const chartOptions = {
                type: 'doughnut',
                data: {
                    labels: value.map((item) => `${item.type} (${item.sessions})`),
                    datasets: [{
                        data: value.map((item) => item.sessions),
                        backgroundColor: CHART_COLORS,
                    }],
                },
                options: {
                    responsive: false,
                    aspectRatio: 1,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            align: 'start',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                font: {
                                    fontFamily: '-apple-system, BlinkMacSystemFont, Segoe UI, "Helvetica Neue", Arial',
                                    size: 14,
                                },
                                usePointStyle: true,
                            },
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            cornerRadius: 4,
                            boxPadding: 3,
                            usePointStyle: true,
                        },
                    },
                },
            };
            this.chartDevices = new Chart(ctx, chartOptions);
        },

        updateChart(chart, labels, datasets) {
            chart.data.labels = labels;
            chart.data.datasets = datasets;
            chart.update();
        },
    },

    computed: {
        mostVisitedPages() {
            return _.get(this.analytics, 'mostVisitedPages');
        },

        datePickerOptions() {
            return {
                disabledDate(date, current) {
                    // console.log(date, current);
                    // return date > new Date('2018-2-2');
                    return false;
                },
            };
        },

        topReferrers() {
            return _.get(this.analytics, 'topReferrers', []);
        },

        userTypes() {
            return _.chain(this.analytics)
                .get('userTypes', [])
                .each((item) => {
                    if (item.type === 'New Visitor') {
                        item.description = 'Новые посетители проводят меньше времени на сайте, чем возвращающиеся';
                    }
                    if (item.type === 'Returning Visitor') {
                        item.description = 'Возвращеные посетители имеют более высокую конверсию, чем новые';
                        item.class = 'el-text-success';
                    }
                    return item;
                })
                .value();
        },

        showVisitorsAndPageViews() {
            return this.analytics.visitorsAndPageViews && this.analytics.visitorsAndPageViews.length > 0;
        },

        period() {
            const from = _.get(this.analytics, ['period', 'from']);
            const to = _.get(this.analytics, ['period', 'to']);
            return [from, to].filter(Boolean).join(' - ');
        },
    },

    watch: {
        'analytics.totalVisitorsPageViews': {
            handler: function() {
                setTimeout(() => {
                    this.drawChartVisitorsPageView(this.analytics.visitorsAndPageViews);
                    this.drawChartDevices(this.analytics.devices);
                }, 300);
            },
            deep: true,
        },
    },
});
