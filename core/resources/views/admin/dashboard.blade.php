@extends('admin.layouts.app')

@section('panel')


<div class="row gy-4">
    <div class="col-xxl-3 col-sm-6">
        <div class="card bg--primary has-link overflow-hidden box--shadow2">
            <a href="{{ route('admin.users.all') }}" class="item-link"></a>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-4">
                        <i class="las la-users f-size--56"></i>
                    </div>
                    <div class="col-8 text-end">
                        <span class="text-white text--small">@lang('Total Users')</span>
                        <h2 class="text-white">{{ $widget['total_verified_users'] }}</h2>
                    </div>
                </div>
            </div>
        </div>


</div><!-- row end-->
 <div class="widget-two__content">
                <h3 class="text-white">{{ showAmount($widget['referral_commissions']) }}
                    {{ $general->cur_text }}</h3>
                <p class="text-white">@lang('Referral Commissions')</p>
            </div>
            <a href="{{ route('admin.report.commissions') }}" class="widget-two__btn">@lang('View All')</a>
        </div>

<div class="row gy-4 mt-2">
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="fas fa-hand-holding-usd overlay-icon text--success"></i>
            <div class="widget-two__icon b-radius--5 bg--success">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="widget-two__content">
                <h3>{{ $general->cur_sym }}{{ showAmount($deposit['total_deposit_amount']) }}
                </h3>
                <p>@lang('Total Deposited')</p>
            </div>
            <a href="{{ route('admin.deposit.list') }}"
                class="widget-two__btn border border--success btn-outline--success">@lang('View All')</a>
        </div>
    </div><!-- dashboard-w1 end -->
    
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="fas fa-spinner overlay-icon text--warning"></i>
            <div class="widget-two__icon b-radius--5 bg--warning">
                <i class="fas fa-user-check"></i>
            </div> 
            <div class="widget-two__content">
                <h3>{{ $widget['total_approved'] }}
                </h3>
                <p>@lang('Today Approved Users')</p>
            </div>
            <a href="{{ route('admin.deposit.today') }}"
                class="widget-two__btn border border--warning btn-outline--warning">@lang('View Detail')</a>
        </div>
    </div>
    
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="fas fa-hand-holding-usd overlay-icon text--success"></i>
            <div class="widget-two__icon b-radius--5 bg--success">
                <i class="fas fa-hand-holding-usd"></i>
            </div> 
            <div class="widget-two__content">
                <h3>{{ $general->cur_sym }}{{ showAmount($deposit['today_total_deposit_amount']) }}
                </h3>
                <p>@lang('Today Received Balance')</p>
                <small class="text-muted">@lang('New account payments only')</small>
            </div>
            <a href="{{ route('admin.deposit.today') }}"
                class="widget-two__btn border border--success btn-outline--success">@lang('View Detail')</a>
        </div>
    </div>
    
    
     <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="fas fa-spinner overlay-icon text--warning"></i>
            <div class="widget-two__icon b-radius--5 bg--warning">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="widget-two__content">
                <h3> {{ $withdrawals['today_total_withdraw_amount'] }}
                </h3>
                <p>@lang('Today Withdraw')</p>
            </div>
          
        </div>
    </div>
    

    
</div><!-- row end-->

<div class="row mt-4 mb-2">
    <div class="col-12">
        <h4 class="mb-1">@lang('Earnings Breakdown')</h4>
        <p class="text-muted mb-0">@lang('Each source is counted on its own. Plan upgrade payments are not included here.')</p>
    </div>
</div>
<div class="row gy-4">
    <div class="col-md-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="las la-tasks overlay-icon text--primary"></i>
            <div class="widget-two__icon b-radius--5 bg--primary">
                <i class="las la-tasks"></i>
            </div>
            <div class="widget-two__content">
                <h3>{{ $general->cur_sym }}{{ showAmount($earnings['task_overall']) }}</h3>
                <p>@lang('Task Earnings') — @lang('Overall')</p>
                <h3 class="mt-2">
                    <a href="{{ route('admin.earnings.tasks', ['date' => now()->toDateString()]) }}">
                        {{ $general->cur_sym }}{{ showAmount($earnings['task_today']) }}
                    </a>
                </h3>
                <p>@lang('Today Earnings')</p>
            </div>
            <a href="{{ route('admin.earnings.tasks', ['date' => now()->toDateString()]) }}" class="widget-two__btn border border--primary btn-outline--primary">@lang('Today Details')</a>
        </div>
    </div>
    <div class="col-md-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="las la-plus-circle overlay-icon text--success"></i>
            <div class="widget-two__icon b-radius--5 bg--success">
                <i class="las la-plus-circle"></i>
            </div>
            <div class="widget-two__content">
                <h3>{{ $general->cur_sym }}{{ showAmount($earnings['manual_overall']) }}</h3>
                <p>@lang('Manual Balance Add') — @lang('Overall')</p>
                <h3 class="mt-2">
                    <a href="{{ route('admin.earnings.manual', ['date' => now()->toDateString()]) }}">
                        {{ $general->cur_sym }}{{ showAmount($earnings['manual_today']) }}
                    </a>
                </h3>
                <p>@lang('Today Manual Balance Add')</p>
            </div>
            <a href="{{ route('admin.earnings.manual', ['date' => now()->toDateString()]) }}" class="widget-two__btn border border--success btn-outline--success">@lang('Today Details')</a>
        </div>
    </div>
