<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Document</title>
	<link rel="stylesheet" href="../css/materialize.min.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<style>

		.below-content-overlay a {
			color:#fff !important;
		}

		.main_nav a{
			color:#fff !important;
		}


		.login-container i {
			color:#fff;
		}

		@media only screen and (max-width: 800px) {
			.overlay-black-login {
				position: absolute;
				top: 0;
				left: 5%;
				width: 90%;
				height: 100%;
				background: #000;
				opacity: 0.7;
				z-index: -2;
			}
		}
		@media only screen and (max-width: 500px) {
			.overlay-black-login {
				position: absolute;
				top: 0;
				left: 1%;
				width: 98%;
				height: 100%;
				background: #000;
				opacity: 0.7;
				z-index: -2;
			}
		}


		header, main, footer {
			padding-left: 300px;
		}

		@media only screen and (max-width : 992px) {
			header, main, footer {
				padding-left: 0;
			}
		}
		#main{
			padding:15px;
		}
		#img-logo{
			height: 100%;
		}

		@media only screen and (max-width: 800px) {

			/* Force table to not be like tables anymore */
			#no-more-tables table,
			#no-more-tables thead,
			#no-more-tables tbody,
			#no-more-tables th,
			#no-more-tables td,
			#no-more-tables tr {
				display: block;
			}

			/* Hide table headers (but not display: none;, for accessibility) */
			#no-more-tables thead tr {
				position: absolute;
				top: -9999px;
				left: -9999px;
			}

			#no-more-tables tr { border: 1px solid #ccc; }

			#no-more-tables td {
				/* Behave  like a "row" */
				border: none;
				border-bottom: 1px solid #eee;
				position: relative;
				padding-left: 50%;
				white-space: normal;
				text-align:left;
			}

			#no-more-tables td:before {
				/* Now like a table header */
				position: absolute;
				/* Top/left values mimic padding */
				top: 6px;
				left: 6px;
				width: 45%;
				padding-right: 10px;
				white-space: nowrap;
				text-align:left;
				font-weight: bold;
			}

			/*
			Label the data
			*/
			#no-more-tables td:before { content: attr(data-title); }
		}

	</style>

</head>
<body>