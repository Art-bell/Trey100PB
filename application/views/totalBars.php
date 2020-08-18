<script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>

<button>Click me</button>

<script type="text/javascript">
	$('button').click(function() {
		$.ajax({
			type:'GET',
			url: '/nav/executeBarCount'
		})
	})
</script>