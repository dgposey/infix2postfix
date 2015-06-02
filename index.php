<!doctype html>
<html>
<head>
	<title>Infix to Postfix Demonstration</title>
	<style type="text/css">
		#wrapper {
			width: 600px;
			margin: 0 auto;
			border: 1px solid #000000;
		}
		#header {
			background-color: navy;
		}
			h1, h2 {
				margin-top: 0px;
				text-align: center;
				color: #FFFFFF;
			}
		#result {
			min-height: 100px;
			border: 1px solid #000000;
			margin: 3px;
		}
	</style>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#mainform").submit(function(e){
				//alert("clicked");
				e.preventDefault();
				$.ajax("http://www.davidgposey.com/portfolio/infix2postfix/math.php?expr=" + encodeURIComponent($("#expr").val())).done(function(result){
					$("#result").html("infix: " + result.infix + "<br />postfix: " + result.postfix);
					//$("#result").html(result);
				});
			});
		});
	</script>
</head>
<body>
	<div id="wrapper">
		<div id="header">
			<h1>Infix to Postfix Demonstration</h1>
			<h2>A PHP-Based Web Service</h2>
		</div>
		<div id="main">
			<noscript>
				<p>
					Sorry folks, this demonstration of my PHP web service requires Javascript to run.
				</p>
			</noscript>
			<p>Enter an infix expression below.  An infix expression is a standard mathematical expression, such as 4+3*(2-4)</p>
			<form id="mainform" method="get" action="math.php">
				<input type="text" id="expr" name="expr" value="4+3*(2-4)" />
				<input type="submit" id="submit" value="To Postfix" />
			</form>
			<div id="result">
				
			</div>
		</div>
	</div>
</body>
</html>