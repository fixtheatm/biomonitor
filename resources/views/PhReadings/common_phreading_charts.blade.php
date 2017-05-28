<script>
// common line chart options for ph graphs
/*global $ */
/*jslint browser */

var baseGraphOptions = {
    scales: {
        yAxes: [{
            scaleLabel: {
                display: true,
                labelString: "pH",
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

var small_phOptions = $.extend(true, {}, baseGraphOptions, {
    scales: {
        yAxes: [{
            ticks: {
              suggestedMin: 0,
            },
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
                labelString: "Time (HH:MM)"
            }
        }]
    },
    title: {
        text: "pH vs Time"
    }
});
var full_phOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "Photo BioReactor pH vs Time"
    }
});
// alert ("small ph:"+JSON.stringify(small_phOptions));

</script>
