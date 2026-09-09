<script>
    "use strict";

    let walletChart;
    const walletChartEl = document.querySelector("#chart-wallet-growth");

    function renderWalletChart(series, categories) {
        if (walletChart) walletChart.destroy();

        walletChart = new ApexCharts(walletChartEl, {
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false }
            },
            series: series,
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
                    formatter: val => val + ' {{ __("Wallets") }}'
                }
            },
            yaxis: {
                title: { text: '{{ __("New Wallets") }}' }
            },
            colors: ['#43A047'],
            dataLabels: { enabled: false },
            fill: {
                type: 'solid'
            },
            plotOptions: {
                bar: {
                    columnWidth: '50%',
                    borderRadius: 6,
                    borderRadiusApplication: 'end'
                }
            }
        });

        walletChart.render();
    }

    function fetchWalletChart(start, end) {
        $.ajax({
            url: "{{ route('admin.dashboard') }}",
            data: {
                start_date: start,
                end_date: end,
                wallet_chart: true
            },
            success: function (res) {
                renderWalletChart(res.series, res.categories);
            }
        });
    }

    $(function () {
        const start = moment().subtract(6, 'days');
        const end = moment();

        function updateDisplay(start, end) {

            const sameYear = start.year() === end.year();
            const startFormat = 'MMM D, YYYY';
            const endFormat = sameYear ? 'MMM D' : 'MMM D, YYYY';
            
            $('#wallet-reportrange span').html(start.format(startFormat) + ' - ' + end.format(endFormat));
            $('#wallet-hidden-daterange').val(start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            fetchWalletChart(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }

        $('#wallet-reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            opens: 'left',
            ranges: {
                '{{ __("Today") }}': [moment(), moment()],
                '{{ __("Last 7 Days") }}': [moment().subtract(6, 'days'), moment()],
                '{{ __("Last 30 Days") }}': [moment().subtract(29, 'days'), moment()],
                '{{ __("This Month") }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ __("Last Month") }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, updateDisplay);

        updateDisplay(start, end);
    });
</script>