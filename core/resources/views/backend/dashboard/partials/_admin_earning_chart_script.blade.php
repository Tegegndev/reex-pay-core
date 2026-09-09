<script>
    "use strict";

    let feeChart;
    const feeChartEl = document.querySelector("#fee-earnings-chart");

    // Initialize Chart
    function renderFeeChart(dataSeries, categories) {
        if (feeChart) feeChart.destroy();

        feeChart = new ApexCharts(feeChartEl, {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            series: dataSeries,
            xaxis: {
                type: 'datetime',
                categories: categories,
                labels: {
                    format: 'dd MMM',
                    rotate: -45
                }
            },
            tooltip: {
                x: { format: 'dd MMM yyyy' },
                y: {
                    formatter: val => '{{ siteCurrency("symbol") }}' + parseFloat(val).toFixed(2)
                }
            },
            yaxis: {
                title: { text: '{{ siteCurrency("symbol") }} Fee' }
            },
            colors: ['#1d6ce0'],
            dataLabels: { enabled: false },
            fill: {
                type: 'solid',
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#1d6ce0']
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 6,
                    borderRadiusApplication: 'end',
                }
            }
        });

        feeChart.render();
    }

    // Fetch Data
    function fetchFeeChart(start, end) {
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            data: {
                start_date: start,
                end_date: end,
                fee_chart: true
            },
            success: function (res) {
                renderFeeChart(res.series, res.categories);
            }
        });
    }

    // Date Picker Initialization
    $(function () {
        const start = moment().subtract(6, 'days');
        const end = moment();

        function updateDisplay(start, end) {

            const sameYear = start.year() === end.year();
            const startFormat = 'MMM D, YYYY';
            const endFormat = sameYear ? 'MMM D' : 'MMM D, YYYY';
            
            $('#report-earning-range span').html(start.format(startFormat) + ' - ' + end.format(endFormat));
            $('#hidden-daterange').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            fetchFeeChart(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }

        $('#report-earning-range').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                {{ __("Today") }}: [moment(), moment()],
	            '{{ __("Last 7 Days") }}': [moment().subtract(6, 'days'), moment()],
                '{{ __("Last 30 Days") }}': [moment().subtract(29, 'days'), moment()],
                '{{ __("This Month") }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ __("Last Month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, updateDisplay);

        // Initial Load
        updateDisplay(start, end);
    });
</script>