</div>

<div class="row gy-4 mt-2">
    <div class="col-xxl-3 col-sm-6">
        <div class="widget-two box--shadow2 b-radius--5 bg--white">
            <i class="lar la-credit-card overlay-icon text--success"></i>
            <div class="widget-two__icon b-radius--5 border border--success text--success">
                <i class="lar la-credit-card"></i>
            </div>
            <div class="widget-two__content">
                <h3>{{ $general->cur_sym }}{{ showAmount($withdrawals['total_withdraw_amount']) }}
                </h3>
                <p>@lang('Total Withdrawan')</p>
            </div>
            <a href="{{ route('admin.withdraw.log') }}"
                class="widget-two__btn border border--success btn-outline--success">@lang('View All')</a>
        </div>
    </div>
  
</div><!-- row end-->



    
    
    
    
@endsection


@push('script')

    <script src="{{asset('assets/admin/js/vendor/apexcharts.min.js')}}"></script>
    <script src="{{asset('assets/admin/js/vendor/chart.js.2.8.0.js')}}"></script>

    <script>
        "use strict";
        var options = {
            series: [{
                name: 'Total Deposit',
                data: [
                  @foreach($months as $month)
                    {{ getAmount(@$depositsMonth->where('months',$month)->first()->depositAmount) }},
                  @endforeach
                ]
            }, {
                name: 'Total Withdraw',
                data: [
                  @foreach($months as $month)
                    {{ getAmount(@$withdrawalMonth->where('months',$month)->first()->withdrawAmount) }},
                  @endforeach
                ]
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
                categories: @json($months),
            },
            yaxis: {
                title: {
                    text: "{{__($general->cur_sym)}}",
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
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "{{__($general->cur_sym)}}" + val + " "
                    }
                }
            }
        };
        var chart = new ApexCharts(document.querySelector("#apex-bar-chart"), options);
        chart.render();
        var ctx = document.getElementById('userBrowserChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_browser_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_browser_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
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
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                maintainAspectRatio: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });
        var ctx = document.getElementById('userOsChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_os_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_os_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
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
                    ],
                    borderColor: [
                        'rgba(0, 0, 0, 0.05)'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            },
        });
        // Donut chart
        var ctx = document.getElementById('userCountryChart');
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($chart['user_country_counter']->keys()),
                datasets: [{
                    data: {{ $chart['user_country_counter']->flatten() }},
                    backgroundColor: [
                        '#ff7675',
                        '#6c5ce7',
                        '#ffa62b',
                        '#ffeaa7',
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
                    ],
                    borderColor: [
                        'rgba(231, 80, 90, 0.75)'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                aspectRatio: 1,
                responsive: true,
                elements: {
                    line: {
                        tension: 0 // disables bezier curves
                    }
                },
                scales: {
                    xAxes: [{
                        display: false
                    }],
                    yAxes: [{
                        display: false
                    }]
                },
                legend: {
                    display: false,
                }
            }
        });
        // apex-line chart
        var options = {
        chart: {
            height: 450,
            type: "area",
            toolbar: {
            show: false
            },
            dropShadow: {
            enabled: true,
            enabledSeries: [0],
            top: -2,
            left: 0,
            blur: 10,
            opacity: 0.08
            },
            animations: {
            enabled: true,
            easing: 'linear',
            dynamicAnimation: {
                speed: 1000
            }
            },
        },
        dataLabels: {
            enabled: false
        },
        series: [
            {
            name: "Plus Transactions",
            data: [
                @foreach($trxReport['date'] as $trxDate)
                    {{ @$plusTrx->where('date',$trxDate)->first()->amount ?? 0 }},
                @endforeach
            ]
            },
            {
            name: "Minus Transactions",
            data: [
                    @foreach($trxReport['date'] as $trxDate)
                        {{ @$minusTrx->where('date',$trxDate)->first()->amount ?? 0 }},
                    @endforeach
                ]
            }
        ],
        fill: {
            type: "gradient",
            gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.9,
            stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: [
                @foreach($trxReport['date'] as $trxDate)
                    "{{ $trxDate }}",
                @endforeach
            ]
        },
        grid: {
            padding: {
            left: 5,
            right: 5
            },
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
        };
        var chart = new ApexCharts(document.querySelector("#apex-line"), options);
        chart.render();
    </script>
@endpush
