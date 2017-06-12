<script>
/*global Chart */
/*jslint browser, devel */

@if ( isset( $date_constants ))
// Some common (cross sensor) functions and view data
var weekday_names = [@foreach ($date_constants['weekday']  as $nm)"{{ $nm }}",@endforeach];
var month_names = [@foreach ($date_constants['month']  as $nm)"{{ $nm }}",@endforeach];

/**
 * convert a database timestamp string to a date object
 *
 * @param String timestamp "yyyy-mm-dd hh:mm:ss"
 * @returns Date
 */
var timestamp2date = function (timestamp) {
    "use strict";
    var dttm = timestamp.split(" ");
    var dt = dttm[0].split("-");
    var tm = dttm[1].split(":");
    return new Date(Date.UTC(dt[0], dt[1] - 1, dt[2], tm[0], tm[1], tm[2]));
};

/**
 * format date to "Www Mmm DD YYYY HH:MM (TZ)"
 *
 * @param Date timestamp
 * @returns String date as Www Mmm DD YYYY HH:MM (TZ)
 */
var fmt_www_mmm_dd_yyyy_hh_mm_tx = function (full) {
    "use strict";
    var hr = "0" + full.getHours();
    var mn = "0" + full.getMinutes();
    var pts = full.toString().split(" ");
    var fmtd = weekday_names[full.getDay()] + " " + month_names[full.getMonth()] + " " + full.getDate() + " " + full.getFullYear() + " " + hr.substr(hr.length - 2) + ":" + mn.substr(mn.length - 2) + " " + pts[pts.length - 1];
    return fmtd;
};

/**
 * format date for display as the ending date for the graph
 *
 * @param String timestamp "yyyy-mm-dd hh:mm:ss"
 * @returns Www Mmm DD YYYY HH:MM (TZ)
 */
var fmtEndingDate = function (dt) {
    "use strict";
    return fmt_www_mmm_dd_yyyy_hh_mm_tx(timestamp2date(dt));
};

/**
 * format date for display in tooltips
 *
 * @param int dt epoch (unix) time
 * @returns Www Mmm DD YYYY HH:MM (TZ)
 */
var fmtTooltipDate = function (dt) {
    "use strict";
    return fmt_www_mmm_dd_yyyy_hh_mm_tx(new Date(dt));
};

@endif
@if ( isset( $xy_data ))
// The raw data points that will be used with whatever chart is being displated
var graphPoints = [@foreach ($xy_data as $pt){ x: {{ $pt['x'] }}000, y: {{ $pt['y'] }} },@endforeach];

@endif
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
Chart.defaults.global.defaultFontColor = "#000";
Chart.defaults.global.defaultFontSize = 12;
Chart.defaults.global.title.display = true;
Chart.defaults.global.title.display = "unimplemented";
Chart.defaults.global.title.fontSize = 16;
Chart.defaults.global.legend.display = false;
Chart.defaults.global.hover.mode = "nearest";
Chart.defaults.global.hover.intersect = false;
Chart.defaults.global.hover.animationDuration = 1000;

var baseDataset = {
    fill: false,
    lineTension: 0,
    spanGaps: false,
    borderColor: "rgba(14,17,176,1)",
    pointBackgroundColor: "rgba(1,246,6,1)",
    pointHoverBackgroundColor: "rgba(75,192,192,1)",
    pointHoverBorderColor: "rgba(220,0,0,1)"
};
@if ( isset( $date_constants ))
var fullDataset = {
    pointRadius: {{ $point_count > 120 ? 1 : 3 }},
    pointHoverRadius: {{ $point_count > 120 ? 2 : 12 }},
    pointHoverBorderWidth:  {{ $point_count > 120 ? 1 : 2 }},
    label: "{{ Lang::get('bioreactor.end_time_prefix') }}" + fmtEndingDate("{{ $end_datetime }}") + "{{ Lang::get('bioreactor.end_time_suffix') }}"
};

// Options for all graphs: all sensors, all sizes
var baseOptions = {
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
                fontSize: 14
            }
        }],
        xAxes: [{
            type: "time",
            position: "bottom",
            time: {
                displayFormats: {
                    minute: "HH:mm",
                    hour: "h a"
                },
                unit: "{{ $interval_count > 108 ? 'day' :( $interval_count > 10 ? 'hour' : 'minute' )}}",
                unitStepSize: {{ $interval_count > 744 ? 7 :( $interval_count > 108 ? 1 :( $interval_count > 57 ? 3 :($interval_count > 33 ? 2 :( $interval_count > 10 ? 1 :( $interval_count > 4 ? 30 :( $interval_count > 2 ? 10 : 5 ))))))}}
            },
            gridLines: {
                display: false
            },
            scaleLabel: {
                display: true,
                fontSize: 14
            }
        }]
    }
};
// TODO turn off display of x (time) axis ticks for small chart (or replace with axis label)

// Options for all full sized graphs, regardless of which sensor
var fullOptions = {
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
                labelString: "{{ Lang::get('bioreactor.time_axis_big') }}"
            }
        }],
        yAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.' . $sensor_name . '_axis_full') }}"
            }
        }]
    },
    tooltips: {
        backgroundColor: "rgba( 0, 0, 0, 0.6)",
        displayColors: false,
        callbacks: {
            label: function (tooltipItem, data) {
                "use strict";
                return tooltipItem.yLabel + " @" + fmtTooltipDate(tooltipItem.xLabel);
            }
        }
    },
    title: {
        text: "{{ Lang::get('bioreactor.chart_' . $sensor_name . '_title_full') }}"
    }
};
// TODO add sensor specific units to tooltip callback: $sensor_units ?? (which could be null)
// Lang::get ?? are units language specific here?  international units?
// IDEA for refactoring careful of difference between page and chart specific content
@endif

// TODO find and get rid of references to lineOptionsTemplate and baseGraphOptions
var lineChartTemplate = {
    fill: false,
    lineTension: 0,
    spanGaps: false,
    borderColor: "rgba(1,246,6,1)",
    pointBackgroundColor: "rgba(1,246,6,1)",
    pointHoverBackgroundColor: "rgba(75,192,192,1)",
    pointHoverBorderColor: "rgba(220,0,0,1)"
};
var lineOptionsTemplate = {
    scales: {
        yAxes: [{
            ticks: {
                beginAtZero: false
            },
            scaleLabel: {
                display: true,
                fontSize: 14
            }
        }],
        xAxes: [{
            gridLines: {
                display: false
            },
            scaleLabel: {
                display: true,
                fontSize: 14
            }

        }]
    }
};

</script>
