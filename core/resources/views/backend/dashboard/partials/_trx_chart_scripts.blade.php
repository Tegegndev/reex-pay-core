<script>
    "use strict";

    $(function () {
        const chartEl = document.querySelector("#dashboard-trx-chart");
        if (!chartEl) return;

        let chart;
        let fullSeries = {};
        let categories = [];
        const colorMap = {
            deposit: '#198754BB',   // success
            withdraw: '#dc3545BB',  // danger
            payment: '#0d6efdBB',   // primary
            reward: '#ffc107BB'     // warning
        };

        const $range = $('#reportrange span');
        const $input = $('#hidden-daterange');

	    
        function setDateDisplay(start, end) {
            const sameYear = start.year() === end.year();
            const startFormat = 'MMM D, YYYY';
            const endFormat = sameYear ? 'MMM D' : 'MMM D, YYYY';

            $range.text(start.format(startFormat) + ' - ' + end.format(endFormat));
            $input.val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
        }


        // Initial date
        let start = moment().subtract(14, 'days');
        let end = moment();

        // Init date range picker
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()]
            }
        }, function (startPick, endPick) {
            start = startPick;
            end = endPick;
            setDateDisplay(start, end);
            fetchChartData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        });

        // Initial render
        setDateDisplay(start, end);
        fetchChartData(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));

        // Fetch chart data by date range
        function fetchChartData(startDate, endDate) {
            $.ajax({
                url: "{{ route('admin.dashboard') }}",
                data: {
                    start_date: startDate,
                    end_date: endDate,
	                trx_chart: true
                },
                success: function (res) {
                    if (!res.series) return;
                    
                    

                    fullSeries = res.series.reduce((acc, item) => {
                        acc[item.name.toLowerCase()] = item;
                        return acc;
                    }, {});

                   

                    categories = res.dates.map(d => new Date(d).toISOString());

                    const seriesArray = Object.values(fullSeries);
                    const colorArray = Object.keys(fullSeries).map(type => colorMap[type] || '#888888');


                    seriesArray.forEach(({ name, total }) => {
                        const key = name?.toLowerCase?.(); // safe lowercase
                        if (!key || typeof total === 'undefined') return;

                        const el = document.getElementById(`total-${key}`);
                        if (el) el.textContent = total;
                    });


                    if (!chart) {
                        chart = new ApexCharts(chartEl, {
                            chart: {
                                type: 'area',
                                height: 320,
                                toolbar: { show: false }
                            },
                            series: seriesArray,
                            colors: colorArray,
                            xaxis: {
                                type: 'datetime',
                                categories: categories,
                                labels: { format: 'dd MMM' }
                            },
                            stroke: { curve: 'smooth', width: 2 },
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    opacityFrom: 0.4,
                                    opacityTo: 0.05,
                                    stops: [0, 90, 100]
                                }
                            },
                            grid: {
                                borderColor: '#e5e5e5',
                                strokeDashArray: 4
                            },
                            dataLabels: { enabled: false },
                            tooltip: { x: { format: 'dd/MM/yyyy' } },
                            legend: { show: false }
                        });
                        chart.render();
                    } else {
                        chart.updateOptions({
                            series: seriesArray,
                            xaxis: { categories },
                            colors: colorArray
                        });
                    }

                    chart.initialSeries = fullSeries;
                }
            });
        }

        // Filter stat-card click handler
        $(document).on('click', '.stat-filter', function () {
            const type = $(this).data('type');
            const isActive = $(this).hasClass('active');

            $('.stat-filter').removeClass('active');

            if (isActive) {
                chart.updateOptions({
                    series: Object.values(chart.initialSeries),
                    colors: Object.keys(chart.initialSeries).map(t => colorMap[t] || '#888')
                });
            } else {
                $(this).addClass('active');
                if (chart.initialSeries[type]) {
                    chart.updateOptions({
                        series: [chart.initialSeries[type]],
                        colors: [colorMap[type] || '#888']
                    });
                }
            }
        });
    });
</script>