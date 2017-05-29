<script>
// common line chart options for ph graphs
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
                labelString: "{{ Lang::get('bioreactor.ph_axis_full') }}",
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

var small_phOptions = $.extend(true, {}, baseGraphOptions, {
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
var big_phOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        xAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.time_axis_big') }}"
            }
        }]
    },
    title: {
        text: "{{ Lang::get('bioreactor.chart_ph_title_big') }}"
    }
});
var full_phOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "{{ Lang::get('bioreactor.chart_ph_title_full') }}"
    }
});

</script>
