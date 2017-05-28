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
                labelString: "ml/min",
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
                labelString: "Time (HH:MM)"
            }
        }]
    },
    title: {
        text: "Gas Flow vs Time"
    }
});
var full_gasflowOptions = $.extend(true, {}, baseGraphOptions, {
    title: {
        text: "Photo BioReactor Gas Flow vs Time"
    }
});
// alert ("small gas flow:"+JSON.stringify(small_gasflowOptions));

</script>
