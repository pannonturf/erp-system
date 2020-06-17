<?php
////////////////////////////////////////////
// Loading graphs for customer statistics //
////////////////////////////////////////////

//Show right year in graphs   
if (isset($_GET['year'])) {
    $year = $_GET['year'];
}
else {
    $year = date("Y");
}
?>

<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});

  google.charts.setOnLoadCallback(drawPiechart1);
  google.charts.setOnLoadCallback(drawPiechart2);

  function drawPiechart1() {
    var year = "<?php echo $year; ?>";

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData8.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        title: 'm2 - Vevő csoport',
        pieHole: 0.4,
        legend: 'none',
        pieSliceText: 'label',
        width: 230,
        height: 230,
        colors: ['#9ed5cd','#44a7cb','#2e62a1','#192574']    
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('piechart1'));
    chart.draw(data, options);
  }


  function drawPiechart2() {
    var year = "<?php echo $year; ?>";

    var jsonData = $.ajax({
        type: "POST",
        url: "tools/getData9.php",
        dataType: "json",
        data: {year: year},
        async: false
        }).responseText;
        
    // Create our data table out of JSON data loaded from server.
    var data = new google.visualization.DataTable(jsonData);

    // Set chart options
    var options = {
        title: 'm2 - Nemzetiség',
        pieHole: 0.4,
        legend: 'none',
        pieSliceText: 'label',
        width: 230,
        height: 230,
        colors: ['#6b439c', '#b968ad', '#84d4c9', '#00a88f']   
    };

    // Instantiate and draw our chart, passing in some options.
    var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
    chart.draw(data, options);
  }

  

</script>
