<!-- Start of MyBio/common_oxygen_charts.blade.php -->
<script>
/*jslint browser */

(function () {
    "use strict";
    var base = document.com.solarbiocells.biomonitor;
    var bin = base.bin;

    base.chartDataSet["{{ $sensor_name }}"] = {
        steppedLine: true
    };
    base.chartDataSet["small{{ $sensor_name }}"] = {
        pointRadius: {{ $sensor['point_count'] > 20 ? 1 :( $sensor['point_count'] > 10 ? 2 : 3 ) }}
    };
    base.chartDataSet["big{{ $sensor_name }}"] = {
        label: "{{ Lang::get('bioreactor.end_time_prefix') }}" + bin.fmtEndingDate("{{ $sensor['end_datetime'] }}") + "{{ Lang::get('bioreactor.end_time_suffix') }}"
    };
    // Handled by chartDataSet[ 'full' ]
    base.chartDataSet["full{{ $sensor_name }}"] = {};

    // Options for all oxygen sensor charts, regardless of the graph size
    base.chartOptions["{{ $sensor_name }}"] = {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                },
                scaleLabel: {
                    labelString: "{{ Lang::get('bioreactor.' . $sensor_name . '_axis_small') }}"
                }
            }]
        }
    };
    base.chartOptions["small{{ $sensor_name }}"] = {};
    base.chartOptions["big{{ $sensor_name }}"] = {};
    // Handled by chartOptions[ 'full' ]
    base.chartOptions["full{{ $sensor_name }}"] = {};
}());// anonymous function()
</script>
<!-- End of MyBio/common_oxygen_charts.blade.php -->
