<script>
/*global Chart */
/*jslint browser, devel */
/*property
    borderColor, defaultFontColor, defaultFontSize, defaults, display, fill,
    fontSize, global, legend, lineTension, pointBackgroundColor,
    pointHoverBackgroundColor, pointHoverBorderColor, responsive, spanGaps,
    stringify, title
*/

// common settings for line charts

{{--
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
--}}
Chart.defaults.global.responsive = true;
Chart.defaults.global.defaultFontFamily = "'Lato', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
Chart.defaults.global.defaultFontColor = "#000";
Chart.defaults.global.defaultFontSize = 12;
Chart.defaults.global.title.display = true;
Chart.defaults.global.title.display = "unimplemented";
Chart.defaults.global.title.fontSize = 16;
Chart.defaults.global.legend.display = false;

var lineChartTemplate = {
    fill: false,
    lineTension: 0,
    spanGaps: false,
    borderColor: "rgba(0,180,180,1)",
    pointBackgroundColor: "rgba(220,180,0,1)",
    pointHoverBackgroundColor: "rgba(75,192,192,1)",
    pointHoverBorderColor: "rgba(220,0,0,1)"
};

</script>
