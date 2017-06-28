<!-- Start of MyBio/common_light_charts.blade.php -->
<script>
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

    // Need to find a way to prevent the php expansion from converting the utf-8
    // character to encoded form.  I *want& µ here, not &micro;
    // alert("{{ Lang::get('bioreactor.intensity_small') }}");
    // alert("{{{ Lang::get('bioreactor.intensity_small') }}}");
    // alert("{{ 'µ' }}");
    // alert("µ");
    // Only the final hard-coded string actually works
    // Options for all light sensor charts, regardless of the graph size
    base.chartOptions["{{ $sensor_name }}"] = {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    };
    base.chartOptions["small{{ $sensor_name }}"] = {
        scales: {
            yAxes: [{
                scaleLabel: {
                    // labelString: "{{ Lang::get('bioreactor.' . $sensor_name . '_axis_small') }}",
                    labelString: "µmol γ/(m^2 S)"
                }
            }]
        }
    };
    base.chartOptions["big{{ $sensor_name }}"] = {
        scales: {
            yAxes: [{
                scaleLabel: {
                    // labelString: "{{ Lang::get('bioreactor.' . $sensor_name . '_axis_big') }}"
                    labelString: "µmol photons/(m^2 S)"
                }
            }]
        }
    };
    // Handled by chartOptions[ 'full' ]
    base.chartOptions["full{{ $sensor_name }}"] = {};
}());// anonymous function()
</script>
<!-- End of MyBio/common_light_charts.blade.php -->
