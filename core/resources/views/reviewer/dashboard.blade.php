@extends('reviewer.layouts.app')

@section('panel')
    <div class="row gy-4 mt-2">
        <div class="col-xxl-3 col-sm-6">
            <x-widget value="{{ $widget['total'] }}" title="Total Images" style="7" type="2" link="javascript:void(0)" icon="las la-images" color="white"
                bg="15" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget value="{{ $widget['pending'] }}" title="Pending Images" style="7" type="2" link="{{ route('reviewer.images.pending') }}"
                icon="las la-spinner" color="white" bg="warning" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget value="{{ $widget['approved_by_me'] }}" title="Approved By Me" style="7" type="2"
                link="{{ route('reviewer.images.approved') }}" icon="las la-check" color="white" bg="success" />
        </div>
        <div class="col-xxl-3 col-sm-6">
            <x-widget value="{{ $widget['rejected_by_me'] }}" title="Rejected By Me" style="7" type="2"
                link="{{ route('reviewer.images.rejected') }}" icon="las la-times" color="white" bg="red" />
        </div>
    </div><!-- row end-->


    <div class="row mb-none-30 mt-30">
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Monthly Approved Images By Me') (@lang('Last 12 Month'))</h5>
                    <div id="apex-bar-chart-approved"> </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 mb-30">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">@lang('Monthly Rejected Images By Me') (@lang('Last 12 Month'))</h5>
                    <div id="apex-bar-chart-reject"></div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>

    <script>
        "use strict";

        var options = {
            series: [{
                name: 'Total Approved',
                data: @json($report['approved']->flatten())
            }],
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
                    columnWidth: '3%',
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
                categories: @json($months),
            },
            yaxis: {
                title: {
                    text: "pc's",
                    style: {
                        color: '#7c97bb'
                    }
                }
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
            }
        };

        var chart = new ApexCharts(document.querySelector("#apex-bar-chart-approved"), options);
        chart.render();

        var options = {
            series: [{
                name: 'Total Rejected',
                data: @json($report['rejected']->flatten())
            }],
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
                    columnWidth: '3%',
                    endingShape: 'rounded'
                }
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
                categories: @json($months),
            },
            yaxis: {
                title: {
                    text: "pc's",
                    style: {
                        color: '#7c97bb'
                    }
                }
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
                opacity: 1,
                colors: ['#F44336']
            },
            colors: ["#F44336"]
        };

        var chart = new ApexCharts(document.querySelector("#apex-bar-chart-reject"), options);
        chart.render();
    </script>
@endpush
