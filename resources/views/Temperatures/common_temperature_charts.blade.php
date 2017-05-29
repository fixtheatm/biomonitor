<script>
// common line chart options for temperature graphs
/*global $ */
/*jslint browser */

var baseGraphOptions = {
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
                display: true,
                labelString: "{{ Lang::get('bioreactor.temperature_axis_full') }}",
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

var small_tempOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.temperature_axis_small') }}",
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

</script>
