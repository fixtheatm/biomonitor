@extends('layouts.app')

@section('content')

<div class="panel panel-success">
<div class="panel panel-primary">

  @include('common_detail_header', array('show_map' => false))

  <div class="panel-body">
    <ul class="nav nav-pills">
      <li class="active"><a data-toggle="pill" href="#temp_graph">Graph</a></li>
      <li><a data-toggle="pill" href="#temp_list">Data Point List</a></li>
    </ul>

    <div class="tab-content">
      <div id="temp_graph" class="tab-pane fade in active">
        <div style="height:3px"></div>
        <div>
           <canvas id="temp_canvas"></canvas>
        </div>
      </div>
      <div id="temp_list" class="tab-pane fade">
        <div style="height:3px"></div>
        <div class="table table-condensed table-responsive" style='overflow-y:scroll;height:275px'>
          <table class="table">
            <thead>
              <tr class="info">
                <th>Date and Time</th>
                <th>Temperature</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($dbdata as $temperature)
              <tr>
                <td>{{ $temperature->recorded_on }}</td>
                <td>{{ $temperature->temperature }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
         </div>
       </div>
    </div>
  </div>
</div>
</div>

@stop



@section('footer_js')
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.bundle.min.js"></script>

<script>
  var temp_lineChartData = {
    labels: [@foreach ($x_temperature_data as $pt)"{{ $pt }}",@endforeach],
    datasets: [{
      fillColor: "rgba(220,220,220,0)",
      strokeColor: "rgba(220,180,0,1)",
      pointColor: "rgba(220,180,0,1)",
      data: [@foreach ($y_temperature_data as $pt)"{{ $pt }}",@endforeach]
    }]
  }

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
  //
  // var ctx = document.getElementById("temp_canvas").getContext("2d");
  //
  // var LineChartDemo = new Chart(ctx).Line(temp_lineChartData, {
  //   pointDotRadius: 5,
  //   bezierCurve: false,
  //   scaleShowVerticalLines: false,
  //   scaleGridLineColor: "black"
  // });

  Chart.defaults.global.responsive = true;
  Chart.defaults.global.defaultFontColor = '#000';
  Chart.defaults.global.defaultFontSize = 12;
  Chart.defaults.global.title.display = true;
  Chart.defaults.global.title.display = 'unimplemented';
  Chart.defaults.global.title.fontSize = 16;
  Chart.defaults.global.legend.display = false;
  // Chart.scaleService.updateScaleDefault( 'linear', {
  //   ticks: {
  //     fontSize: 12,
  //   }
  // });
  var tempOptions = {
    scales: {
      yAxes: [{
        ticks: {
          callback: function(label, index, labels) {
            return label+"°";
          },
          suggestedMin: 20.0,
          suggestedMax: 30.0,
          stepSize: 2,
        },
        scaleLabel: {
          display: true,
          labelString: "Degrees Celsius",
          fontSize: 14,
        },
      }],
      xAxes: [{
        scaleLabel: {
          display: true,
          labelString: "HH:MM ending at: {{ $end_datetime }}",
          fontSize: 14,
        },
      }]
    },
    title: {
      text: "BioReactor Temperature versus Time",
    },
  };

  // backgroundColor: "rgba(220,180,0,1)",
  // borderColor: "rgba(220,180,0,1)",
  // borderCapStyle: 'butt',
  // borderDash: [],
  // borderDashOffset: 0.0,
  // borderJoinStyle: 'miter',
  // pointBackgroundColor: "rgba(220,180,0,1)",
  // pointHoverRadius: 8,
  // pointHoverBackgroundColor: "rgba(75,192,192,1)",
  // pointHoverBorderColor: "rgba(220,220,220,1)",
  // pointBorderColor: "rgba(220,220,220,1)",
  // pointBorderWidth: 1,
  // pointHitRadius: 10,
  // pointStyle: "circle",
  var tempData = {
    labels: [@foreach ($x_temperature_data as $pt)"{{ $pt }}",@endforeach],
    datasets: [
      {
        data: [@foreach ($y_temperature_data as $pt)"{{ $pt }}",@endforeach],
        fill: false,
        lineTension: 0,
        pointRadius: 5,
        pointHoverRadius: 12,
        spanGaps: false,
        borderColor: "rgba(0,180,180,1)",
        pointBackgroundColor: "rgba(220,180,0,1)",
        pointHoverBackgroundColor: "rgba(75,192,192,1)",
        pointHoverBorderColor: "rgba(220,0,0,1)",
        pointHoverBorderWidth: 2,
      }
    ]
  };

  var ctx = document.getElementById("temp_canvas").getContext("2d");

  var temperatureChart = new Chart( ctx, {
    type: 'line',
    data: tempData,
    options: tempOptions
  });
</script>

@stop
