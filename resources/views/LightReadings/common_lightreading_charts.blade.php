  <script type="text/javascript">

    var small_light_lineChartData = {
    labels: [@foreach ($x_lightreading_data as $pt)".",@endforeach],
    datasets: [{
    fillColor: "rgba(220,220,220,0)",
    strokeColor: "rgba(220,180,0,1)",
    pointColor: "rgba(220,180,0,1)",
    data: [@foreach ($y_lightreading_data as $pt)"{{ $pt }}",@endforeach]
    }]
    }

    var big_light_lineChartData = {
    labels: [@foreach ($x_lightreading_data as $pt)"{{ $pt }}",@endforeach],
    datasets: [{
    fillColor: "rgba(220,220,220,0)",
    strokeColor: "rgba(220,180,0,1)",
    pointColor: "rgba(220,180,0,1)",
    data: [@foreach ($y_lightreading_data as $pt)"{{ $pt }}",@endforeach]
    }]
    }

    Chart.defaults.global.animationSteps = 50;
    Chart.defaults.global.tooltipYPadding = 16;
    Chart.defaults.global.tooltipCornerRadius = 0;
    Chart.defaults.global.tooltipTitleFontStyle = "normal";
    Chart.defaults.global.tooltipFillColor = "rgba(0,160,0,0.8)";
    Chart.defaults.global.animationEasing = "easeOutBounce";
    Chart.defaults.global.responsive = true;
    Chart.defaults.global.scaleLineColor = "black";
    Chart.defaults.global.scaleFontSize = 12;
    Chart.defaults.global.scaleBeginAtZero= true;


    var ctx = document.getElementById("light_canvas").getContext("2d");

    var LineChartDemo = new Chart(ctx).Line(small_light_lineChartData, {
    showTooltips:false,
    pointDotRadius: 2,
    bezierCurve: false,
    scaleShowVerticalLines: false,
    scaleGridLineColor: "black"
    });

    $(document).ready(function(){
    $("#light_modal").on('shown.bs.modal', function () {
    var big_ctx = document.getElementById("big_light_canvas").getContext("2d");

    var LineChartDemo = new Chart(big_ctx).Line(big_light_lineChartData, {
    pointDotRadius: 5,
    bezierCurve: false,
    scaleShowVerticalLines: false,
    scaleGridLineColor: "black"
    });
    });
    });

  </script>
