<html>
<head>
	<meta name='robots' content='noindex, nofollow' />
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'/>
	<title>{mojo:site:site_name} : {mojo:page:page_title}</title>
	<style type='text/css'>
		* {
			margin: 0;
			padding: 0;
		}

		body {
			background: #eee;
		}

		#outer > #content, h1, #footer > p {
			width: 80%;
			margin: auto;
		}

		#content {
			padding: 30px 0;
		}

		.main_page {
			margin: 0 75px;
		}

		#header {
			background: #666;
			color: #fff;
			border-bottom: 5px solid #888;
			line-height: 100px;
		}

		#footer {
			background: #ccc;
			border-top: 1px solid #888;
			padding: 15px;
		}

	</style>
</head>
<body>

<div id='outer'>

	<div id='header'>
		<h1>{mojo:site:site_name} : {mojo:page:page_title}</h1>
	</div>

	<div class="main_page">
		<div id='editable_content' class='mojo_page_region'>
			{mojo:page:page_region id="editable_content"}
		</div>
	</div>

	<div id='footer'>
		<p>This demo site is proudly powered by MojoMotor, and is built as a demonstration of its capabilities.<br />
		{mojo:site:login}</p>
	</div>

</div>

</body>
</html>