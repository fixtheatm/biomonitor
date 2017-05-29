<script>
// common line chart options for gas flow graphs
/*global $ */
/*jslint browser */

var baseGraphOptions = {
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero: false
            },
            scaleLabel: {
                display: true,
                labelString: "{{ Lang::get('bioreactor.flow_axis_full') }}",
                fontSize: 14
            }
        }],
        xAxes: [{
            scaleLabel: {
                display: true,
                labelString: "{{ Lang::get('bioreactor.time_before_end') }}{{ $end_datetime }}{{ Lang::get('bioreactor.after_end_time') }}",
                fontSize: 14
            }
        }]
    }
};

var small_gasflowOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.flow_axis_small') }}",
                fontSize: 12
            }
        }],
        xAxes: [{
            scaleLabel: {
                display: false
            }
        }]
    },
    title: {
        display: false,
    }
});
var big_gasflowOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        xAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.time_axis_big') }}"
            }
        }]
    },
    title: {
        text: "{{ Lang::get('bioreactor.chart_gas_title_big') }}"
    }
});
var full_gasflowOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "{{ Lang::get('bioreactor.chart_gas_title_full') }}"
    }
});

</script>
