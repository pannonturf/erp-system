<?php
/////////////////////////////////////////
// Loading graphs for sales statistics //
/////////////////////////////////////////

//Show right year in graphs   
if (isset($_GET['year'])) {
    $year = $_GET['year'];
}
else {
    $year = date("Y");
}
?>

<script type="text/javascript">
  google.charts.load('current', {'packages':['line', 'bar', 'corechart']});

  google.charts.setOnLoadCallback(drawBarchart);
  google.charts.setOnLoadCallback(drawLinechart);
  google.charts.setOnLoadCallback(drawPiechart1);
  google.charts.setOnLoadCallback(drawPiechart2);
  google.charts.setOnLoadCallback(drawPiechart3);
  google.charts.setOnLoadCallback(drawPiechart4);
  google.charts.setOnLoadCallback(drawPiechart5);

  function drawBarchart() {
    var year = "<?php echo $year; ?>";

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        isStacked: true,
        width: 800,
        height: 400,
        chart: {
            title: 'Menge Jahresvergleich',
            subtitle: 'in ha'
        },
        vAxis: {
            viewWindow: {
                min: 0,
                max: 16
            }
        },
        series: {
            2: {
                targetAxisIndex: 1
            },
            3: {
                targetAxisIndex: 1
            }
        }
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.charts.Bar(document.getElementById('barchart'));
    chart.draw(data, google.charts.Bar.convertOptions(options));
  }


  function drawLinechart() {
    var year = "<?php echo $year; ?>";

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData2.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        chart: {
          title: 'Menge Jahresvergleich akkumuliert',
          subtitle: 'in ha'
        },
        width: 750,
        height: 400  
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.charts.Line(document.getElementById('linechart'));
    chart.draw(data, google.charts.Line.convertOptions(options));
  }

  function drawPiechart1() {
    var year = "<?php echo $year; ?>";

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData3.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        title: 'Type 1',
        pieHole: 0.4,
        legend: 'none',
        pieSliceText: 'label',
        width: 300,
        height: 300,
        colors: ['#3276b5','#fba64c']    
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('piechart1'));
    chart.draw(data, options);
  }


  function drawPiechart2() {
    var year = "<?php echo $year; ?>";

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData4.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        title: 'Type 2',
        pieHole: 0.4,
        legend: 'none',
        pieSliceText: 'label',
        width: 300,
        height: 300,
        colors: ['#0ea50e','orange']   
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
    chart.draw(data, options);
  }

  function drawPiechart3() {
    var year = "<?php echo $year; ?>";  

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData5.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        title: 'Type 3',
        pieHole: 0.4,
        legend: 'none',
        pieSliceText: 'label',
        width: 300,
        height: 300,
        colors: ['#0ea50e', '#db4437']   
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('piechart3'));
    chart.draw(data, options);
  }

  function drawPiechart4() {
    var year = "<?php echo $year; ?>";

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData6.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        title: 'Fizetés',
        pieHole: 0.4,
        legend: 'none',
        pieSliceText: 'label',
        width: 300,
        height: 300,
        colors: ['#00a88f', '#84d4c9']   
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('piechart4'));
    chart.draw(data, options);
  }

  function drawPiechart5() {
    var year = "<?php echo $year; ?>";

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData7.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        title: 'Nagytekercs',
        pieHole: 0.4,
        legend: 'none',
        pieSliceText: 'label',
        width: 300,
        height: 300,
        colors: ['#6b439c', '#b968ad', '#84d4c9']   
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('piechart5'));
    chart.draw(data, options);
  }



</script>