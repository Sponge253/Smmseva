@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-7">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card full-view">
                        <div class="card-header">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-6">
                                    <h5 class="card-title mb-0">@lang('Total Orders')</h5>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <div class="d-flex justify-content-sm-end gap-2">
                                        <select class="widget_select" name="oredr_time">
                                            <option value="week">@lang('Current Week')</option>
                                            <option value="month">@lang('Current Month')</option>
                                            <option value="year">@lang('Current Year')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center pb-0 px-0">
                            <div id="my_order_canvas"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-5">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row g-2 align-items-center">
                                <div class="col-sm-6 col-md-12 col-xl-5">
                                    <h5 class="card-title mb-0">@lang('Statistics by API Providers')</h5>
                                </div>
                                <div class="col-sm-6 col-md-12 col-xl-7">
                                    <div class="pair-option justify-content-md-start justify-content-xl-end">
                                        <select class="widget_select" name="order_statistics_time">
                                            <option value="all">@lang('All Duration')</option>
                                            <option value="week">@lang('Current Week')</option>
                                            <option value="month">@lang('Current Month')</option>
                                            <option value="year">@lang('Current Year')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <div class="chart-info">
                                    <a class="chart-info-toggle" href="#">
                                        <img class="chart-info-img" src="{{ asset('assets/images/collapse.svg') }}">
                                    </a>
                                    <div class="chart-info-content">
                                        <ul class="chart-info-list api-info-data"></ul>
                                    </div>
                                </div>
                                <div class="chart-area chart-area--fixed">
                                    <div class="order_api_statistic_canvas"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="card-title">@lang('Order by API Provider') (@lang('Last 12 Month'))</h5>
                        <select class="widget_select_api" name="provider_id">
                            @foreach ($providers as $provider)
                                <option value="{{ $provider->id }}">{{ __($provider->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="order-chart"> </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script>
        'use strict';
        (function($) {

            var chart;

            $('[name=oredr_time]').on('change', function() {
                let time = $(this).val();
                let url = "{{ route('admin.order.report.statistics') }}";

                $.get(url, {
                    time: time
                }, function(response) {
                    let pendingData = [];
                    let processingData = [];
                    let completedData = [];
                    let cancelledData = [];
                    let refundedData = [];
                    let labels = [];

                    $.each(response.chart_data, function(i, v) {
                        pendingData.push(v.pending);
                        processingData.push(v.processing);
                        completedData.push(v.completed);
                        cancelledData.push(v.cancelled);
                        refundedData.push(v.refunded);
                        labels.push(i);
                    });

                    var options = {
                        series: [{
                                name: 'Pending',
                                data: pendingData
                            },
                            {
                                name: 'Processing',
                                data: processingData
                            },
                            {
                                name: 'Completed',
                                data: completedData
                            },
                            {
                                name: 'Cancelled',
                                data: cancelledData
                            },
                            {
                                name: 'Refunded',
                                data: refundedData
                            }
                        ],
                        colors: [
                            getBackgroundColorForStatus('pending'),
                            getBackgroundColorForStatus('processing'),
                            getBackgroundColorForStatus('completed'),
                            getBackgroundColorForStatus('cancelled'),
                            getBackgroundColorForStatus('refunded')
                        ],
                        chart: {
                            type: 'bar',
                            height: 450,
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '50%',
                                endingShape: 'rounded'
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: labels,
                        },
                        grid: {
                            xaxis: {
                                lines: {
                                    show: false
                                }
                            },
                            yaxis: {
                                lines: {
                                    show: false
                                }
                            },
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val
                                }
                            }
                        }
                    };


                    if (chart) {
                        chart.destroy();
                    }
                    chart = new ApexCharts(document.querySelector("#my_order_canvas"), options);
                    chart.render();

                });
            }).change();

            function getBackgroundColorForStatus(status) {
                switch (status) {
                    case 'pending':
                        return '#ffcc00'; // Yellow for pending
                    case 'processing':
                        return '#007bff'; // Blue for processing
                    case 'completed':
                        return '#28a745'; // Green for completed
                    case 'cancelled':
                        return '#dc3545'; // Red for cancelled
                    case 'refunded':
                        return '#6610f2'; // Purple for refunded
                    default:
                        return '#6c757d'; // Default color
                }
            }



            // Api provider order chart
            $('[name=provider_id]').on('change', function() {
                var providerId = $(this).val();
                $.ajax({
                    type: "GET",
                    url: `{{ route('admin.provider.chart') }}/${providerId}`,
                    success: function(response) {
                        $('#order-chart').html('');
                        var options = {
                            series: [{
                                name: 'Total',
                                data: Object.values(response),
                            }],
                            chart: {
                                type: 'bar',
                                height: 400,
                                toolbar: {
                                    show: false
                                }
                            },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    endingShape: 'rounded'
                                },
                            },
                            dataLabels: {
                                enabled: false
                            },
                            stroke: {
                                show: true,
                                width: 1,
                                colors: ['transparent']
                            },
                            xaxis: {
                                categories: Object.keys(response)
                            },
                            fill: {
                                opacity: 1
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val.toFixed(2) + " {{ gs('cur_text') }}"
                                    }
                                }
                            }
                        };
                        var orderChart = new ApexCharts(document.querySelector("#order-chart"), options);
                        orderChart.render();
                    }
                });
            }).change()


            //API-providers-statistics//
            $('[name=order_statistics_time]').on('change', function() {
                let time = $('[name=order_statistics_time]').val();

                var url = "{{ route('admin.order.report.statistics.api') }}";
                $.get(url, {
                    time: time,
                }, function(response) {
                    $('.order_api_statistic_canvas').html(
                        '<canvas height="250" id="order_api_statistics"></canvas>');
                    let orders = response.order_data;

                    let apiInfo = '';
                    let orderPrice = [];
                    let apiName = [];
                    let totalOrder = response.total_order;
                    if (orders.length > 0) {
                        $.each(orders, function(key, order) {
                            let orderPercent = (order.orderPrice / totalOrder) * 100;
                            orderPrice.push(parseFloat(order.orderPrice).toFixed(2));
                            let providerName = (order.provider && order.provider.name) ? order.provider.name : 'Direct Order';
                            apiName.push(providerName);

                            apiInfo += `<li class="chart-info-list-item"><i class="las la-shopping-cart apiPoint me-2"></i>${orderPercent.toFixed(2)}% - ${providerName} </li>`;
                        });
                    } else {
                        orderPrice.push(0);
                        apiName.push('No Data');
                        apiInfo = '<li class="chart-info-list-item">No Data</li>';
                    }

                    $('.api-info-data').html(apiInfo);

                    /* -- Chartjs - Pie Chart -- */
                    var pieChartID = document.getElementById("order_api_statistics").getContext('2d');
                    var pieChart = new Chart(pieChartID, {
                        type: 'pie',
                        data: {
                            datasets: [{
                                data: orderPrice,
                                borderColor: 'transparent',
                                backgroundColor: apiColors()
                            }],
                            labels: apiName // Use provider names or 'No Data'
                        },
                        options: {
                            responsive: true,
                            legend: {
                                display: false
                            },
                            tooltips: {
                                callbacks: {
                                    label: (tooltipItem, data) => data.datasets[0].data[
                                        tooltipItem.index] + ' {{ gs("cur_text") }}'
                                }
                            }
                        }
                    });

                    var apiPoints = $('.apiPoint');
                    apiPoints.each(function(key, apiPoint) {
                        var apiPoint = $(apiPoint)
                        apiPoint.css('color', apiColors()[key])
                    });

                });
            }).change();


            function apiColors() {
                return [
                    '#D980FA',
                    '#fccbcb',
                    '#45aaf2',
                    '#05dfd7',
                    '#FF00F6',
                    '#1e90ff',
                    '#2ed573',
                    '#eccc68',
                    '#ff5200',
                    '#cd84f1',
                    '#7efff5',
                    '#7158e2',
                    '#fff200',
                    '#ff9ff3',
                    '#08ffc8',
                    '#3742fa',
                    '#1089ff',
                    '#70FF61',
                    '#bf9fee',
                    '#574b90'
                ]
            }

            let chartToggle = $('.chart-info-toggle');
            let chartContent = $(".chart-info-content");
            if (chartToggle || chartContent) {
                chartToggle.each(function() {
                    $(this).on("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        $(this).siblings().toggleClass("is-open");
                    });
                });
                chartContent.each(function() {
                    $(this).on("click", function(e) {
                        e.stopPropagation();
                    });
                });
                $(document).on("click", function() {
                    chartContent.removeClass("is-open");
                });
            }

        })(jQuery);
    </script>
@endpush
