<script>
    "use strict";

    document.addEventListener("DOMContentLoaded", function () {
        const chartEl = document.querySelector("#user-trx-chart");
        const rangePicker = $('#user-trx-reportrange span');
        const input = document.getElementById('wallet-hidden-daterange');

        if (!chartEl) return;

        let chart = null;

        const loadChart = (start, end) => {
            $.get("{{ route('admin.user.manage', ['username' => $user->username,'param' => 'statistics']) }}", {
                start_date: start.format('YYYY-MM-DD'),
                end_date: end.format('YYYY-MM-DD'),
            }, function (res) {
                const options = {
                    series: res.series,
                    chart: {
                        type: 'bar',
                        height: 340,
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '50%',
                            borderRadius: 5,
                            borderRadiusApplication: 'end',
                        },
                    },
	                
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        type: 'datetime',
                        categories: res.categories,
                        labels: {
                            format: 'dd MMM',
                            rotate: -45,
                            style: { fontSize: '13px', colors: '#6c757d' }
                        }
                    },
                    fill: {
                        opacity: 1,
                        colors: ['#198754', '#ffc107', '#dc3545']
                    },
                    tooltip: {
                        y: { formatter: val => `${val} trx` }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '13px',
                        labels: { colors: '#6c757d' },
                        markers: {
                            radius: 12
                        }
                    },
                    grid: {
                        borderColor: '#e9ecef',
                        strokeDashArray: 4
                    }
                };

                if (chart) {
                    chart.updateOptions(options);
                } else {
                    chart = new ApexCharts(chartEl, options);
                    chart.render();
                }
            });
        };

        const init = () => {
            const start = moment().subtract(6, 'days');
            const end   = moment();
            $('#wallet-hidden-daterange').val(`${start.format('YYYY-MM-DD')} / ${end.format('YYYY-MM-DD')}`);
            $('#user-trx-reportrange span').text(`${start.format('MMM D')} - ${end.format('MMM D, YYYY')}`);
            loadChart(start, end);
        };

        $('#user-trx-reportrange').daterangepicker({
            startDate: moment().subtract(6, 'days'),
            endDate: moment(),
            ranges: {
                'Today': [moment(), moment()],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            },
            locale: {
                format: 'MM/DD/YYYY'
            }
        }, function (start, end) {
            input.value = `${start.format('YYYY-MM-DD')} / ${end.format('YYYY-MM-DD')}`;
            rangePicker.text(`${start.format('MMM D')} - ${end.format('MMM D, YYYY')}`);
            loadChart(start, end);
        });

        init();
    });
</script>