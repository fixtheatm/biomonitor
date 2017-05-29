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
                labelString: "{{ Lang::get('bioreactor.ml_per_min_full') }}",
                fontSize: 14
            }
        }],
        xAxes: [{
            scaleLabel: {
                display: true,
                labelString: "{{ Lang::get('bioreactor.before_end_time') }}{{ $end_datetime }}{{ Lang::get('bioreactor.after_end_time') }}",
                fontSize: 14
            }
        }]
    }
};

var small_gasflowOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            scaleLabel: {
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
        text: "{{ Lang::get('bioreactor.chart_title_big') }}"
    }
});
var full_gasflowOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "{{ Lang::get('bioreactor.chart_title_full') }}"
    }
});
// alert ("small gas flow:"+JSON.stringify(small_gasflowOptions));

</script>
