<script>
// common line chart options for light graphs
/*global $ */
/*jslint browser */

// Need to find a way to prevent the php expansion from converting the utf-8
// character to encoded form.  I *want& µ here, not &micro;
// alert("{{ Lang::get('bioreactor.intensity_small') }}");
// alert("{{{ Lang::get('bioreactor.intensity_small') }}}");
// alert("{{ 'µ' }}");
// alert("µ");
// Only the final hard-coded string actually works
// labelString: "{{ Lang::get('bioreactor.intensity_axis_full') }}",
var baseGraphOptions = {
    scales: {
        yAxes: [{
            scaleLabel: {
                display: true,
                labelString: "µmol photons/(m^2 S)",
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

// labelString: "{{ Lang::get('bioreactor.intensity_axis_small') }}",
var small_lightOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            ticks: {
              suggestedMin: 0,
            },
            scaleLabel: {
                labelString: "µmol γ/(m^2 S)",
                fontSize: 10
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
// labelString: "{{ Lang::get('bioreactor.intensity_axis_big') }}",
var big_lightOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            scaleLabel: {
                labelString: "µmol photons/(m^2 S)",
            }
        }],
        xAxes: [{
            scaleLabel: {
                labelString: "{{ Lang::get('bioreactor.time_axis_big') }}"
            }
        }]
    },
    title: {
        text: "{{ Lang::get('bioreactor.chart_light_title_big') }}"
    }
});
var full_lightOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "{{ Lang::get('bioreactor.chart_light_title_full') }}"
    }
});

</script>
