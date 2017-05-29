<script>
// common line chart options for light graphs
/*global $ */
/*jslint browser */

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
                labelString: "Time (HH:MM) ending at: {{ $end_datetime }}",
                fontSize: 14
            }
        }]
    }
};

var small_lightOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            ticks: {
              suggestedMin: 0,
            },
            scaleLabel: {
                labelString: "µmol γ/(m^2 S)",
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
var big_lightOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        xAxes: [{
            scaleLabel: {
                labelString: "Time (HH:MM)"
            }
        }]
    },
    title: {
        text: "Light vs Time"
    }
});
var full_lightOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "Photo BioReactor Light vs Time"
    }
});
// alert ("small light:"+JSON.stringify(small_lightOptions));

</script>
