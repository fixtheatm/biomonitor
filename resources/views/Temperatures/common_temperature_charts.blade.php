<script>
// common line chart options for temperature graphs
/*global $ */
/*jslint browser */

var baseGraphOptions = $.extend( true, {}, lineOptionsTemplate, {
    scales: {
        yAxes: [{
            ticks: {
                callback: function (label, index, labels) {
                    "use strict";
                    return label + "°";
                },
                suggestedMin: 20.0,
                suggestedMax: 30.0,
                stepSize: 2
            },
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.temp_axis_full') }}",
            }
        }],
        xAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.time_before_end') }}{{ $end_datetime }}{{ Lang::get('bioreactor.after_end_time') }}",
            }
        }]
    }
});
// alert("base: "+JSON.stringify(baseGraphOptions));

var small_tempOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.temp_axis_small') }}",
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
var big_tempOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        xAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.time_axis_big') }}"
            }
        }]
    },
    title: {
        text: "{{ Lang::get('bioreactor.chart_temp_title_big') }}"
    }
});
var full_tempOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "{{ Lang::get('bioreactor.chart_temp_title_full') }}"
    }
});

// Options for all temperature sensor charts, regardless of the graph size
// TODO Add logic to stepSize, to increase for the small charts
// merge alternate stepSize in the ?manuall? for small charts
// TODO get rid of isset test after $sensor_name in every view
@if ( isset( $sensor_name ))
{{ $sensor_name }}Options = {
    scales: {
        yAxes: [{
            ticks: {
                callback: function (label, index, labels) {
                    "use strict";
                    return label + "°";
                },
                suggestedMin: 20.0,
                suggestedMax: 22.0,
                stepSize: 1.0,
            },
        }],
    },
};
@endif

</script>
