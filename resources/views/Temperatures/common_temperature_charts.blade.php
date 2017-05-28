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
                labelString: "Degrees Celsius",
                fontSize: 14
            }
        }],
        xAxes: [{
            scaleLabel: {
                display: true,
                labelString: "Time (HH:MM) ending at: {{ $end_datetime }}",
                fontSize: 14
            }
        }]
    }
};

var small_tempOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            scaleLabel: {
                labelString: "Celsius",
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
                labelString: "Time (HH:MM)"
            }
        }]
    },
    title: {
        text: "Temperature vs Time"
    }
});
var full_tempOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "Photo BioReactor Temperature versus Time"
    }
});
// alert ("small temperature:"+JSON.stringify(small_tempOptions));

</script>
