<html>
<head>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<link rel="stylesheet" type="text/css" href="css/chart.css" />
<script src='js/dropdown.js'></script>

<script>
/* Mainigie */

var Main = {
	chart : '',
	chardata : '',
	dropDownData : [
					{text: 'Select...'},
					{click: function() {getMood('ipad');},    text : 'iPad'},
					{click: function() {getMood('galaxytab');}, text: 'Galaxy Tab'},
					{click: function() {getMood('php');},    text : 'PHP'},
					{click: function() {getMood('python');}, text: 'Python'},
				   
			],
	positive : 0,
	negative : 0,
	neutral :0,
	setDDdata : function(x){dropDownData = x;}
}


function getMood(id){
$('#chart_box').fadeOut(300);
	$.get("api/stats.php", { object : id}, function(data){
									var mood = data;
									console.log(mood);
									Main.positive = mood['positive'];
									Main.negative = mood['negative'];
									Main.neutral = mood['neutral'];
									console.log(Main);
									drawChart();
									$('#chart_box').fadeIn(500);
								}, "json");
}


      function drawChart() {
        Main.chardata = new google.visualization.DataTable();
        Main.chardata.addColumn('string', 'Mood');
        Main.chardata.addColumn('number', 'Emotioncount');
        Main.chardata.addRows(3);
        Main.chardata.setValue(0, 0, ':)');
        Main.chardata.setValue(0, 1, Main.positive);
        Main.chardata.setValue(1, 0, ':(');
        Main.chardata.setValue(1, 1, Main.negative);
        Main.chardata.setValue(2, 0, ': |');
		Main.chardata.setValue(2, 1, Main.neutral);


		Main.chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        Main.chart.draw(Main.chardata, {width: 500, height: 250, title: 'TweetMood', is3D:true, backgroundColor:'none'});
      }
	  
     google.load("visualization", "1", {packages:["corechart"]});
     //google.setOnLoadCallback(drawChart);
	 

</script>


</head>
<body>


<div id="container">
<script>

new Dropdown({
			mode: 'form',
			data: Main.dropDownData,
			fieldName: 'product'
		});
</script>

</div>

<div id='chart_box'>
	<div id="chart_div"></div>
 </div>
</body>
</html>
