<!DOCTYPE HTML>
<html>
<head>
<link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@100;200;400&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
</head>
<body>
<div id="chartContainer" style="height: 300px; width: 100%;"></div>
<script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
<script>
window.onload = function () {

//Better to construct options first and then pass it as a parameter
$.ajax({
	type: "GET",
	url: "/welcome/plotAlbum"
}).done(function(items) {
	var points = []
	var allData = JSON.parse(items)
	var i = 0;
	for ([key, value] of Object.entries(allData).sort(function(a,b) {
		return b[1] - a[1];
	})) {
	  	points.push({label: key, y:value})
	  	i += 1;
	  	if (i == 10)
	  	{
	  		break;
	  	}
	}
console.log(Object.entries(allData).sort(function(a,b) {
		return b[1] - a[1];
	}))
	var options = {
		theme: "dark1",
		title: {
			fontFamily: "Montserrat",
			text: "Lil Uzi Vert - Luv is Rage"              
		},
		data: [              
		{
			// Change type to "doughnut", "line", "splineArea", etc.
			indexLabelFontSize: 32,
			indexLabelFontFamily: "Josefin Sans",
			color: "red",
			type: "column",
			dataPoints: points
		}
		]
	};

	$("#chartContainer").CanvasJSChart(options);
}).fail(function(data) {
	console.log(data.responseText);
})
	
}
</script>
</body>
</html>