<!-- Start of common_line_chart.blade.php -->
<script>
/*global $, Chart */
/*jslint browser, devel */

(function () {
    "use strict";

    // Initialized the pseudo namespace object
    // Create a place to store application information out of the global namespace
    var base = document;
    var bin = null;
    var cnst = null;
    if (base.com === undefined) {
        base.com = {};
    }
    if (!$.isPlainObject(base.com)) {
        console.log("early 1");
        return;
    }
    base = base.com;
    if (base.solarbiocells === undefined) {
        base.solarbiocells = {};
    }
    if (!$.isPlainObject(base.solarbiocells)) {
        console.log("early 2");
        return;
    }
    base = base.solarbiocells;
    if (base.biomonitor === undefined) {
        base.biomonitor = {};
    }
    if (!$.isPlainObject(base.biomonitor)) {
        console.log("early 3");
        return;
    }
    base = base.biomonitor;
    if (base.constants === undefined) {
        base.constants = {};
    }
    if (base.bin === undefined) {
        base.bin = {};
    }
    if (!$.isPlainObject(base.constants)) {
        console.log("early 3");
        return;
    }
    if (!$.isPlainObject(base.bin)) {
        console.log("early 4");
        return;
    }
    // console.log("const is: " + $.isPlainObject(base.constants));
    // console.log("const is: " + $.isPlainObject(document.com.solarbiocells.biomonitor.constants));
    bin = base.bin;
    cnst = base.constants;

    // Some common (cross sensor) functions and view data
    cnst.weekday_names = [@foreach ($date_constants['weekday'] as $nm)"{{ $nm }}"@if ($nm !== end($date_constants['weekday'])), @endif{{-- --}}@endforeach];
    cnst.month_names = [@foreach ($date_constants['month'] as $nm)"{{ $nm }}"@if ($nm !== end($date_constants['month'])), @endif{{-- --}}@endforeach];

    /**
     * convert a database timestamp string to a date object
     *
     * @param String timestamp "yyyy-mm-dd hh:mm:ss"
     * @returns Date
     */
    bin.timestamp2date = function (timestamp) {
        var dttm = timestamp.split(" ");
        var dt = dttm[0].split("-");
        var tm = dttm[1].split(":");
        return new Date(Date.UTC(dt[0], dt[1] - 1, dt[2], tm[0], tm[1], tm[2]));
    };// ./timestamp2date(…)

    /**
     * format date to "Www Mmm DD YYYY HH:MM (TZ)"
     *
     * @param Date timestamp
     * @returns String date as Www Mmm DD YYYY HH:MM (TZ)
     */
    bin.fmt_www_mmm_dd_yyyy_hh_mm_tx = function (full) {
        var hr = "0" + full.getHours();
        var mn = "0" + full.getMinutes();
        var pts = full.toString().split(" ");
        var fmtd = cnst.weekday_names[full.getDay()] + " " + cnst.month_names[full.getMonth()] + " " + full.getDate() + " " + full.getFullYear() + " " + hr.substr(hr.length - 2) + ":" + mn.substr(mn.length - 2) + " " + pts[pts.length - 1];
        return fmtd;
    };// ./fmt_www_mmm_dd_yyyy_hh_mm_tx(…)

    // Mon Jun 19 2017 13:11:00 GMT-0600 (MDT)
    /**
     * date to local format that is usable by Carbon::
     *
     * ddd mmm dd yyyy hh:mm o (tz)
     *
     * @param Date timestamp
     * @returns String date as Www Mmm DD YYYY HH:MM O (TZ)
     */
    bin.fmtLocalDateTime = function (full) {
      var hr = "0" + full.getHours();
      var mn = "0" + full.getMinutes();
      var pts = full.toString().split(" ");
      var tzo = "0000" + full.getTimezoneOffset();

      var fmtd = cnst.weekday_names[full.getDay()] + " " + cnst.month_names[full.getMonth()] + " " + full.getDate() + " " + full.getFullYear() + " " + hr.substr(hr.length - 2) + ":" + mn.substr(mn.length - 2) + " " + pts[pts.length - 2] + " " + pts[pts.length - 1];
      return fmtd;
    }

    /**
     * format date to "Www Mmm DD YYYY HH:MM (TZ)"
     *
     * @param Date timestamp
     * @returns String date as HH:MM (TZ)
     */
    bin.fmt_hh_mm_tx = function (full) {
        var hr = "0" + full.getHours();
        var mn = "0" + full.getMinutes();
        var pts = full.toString().split(" ");
        var fmtd = hr.substr(hr.length - 2) + ":" + mn.substr(mn.length - 2) + " " + pts[pts.length - 1];
        return fmtd;
    };// ./fmt_hh_mm_tx(…)

    /**
     * format date for display as the ending date for the graph
     *
     * @param String timestamp "yyyy-mm-dd hh:mm:ss"
     * @returns Www Mmm DD YYYY HH:MM (TZ)
     */
    bin.fmtEndingDate = function (dt) {
        return bin.fmt_www_mmm_dd_yyyy_hh_mm_tx(bin.timestamp2date(dt));
    };

    /**
     * format date for display in tooltips
     *
     * @param int dt epoch (unix) time
     * @returns Www Mmm DD YYYY HH:MM (TZ)
     */
    bin.fmtTooltipDate = function (dt) {
        return bin.fmt_www_mmm_dd_yyyy_hh_mm_tx(new Date(dt));
    };

    /**
     * format date for display in tooltips
     *
     * @param int dt epoch (unix) time
     * @returns Www Mmm DD YYYY HH:MM (TZ)
     */
    bin.fmtSmallTooltipDate = function (dt) {
        return bin.fmt_hh_mm_tx(new Date(dt));
    };

    /**
     * fill in a canvas with chart data
     *
     * @param ele_id string name of canvas element
     * @param chart_size string
     * @param sensor string name (type) of sensor
     * @param points array of points for scatter chart
     */
    base.chartDataSet = [];
    base.chartOptions = [];
    bin.populateScatterChart = function (ele_id, chart_size, sensor, points) {
        // console.log( ele_id + "|" + chart_size + "|" + sensor + "|" );
        // console.log( JSON.stringify( base.chartDataSet[ "all" ] ));
        // console.log( JSON.stringify( base.chartDataSet[ chart_size ] ));
        // console.log( JSON.stringify( base.chartDataSet[ sensor ] ));
        // console.log( JSON.stringify( base.chartDataSet[ chart_size + sensor ] ));
        // console.log( JSON.stringify( base.chartOptions[ "all" ] ));
        // console.log( JSON.stringify( base.chartOptions[ chart_size ] ));
        // console.log( JSON.stringify( base.chartOptions[ sensor ] ));
        // console.log( JSON.stringify( base.chartOptions[ chart_size + sensor ] ));
        var graphDataSet = [$.extend(true, {}, base.chartDataSet.all, base.chartDataSet[chart_size], base.chartDataSet[sensor], base.chartDataSet[chart_size + sensor], {
            data: points
        })];
        var graphOptions = $.extend(true, {}, base.chartOptions.all, base.chartOptions[chart_size], base.chartOptions[sensor], base.chartOptions[chart_size + sensor]);
        // console.log( JSON.stringify( graphDataSet ));
        // console.log( JSON.stringify( graphOptions ));

        var ctx = document.getElementById(ele_id).getContext("2d");
        var sensorGraph = new Chart(ctx, {
            type: "scatter",
            data: {
                datasets: graphDataSet
            },
            options: graphOptions
        });
    };// ./populateScatterChart(…)

    //{{--
    // Chart.defaults.global.animationSteps = 50;
    // Chart.defaults.global.tooltipYPadding = 16;
    // Chart.defaults.global.tooltipCornerRadius = 0;
    // Chart.defaults.global.tooltipTitleFontStyle = "normal";
    //
    // Chart.defaults.global.tooltipFillColor = "rgba(0,160,0,0.8)";
    // Chart.defaults.global.animationEasing = "easeOutBounce";
    // Chart.defaults.global.scaleLineColor = "black";
    // Chart.defaults.global.scaleFontSize = 12;
    // Chart.defaults.global.scaleBeginAtZero= true;
    //--}} common settings for measurement reading charts

    Chart.defaults.global.responsive = true;
    Chart.defaults.global.responsiveAnimationDuration = 0;
    Chart.defaults.global.defaultFontFamily = "'Lato', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.global.defaultFontColor = "black";
    Chart.defaults.global.defaultFontSize = 12;
    Chart.defaults.global.legend.display = false;
    Chart.defaults.global.hover.mode = "nearest";
    Chart.defaults.global.hover.intersect = false;
    Chart.defaults.global.hover.animationDuration = 1000;

    // Data set configuration: all sensors, all graph sizes
    base.chartDataSet.all = {
        fill: false,
        lineTension: 0,
        spanGaps: false,
        borderColor: "rgba(14,17,176,1)",
        pointBackgroundColor: "rgba(1,246,6,1)",
        pointHoverBackgroundColor: "rgba(75,192,192,1)",
        pointHoverBorderColor: "rgba(220,0,0,1)"
    };

    // Data set configuration: all sensors, small graph size
    base.chartDataSet.small = {
        pointRadius: 1,
        pointHoverRadius: 5,
        pointHoverBorderWidth: 1
    };
    // Data set configuration: all sensors, big graph size
    base.chartDataSet.big = {};
    // Data set configuration: all sensors, full graph size
    // Only need 'full' entry when have a single sensor, and with that name,
    // can merge most content from any sensor
    base.chartDataSet.full = {
        pointRadius: {{ $sensor['point_count'] > 170 ? 1 :( $sensor['point_count'] > 50 ? 2 : 3 ) }},
        pointHoverRadius: {{ $sensor['point_count'] > 120 ? 6 : 12 }},
        pointHoverBorderWidth: {{ $sensor['point_count'] > 120 ? 1 : 2 }},
        label: "{{ Lang::get('bioreactor.end_time_prefix') }}" + bin.fmtEndingDate("{{ $sensor['end_datetime'] }}") + "{{ Lang::get('bioreactor.end_time_suffix') }}"
    };

    // Chart options: all sensors, all graph sizes
    base.chartOptions.all = {
        animation: {
            duration: 0
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: false
                },
                scaleLabel: {
                    display: true,
                    fontSize: 16,
                    fontStyle: 'bold'
                }
            }],
            xAxes: [{
                type: "time",
                position: "bottom",
                time: {
                    displayFormats: {
                        minute: "HH:mm",
                        hour: "h a"
                    }
                },
                gridLines: {
                    display: false
                },
                scaleLabel: {
                    display: true,
                    fontSize: 16,
                    fontStyle: 'bold'
                }
            }]
        },
        tooltips: {
            backgroundColor: "rgba( 0, 0, 0, 0.6)",
            displayColors: false,
            callbacks: {
                label: function (tooltipItem, data) {
                    return tooltipItem.yLabel + " @" + bin.fmtTooltipDate(tooltipItem.xLabel);
                }
            }
        }
    };

    // Chart options: all sensors, small graph size
    base.chartOptions.small = {
        scales: {
            yAxes: [{
                scaleLabel: {
                    fontSize: 12,
                    fontStyle: 'normal'
                },
                ticks: {
                    fontSize: 8
                }
            }],
            xAxes: [{
                display: false,
                // Keep time information even though not being displayed,
                // so data expand to left/right edges of chart area
                time: {
                    unit: "minute",
                    unitStepSize: 5
                }
            }]
        },
        tooltips: {
            backgroundColor: "rgba( 0, 0, 0, 0.6)",
            displayColors: false,
            callbacks: {
                label: function (tooltipItem, data) {
                    return tooltipItem.yLabel + " @" + bin.fmtSmallTooltipDate(tooltipItem.xLabel);
                }
            }
        },
        title: {
            display: false
        }
    };

    // Chart options: all sensors, big graph size
    base.chartOptions.big = {
        legend: {
            display: true,
            position: "bottom",
            onClick: null,
            onHover: null,
            labels: {
                boxWidth: 0
            }
        },
        scales: {
            xAxes: [{
                scaleLabel: {
                    labelString: "{{ Lang::get('bioreactor.time_axis_hhmm') }}"
                },
                time: {
                    unit: "minute",
                    unitStepSize: 10
                }
            }]
        },
        title: {
            display: false
        }
    };

    // Chart options: all sensors, full graph size
    // Only need 'full' entry when have a single sensor, and with that name,
    // can merge most content from any sensor
    base.chartOptions.full = {
        legend: {
            display: true,
            position: "bottom",
            onClick: null,
            onHover: null,
            labels: {
                boxWidth: 0
            }
        },
        scales: {
            yAxes: [{
                scaleLabel: {
                    labelString: "{{ Lang::get('bioreactor.' . $sensor_name . '_axis_full') }}"
                }
            }],
            xAxes: [{
                scaleLabel: {
                    labelString: "{{ $interval_count > 10 ? Lang::get('bioreactor.time_axis_time') : Lang::get('bioreactor.time_axis_hhmm') }}"
                },
                time: {
                    unit: "{{ $interval_count > 108 ? 'day' :( $interval_count > 10 ? 'hour' : 'minute' )}}",
                    unitStepSize: {{ $interval_count > 744 ? 7 :( $interval_count > 108 ? 1 :( $interval_count > 57 ? 3 :($interval_count > 33 ? 2 :( $interval_count > 10 ? 1 :( $interval_count > 4 ? 30 :( $interval_count > 2 ? 10 : 5 )))))) }}
                }
            }]
        },
        title: {
            display: true,
            fontSize: 20,
            text: "{{ Lang::get('bioreactor.' . $sensor_name . '_chart_title_full') }}"
        }
    };
    // TODO add sensor specific units to tooltip callback: $sensor_units ?? (which could be null)
    // Lang::get ?? are units language specific here?  international units?
    // IDEA for refactoring careful of difference between page and chart specific content
}());// anonymous function()
</script>
<!-- End of common_line_chart.blade.php -->
