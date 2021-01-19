<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" />
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.3.0/gridstack.min.css" />
	<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
	<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'></script>
	<script type="text/javascript" src='https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.4/lodash.min.js'></script>
	<script type="text/javascript" src='//cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.3.0/gridstack.min.js'></script>
	<script type="text/javascript" src='//cdnjs.cloudflare.com/ajax/libs/gridstack.js/0.3.0/gridstack.jQueryUI.min.js'></script>
	<style>
		.grid-stack-item{
			background: #ccc;
		}
	</style>
</head>

<body>

<h1>Test</h1>
<button>Save</button>
<div class="grid-stack">
	<div class="grid-stack-item"
	     data-gs-x="0" data-gs-y="0"
	     data-gs-width="1" data-gs-height="1" data-name='Jay 1'>
		<div class="grid-stack-item-content">1</div>
	</div>
	<div class="grid-stack-item"
	     data-gs-x="1" data-gs-y="0"
	     data-gs-width="1" data-gs-height="1" data-name='Jay 2'>
		<div class="grid-stack-item-content">2</div>
	</div>
</div>


<script type="text/javascript">
	$(function () {
		var options = {
			cellHeight: 80,
			verticalMargin: 10
		};
		$('.grid-stack').gridstack(options);

		function toJSON(s){
			var arr =[];
			$(s +  " .grid-stack-item").each(function(){
				var g = $(this);
				arr.push({
					x: g.attr('data-gs-x'),
					y: g.attr('data-gs-y'),
					width: g.attr('data-gs-width'),
					height: g.attr('data-gs-height'),
					name: g.attr('data-name')
				})
			});
			return JSON.stringify(arr);
		}
		$('button').click(function(){
			console.log(toJSON('.grid-stack'));
		});
	});
</script>
</body>
</html>