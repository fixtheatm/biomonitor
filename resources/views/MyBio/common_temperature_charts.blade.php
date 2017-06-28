<!-- Start of MyBio/common_temperature_charts.blade.php -->
<script>
/*global chartDataSet, chartOptions, fmtEndingDate */
/*jslint browser */

(function () {
    "use strict";
    var base = document.com.solarbiocells.biomonitor;
    var bin = base.bin;

    base.chartDataSet["{{ $sensor_name }}"] = {};
    base.chartDataSet["small{{ $sensor_name }}"] = {};
    base.chartDataSet["big{{ $sensor_name }}"] = {
        label: "{{ Lang::get('bioreactor.end_time_prefix') }}" + bin.fmtEndingDate("{{ $sensor['end_datetime'] }}") + "{{ Lang::get('bioreactor.end_time_suffix') }}"
    };
    // Handled by chartDataSet[ 'full' ]
    base.chartDataSet["full{{ $sensor_name }}"] = {};

    base.chartOptions["{{ $sensor_name }}"] = {
        scales: {
            yAxes: [{
                ticks: {
                    callback: function (label, index, labels) {
                        "use strict";
                        return label + "°";
                    },
                    suggestedMin: 20.0,
                    suggestedMax: 22.0,
                    stepSize: 1.0
                }
            }]
        }
    };
    base.chartOptions["small{{ $sensor_name }}"] = {
        scales: {
            yAxes: [{
                ticks: {
                    stepSize: 5.0
                },
                scaleLabel: {
                    labelString: "{{ Lang::get('bioreactor.' . $sensor_name . '_axis_small') }}"
                }
            }]
        }
    };
    base.chartOptions["big{{ $sensor_name }}"] = {
        scales: {
            yAxes: [{
                scaleLabel: {
                    labelString: "{{ Lang::get('bioreactor.' . $sensor_name . '_axis_big') }}"
                }
            }]
        }
    };
    // Handled by chartOptions[ 'full' ]
    base.chartOptions["full{{ $sensor_name }}"] = {};
}());// anonymous function()
</script>
<!-- Start of MyBio/common_temperature_charts.blade.php -->
