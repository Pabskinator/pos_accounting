
<?php
	session_start();
	if(!$_SESSION['user']) header('Location: login.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta charset="UTF-8">

	<meta name="description" content="">
	<meta name="author" content="Jayson Temporas">
	<meta http-equiv="content-type" content="text/html;charset=UTF-8">
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
	<meta name="HandheldFriendly" content="True" />
	<meta name="theme-color" content="#222222">
	<link rel='shortcut icon' href='css/logo.jpg?v=4'/>
	<link href="css/font.css" rel="stylesheet">
	<link rel="stylesheet" href="css/materialize.min.css">
	<link rel="stylesheet" href="css/animate.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

	<link rel="stylesheet" href="css/select2.css" />

	<style>
		/*
		 * Select2 v3.4.6 styles customization for Flat UI
		 */
		/*----------------------------------------------- Main select element ------------------------------------------------*/
		.select2-container .select2-choice {
			height: 41px; /* Jobsy form controls have 37px total height */
			border: 2px solid #bdc3c7;
			border-radius: 6px;
			outline: none;
			font: 15px/38px "Lato", Liberation Sans, Arial, sans-serif;
			color: #34495e;

			/* important - to keep height always as constant */
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;

			/* reset gradient */
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);

			-webkit-transition: border-color .25s;
			-moz-transition: border-color .25s;
			-ms-transition: border-color .25s;
			-o-transition: border-color .25s;
			transition: border-color .25s;
		}

		/* active state */
		.select2-container-active .select2-choice,
		.select2-container-active .select2-choices {
			border-color: #222;

			/* reset shadow */
			-webkit-box-shadow: none;
			box-shadow: none;
		}

		/* container state, when dropdown open */
		.select2-dropdown-open .select2-choice {
			border-bottom: none;
			border-radius: 6px 6px 0 0;
			padding-bottom: 2px;
			background-color: #fff;

			/* reset shadow */
			-webkit-box-shadow: none;
			box-shadow: none;

			/* reset gradient */
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		}

		/* container state, when dropup open */
		.select2-dropdown-open.select2-drop-above .select2-choice,
		.select2-dropdown-open.select2-drop-above .select2-choices {
			border: 2px solid #222;
			border-radius: 0 0 6px 6px;
			padding-bottom: 0;
			border-top: none;
			padding-top: 2px;
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		}

		/* disabled state */
		.select2-container.select2-container-disabled .select2-choice {
			background-color: #f4f6f6;
			border: 2px solid #d5dbdb;
			color: #d5dbdb;
			cursor: default;
		}

		/*-------------------------------------- X-icon (which clears selected result) ---------------------------------------*/
		.select2-container .select2-choice abbr {
			top: 12px;
		}

		.select2-container-active.select2-drop-above .select2-choice abbr {
			top: 14px;
		}

		/*---------------------------------------------------- Down-arrow ----------------------------------------------------*/
		.select2-container .select2-choice .select2-arrow {
			width: 22px;
			height: 27px;
			top: 5px;
			border: none;
			background: #fff;

			/* reset gradient */
			background-image: none;
			filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
		}

		.select2-dropdown-open.select2-drop-above .select2-choice .select2-arrow {
			top: 7px;
		}

		/*----------------------------------------------------- Dropdown -----------------------------------------------------*/
		/* dropdown with options */
		.select2-drop {
			-webkit-transition: border-color .25s;
			-moz-transition: border-color .25s;
			-ms-transition: border-color .25s;
			-o-transition: border-color .25s;
			transition: border-color .25s;

			/* reset shadow */
			-webkit-box-shadow: none;
			box-shadow: none;
		}

		/* dropup (case, when there is not enough space below the field) */
		.select2-drop.select2-drop-above {
			/* reset shadow */
			-webkit-box-shadow: none;
			box-shadow: none;
		}

		/* active dropdown */
		.select2-drop-active {
			border: 2px solid #222;
			border-top: none;
			border-radius: 0 0 6px 6px;
		}

		/* active dropup */
		.select2-drop.select2-drop-above.select2-drop-active {
			border-top: 2px solid #222;
			border-radius: 6px 6px 0 0;
		}

		.select2-results .select2-result {
			font: 15px/38px "Lato", Liberation Sans, Arial, sans-serif;
			color: #34495e;
		}

		.select2-results .select2-highlighted {
			background-color: #222;
			color: #fff;
		}

		/* help-text line */
		.select2-results .select2-no-results,
		.select2-results .select2-searching,
		.select2-results .select2-selection-limit {
			background: #fff;
			font: 15px/38px "Lato", Liberation Sans, Arial, sans-serif;
			color: #34495e;
		}

		/* "loading more results" message */
		.select2-more-results.select2-active {
			background: #f4f4f4 url('select2-spinner.gif') no-repeat 100%;
			padding: 4px 7px;
		}

		/* input in dropdown */
		.select2-search input {
			background: transparent;
			font: 15px/38px "Lato", Liberation Sans, Arial, sans-serif;
			color: #34495e;
		}

		/*----------------------------------------------- Retina displays fix ------------------------------------------------*/
		@media only screen and (-webkit-min-device-pixel-ratio: 1.5), only screen and (min-resolution: 144dpi) {
			.select2-container .select2-choice abbr {
				margin-top: 1px;
			}

			.select2-container .select2-choice .select2-arrow {
				margin-top: 1px;
			}
		}
		/* end select 2 */
		.mpointer{
			cursor: pointer;
		}
		#main-cart .collection{
			border: 0px !important;
			padding: 0px !important;
		}

		#main-cart .collection-item{
			border-bottom: 1px solid #ccc;
			width: 100%;
		}

		.btn-con .btn{
			width:100%;
		}

		.btn-con{
			padding: 10px;
		}

		#main-cart{
			display:none;
			position: fixed;
			top:0;
			left: 0;
			height: 100%;
			width: 100%;
			overflow-y:auto;
		}
		#cart-con-col-1{
			width:20%;
			float: left;
			padding-left: 5px;
		}
		#cart-con-col-2{
			width: 80%;
			float: left;
			padding-left: 5px;
		}
		#cart-con-col-3{
			position: fixed;
			z-index: 99999;
			top:0;
			right: 0;
			width: 100%;
			height: 100%;
			background: #eee;
			padding-right:10%;
			padding-left:10%;
		}
		#queue_list_container{
			position: fixed;
			z-index: 99999;
			top:0;
			right: 0;
			width: 100%;
			height: 100%;
			background: #eee;
			padding-right:10%;
			padding-left:10%;
			overflow-y: auto;

		}
		#reservation_list_container{
			position: fixed;
			z-index: 99999;
			top:0;
			right: 0;
			width: 100%;
			height: 100%;
			background: #eee;
			padding-right:10%;
			padding-left:10%;
			overflow-y: auto;

		}
		#sales_container{
			position: fixed;
			z-index: 99999;
			top:0;
			right: 0;
			width: 100%;
			height: 100%;
			background: #eee;
			padding-right:10%;
			padding-left:10%;
			overflow-y: auto;

		}
		#main-cart h4{
			position: relative;
		}
		#main-cart h4 span{

			position: absolute;
			top: 15px;
			line-height: 30px;
		}
		#main-cart h1{
			margin-left: 20px;
		}
		#main-cart p{
			margin: 0px;
			line-height: 16px;

		}
		#cart-con{

		}
		.con-box{
			width:50%;
			float: left;
			margin-left: 0px;
			height: 250px;
			text-align: center;
			overflow:  hidden !important ;
		}
		.con-box img{
			width:auto;
			height: 180px;

		}

		.cart-image img{
			width: auto !important;
			height: 140px;
			margin:  0 auto !important;
			cursor: pointer;
			overflow-x: hidden !important;

		}

		.item-name{
			font-size:9px;
		}

		.secondary-content{
			top: 40px !important;
		}
		#cart-con-list{

			overflow-y: auto;
			margin-top: -20px;
		}
		#cart-content,#cart-con-list{
			height: 75vh;;
			overflow-y: auto;
			padding-bottom: 20px;
		}
		.nav-wrapper{
			padding-left: 15px;
			padding-right: 15px;
		}
		.swing li {
			background-color: #fff !important;
			opacity: 0;
			transform: rotateX(-90deg);
			transition: all 0.5s cubic-bezier(.36,-0.64,.34,1.76);

		}

		.swing li.show {
			opacity: 1;
			transform: none;
			transition: all 0.5s cubic-bezier(.36,-0.64,.34,1.76);
		}
		.swing {
			perspective: 100px;
		}
		.card-content {
			margin: 0px !important;
			padding: 0px !important;
			padding: 10px !important;
		}
		.btn-fix {
			width: 120px;
		}
		.txtQty{
			height: 20px !important;
			padding: 0px !important;
			margin: 0px !important;

		}
		.categ-title{
			display: block;
			position: relative;
			padding-top: 20px !important;
			padding-left: 20px;
		}
		.categ-url{
			margin-top: 10px;
			position: relative;

		}
		#categ-list li{

			padding-bottom: 0px !important;
			margin: 0px !important;

			height: 30px !important;
			font-size: 16px;
			font-weight: bold;
			cursor: pointer;
		}
		img.square{
			width: 50px;
			height: 50px;
			float: left;
			margin-left: -30px;
			margin-right: 20px;
			border-radius: 10px 10px 10px 10px;
			-moz-border-radius: 10px 10px 10px 10px;
			-webkit-border-radius: 10px 10px 10px 10px;
			border: 0px solid #000000;

		}

		.nmpd-display{
			text-align: center !important;
			font-size: 26px !important;
			font-weight: bold !important;
		}

		.nav-wrapper .brand-logo img {
			height: 64px !important;
		}

		#loader{

			position: fixed;
			width:100%;
			height:100%;
			background-color: rgba(0, 0, 0, 0.6);
			padding-top: 42vh;
			z-index:99999999;
			text-align: center;
		}

		@media (max-width: 600px) {
			.nav-wrapper .brand-logo img {
				height: 56px !important;
			}
		}
		#c-title{
			margin-left: 80px;
			font-size: 30px;
		}

		#grand-total-holder{
			position: fixed;
			bottom: 0px;
			right: 100px;
			z-index: 100;
			padding: 10px;
		}

		::-webkit-scrollbar-track
		{
			-webkit-box-shadow: inset 0 0 2px rgba(0,0,0,0.3);
			background-color: #ccc;
		}

		::-webkit-scrollbar
		{
			width: 2px;
			height:4px;
			background-color: #222;
		}

		::-webkit-scrollbar-thumb
		{
			background-color: #222;
		}
		#total-item-in-cart{
			position: absolute;
			top: 2px;
			left: 45%;
		}
		.fixed-action-btn{
			bottom: 10px !important;
			left: 10px !important;

		}
		.fixed-action-btn ul{
			left: -240px !important;
		}
		.bottom-sheet{
			padding: 0px !important;
			margin: 0px;
		}
		#modalUpdate > .modal-content{
			padding: 10px;
			min-height: 150px;
		}
		.modal {
			max-height: 100% !important;
			top:10px !important;
			height:90%;
			width:80%;
		}
		#payment-container-overlay {
			position: fixed;
			height: 100%;
			width:  100%;
			background-color: rgba(0, 0, 0, 0.6);
			z-index: 99;
		}

		#payment-container{
			position: fixed;
			height: 90vh;
			width: 90%;
			margin-left: 5%;
			margin-top: 5vh;
			overflow-y: auto;
			z-index: 99;
		}

		#payment-container > .row{
			margin-top: 10px;
		}

		#payment-container > #total-holder > div{
			margin-bottom: 3px;
		}

		#cash-container{
			margin-top:5%;
			min-height: 200px;
			padding-top: 30px;
		}

		#member-credit-container{
			margin-top:5%;
			min-height: 200px;
			padding-top: 30px;
		}

		#cash-total-holder{

		}

		#grand-total-holder-details{
			position: fixed;
			bottom: 90px;
			right: 30px;
			z-index: 100;
			height: 60px;
			padding: 10px;
		}

		.btn-width{
			width: 200px;
		}

		#change-holder{
			position: fixed;
			bottom: 0px;
			left: 100px;
			z-index: 100;
			padding: 10px;
		}

		#change-holder .btn{
			width: 300px;
		}

		#mem-con-bg-overlay{

		}

		#mem-con-bg{
			position: fixed;
			top: 0;
			left: 0;
			z-index: 99;
			background: #ccc;
			width: 100%;
			height: 100%;
			background-repeat:no-repeat;
			background-position: center center;
			background-image:url(css/img/reg_background.jpg);
		}

		#member-container-new{
			width:330px;
			height: 500px;
			z-index: 102;
			position: absolute;
			top: 10%;
			overflow-y: auto;
		}

		.full-height {
			position: absolute;
			top: 0 !important;
			height: 100% !important;
			overflow-y: auto;
		}

		#no-item-con{
			text-align: center;
			margin-top:20%;
			font-size: 2.5em;
		}

		.payment-container-close{
			position: fixed;
			top: 0px;
			right: 0px;
			color:#fff;
		}

		[v-cloak] {
			display: none;
		}

		.item-list{
			padding: 0px !important;
		}

		.button-side{

		}
		.btn-cart{
			float: left;
			width: 33%;
		}

		.cart-info{
			padding: 10px;
		}

		.cart-buttons{
			background: #000;
		}

		#bundle-view{
			position: fixed;
			background-color: #ddd;
			width: 100% ;
			height: 100%;
		}

		#bundle-content{
			position: relative;
			top:10vh;
			left:25%;
			width: 50%;
			height: 80vh;
			overflow-y: auto;
			background-color: #fff;
			color:#222;
		}

		#bundle-content p{
			padding-left: 10px;
		}

		#bundle-close{
			position: fixed;
			top:10px;
			right: 10px;
			font-size: 40px;
			color:red;
		}

		#open-bundle-view{
			position: fixed;
			background-color: #ddd;
			width: 100% ;
			height: 100%;
		}

		#open-bundle-content{
			position: relative;
			top:10vh;
			left:25%;
			width: 50%;
			height: 80vh;
			overflow-y: auto;
			background-color: #fff;
			color:#222;
		}

		#open-bundle-content p{
			padding-left: 10px;
		}

		#open-bundle-close{
			position: fixed;
			top:10px;
			right: 10px;
			font-size: 40px;
			color:red;
		}

		#cart-close{
			display:block;
			position: fixed;
			z-index: 99999;
			top: 5px;
			right: 10px;
			cursor: pointer;
			font-size: 25px;

		}
		#queue-close{

			position: fixed !important;
			z-index: 99999;
			top: 5px;
			right: 10px;
			cursor: pointer;
			font-size: 95px !important;

		}


		.btn-block{
			margin:0 auto;
			width:100%;
			display:block;

		}
		.btn-menu{
			margin-top:5px;
			margin-left:5px;
			margin-bottom:5px;
		}
		.btn-amount{
			display:block;
			width:100%;
			margin-bottom: 2px;
		}
		@media only screen and (max-width:  992px) {
			.payment-container-close{
				position: absolute;
				top:0;
				right:0;
				color:#222;
				z-index: 99999999999999;


			}
			.con-box{
				width: 100%;
			}
			#cart-con-col-1{
				width:100%;
				float: left;
			}
			#cart-con-col-2{
				width: 100%;
				float: left;
			}
			#cart-con-col-3{

				position: fixed;
				z-index: 99999;
				top:0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #eee;
				padding-left: 0;
				padding-right: 0;

			}
			#queue_list_container{
				position: fixed;
				z-index: 99999;
				top:0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #eee;
				padding-left: 0;
				padding-right: 0;
			}
			#reservation_list_container{
				position: fixed;
				z-index: 99999;
				top:0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #eee;
				padding-left: 0;
				padding-right: 0;
			}
			#sales_container{
				position: fixed;
				z-index: 99999;
				top:0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #eee;
				padding-left: 0;
				padding-right: 0;
			}
			.avatar{
				padding-left: 0px !important;
				font-size:8px  !important;

			}
			.collection-item{
				padding-top: 0px !important;
				margin-top: 0px !important;
				padding-bottom: 1px !important;
				min-height: 50px !important;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				text-align: center;
			}
			.title{
				padding-left: 0px !important;
				font-size:8px  !important;
			}
			.cart-image img{
				width: auto !important;
				height: 70px !important;
				margin:  0 auto !important;
				cursor: pointer;
				overflow-x: hidden !important;

			}
			.sm-btn-main{
				font-size: 12px;
				padding-right: 15px;
			}
			#total-holder{
				font-size: 8px !important;
				padding:0px  !important;;
			}
			#cart-close{
				display:block !important;
				position: fixed;
				z-index: 99999;
				top: 5px;
				right: 10px;
				cursor: pointer;
				font-size: 25px;
			}
			#queue-close{
				position: fixed !important;
				z-index: 99999;
				top: 5px;
				right: 10px;
				cursor: pointer;
				font-size: 30px !important;
			}
			#cart-close > i {
				font-size: 30px !important;
			}
			#bundle-content{
				position: relative;
				top:0vh !important;
				left:5%  !important;
				width: 90%  !important;
				height: 100vh  !important;
				overflow-y: auto;
				background-color: #fff;
				color:#222;
			}
			#open-bundle-content{
				position: relative;
				top:0 !important;
				left:5%  !important;
				width: 90%  !important;
				height: 100%  !important;
				overflow-y: auto;
				background-color: #fff;
				color:#222;
			}
			.nav-wrapper{
				text-align: right !important;
			}
			.btn-amount{
				padding:0px;

			}
			.nopaddingcard{
				padding:10px;
			}

			#change-holder .btn{
				width: 180px;
				opacity:0.8;
			}
			#grand-total-holder{

				right:2px !important;
				bottom:0px !important;
				z-index:999999999999;
			}
			#change-holder{
				left:2px
			}
			#payment-container{
				position: fixed;
				height: 100% !important;
				width: 100% !important;
				margin-left: 0 !important;
				margin-top: 0 !important;

			}
			#grand-total-holder > button{
				width:180px !important;
				opacity:0.8;
				margin-top: 2px;
				z-index: 99999;



			}
		} /* less 960 */

		@media only screen and (max-width: 600px) {
			#cart-close > i {
				font-size: 30px !important;
			}

			#queue-close  {
				font-size: 30px !important;
			}

			#cart-con-col-1{
				width: 100%;
			}

			#cart-con-col-2{
				width: 100%;
			}

			#cart-con-col-3{
				position: fixed;
				z-index: 99999;
				top:0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #eee;
			}

			#queue_list_container{
				position: fixed;
				z-index: 99999;
				top:0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #eee;
				padding-left: 0;
				padding-right: 0;
			}

			#reservation_list_container{
				position: fixed;
				z-index: 99999;
				top:0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #eee;
				padding-left: 0;
				padding-right: 0;
			}

			#sales_container{
				position: fixed;
				z-index: 99999;
				top:0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #eee;
				padding-left: 0;
				padding-right: 0;
			}

			body{
				font-size:10px !important;
			}

			.main-lbl {
				font-size: 11px !important;
			}

			.avatar{
				padding-left: 0px !important;
				font-size:8px  !important;
			}

			.collection-item{
				padding-top: 0px !important;
				margin-top: 0px !important;
				padding-bottom: 1px !important;
				min-height: 50px !important;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				text-align: center;
			}

			.title{
				padding-left: 0px !important;
				font-size:8px  !important;
			}

			.cart-image img{
				width: auto !important;
				height: 70px !important;
				margin:  0 auto !important;
				cursor: pointer;
				overflow-x: hidden !important;
			}

			#cart-close{
				display:block !important;
				position: fixed;
				z-index: 99999;
				top: 5px;
				right: 10px;
				cursor: pointer;
				font-size: 25px;
			}

			#queue-close{
				position: fixed !important;
				z-index: 99999;
				top: 5px;
				right: 10px;
				cursor: pointer;
				font-size: 25px;
			}

			#bundle-content{
				position: relative;
				top:0 !important;
				left:5%  !important;
				width: 90%  !important;
				height: 100%  !important;
				overflow-y: auto;
				background-color: #fff;
				color:#222;
			}

			#open-bundle-content{
				position: relative;
				top:0 !important;
				left:5%  !important;
				width: 90%  !important;
				height: 100%  !important;
				overflow-y: auto;
				background-color: #fff;
				color:#222;
			}

			.nav-wrapper{
				text-align: right !important;
			}

			#grand-total-holder > button{
				width:100% !important;
				opacity:0.8;
				margin-top: 2px;
				z-index: 99999;
			}

			#grand-total-holder{
				position: fixed;
				bottom: 0px;

				left: 0;
				z-index: 999999;
				padding: 2px;
				width: 100% !important;
			}

			#payment-container{
				position: fixed;
				height: 100% !important;
				width: 100% !important;
				margin-left: 0 !important;
				margin-top: 0 !important;
			}

			#change-holder{
				position: fixed;
				bottom: auto;
				left: 0px !important;
				top:0px;
				right:auto;
				width: 100% !important;
				height: 30px !important;
				z-index: 999999;
				padding: 2px;
			}

			#change-holder .btn{
				width: 100%;
				opacity:0.8;
			}

			.input-qty{
				display:block !important;
			}

			#main-cart .collection-item{
				border-bottom: 1px solid #ccc;
				width: 98%;
			}

			.title{
				display:block !important;
				white-space: normal;
			}

			.btn-update{
				font-size:15px;
			}

			.modal {
				max-height: 100% !important;
				top: 0 !important;
				height:100%;
				width:100%;
			}

			.payment-container-close{
				position: absolute;
				top:0;
				right:0;
				color:#222;
				z-index: 9999999;
			}

			#cart-content,#cart-con-list{
				padding-bottom: 20px;
			}

			.btn-amount{
				padding:0px;
			}

			.nopaddingcard{
				padding:6px;
			}
			.btn-menu{
				padding-left:10px;
				padding-right:10px;
				width:100px;
				display:inline-block;
			}
		} /* less 600 */


		.cpointer{
			cursor: pointer;
		}
		.mright10{
			margin-rigth:50px;
			display: inline-block;
		}

		.noselect {
			-webkit-touch-callout: none; /* iOS Safari */
			-webkit-user-select: none; /* Safari */
			-khtml-user-select: none; /* Konqueror HTML */
			-moz-user-select: none; /* Firefox */
			-ms-user-select: none; /* Internet Explorer/Edge */
			user-select: none; /* Non-prefixed version, currently
                              supported by Chrome and Opera */
		}

		.animated {
			animation-duration: 0.2s;
		}

		.border-top{
			border-top:1px solid #ccc;
		}


	</style>
</head>
<body>
<input type="hidden" id='txtLayout' value='{"1":{"order":"1","value":"","label":"","style":{"width":"0 auto","height":"0 auto","font-size":"12px","text-align":"center","float":"none","position":"static"},"key":"company"},"2":{"key":"address","order":"2","label":"","value":"","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"}},"3":{"order":"3","value":"","label":"Date","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"},"key":"date"},"4":{"order":"4","value":"","label":"Tin","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"},"key":"tin"},"5":{"order":"5","value":"","label":"Contact","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"},"key":"contact"},"6":{"order":"6","value":"","label":"Ctrl#","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"},"key":"ctr_no"},"7":{"order":"7","value":"","label":"Items","type":"table","props":"item_code,qty,price,total","div":"item_code|price,qty,total","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"left","float":"none","position":"static"},"key":"items"},"8":{"order":"8","value":"","label":"Remarks","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"},"key":"remarks"},"9":{"order":"9","value":"","label":"Sub Total","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"},"key":"sub_total"},"10":{"order":"10","value":"","label":"Vat","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"},"key":"vat"},"11":{"order":"11","value":"","label":"Total","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"},"key":"total"},"12":{"key":"test","order":"12","label":"test","value":"","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"}},"13":{"key":"member","order":"13","label":"Client:","value":"","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"}},"14":{"key":"member_address","order":"14","label":"Address:","value":"","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"}},"15":{"key":"cashier","order":"15","label":"Cashier:","value":"","style":{"width":"0 auto","height":"0 auto","font-size":"10px","text-align":"center","float":"none","position":"static"}}}'>
<div id="app" v-cloak>


	<div id='main-cart'>

		<div class="navbar-fixed ">
			<nav  v-bind:class="(reservation_id == '0') ? 'grey darken-4' : 'blue'" >
				<div class="nav-wrapper">

					<a href="admin/index.php" v-bind:class="isOnline ? 'white-text' : 'red-text' " id='brand-name' class="brand-logo hide-on-med-and-down">
						{{ company_name }}
					</a>
					<span id='c-title' class='hide-on-med-and-down'></span>
					<span id='total-item-in-cart' class='hide-on-med-and-down' v-show='cart.length'>Total: {{ number_format(cartTotal,2) }}</span>

					<a href="admin/index.php" class='sm-btn-main hide-on-large-only left'>Dashboard</a>

					<a href="#" @click.prevent="showCart"  v-show="terminal_id != '0'"  class='sm-btn-main hide-on-large-only'>Cart({{cart.length}})</a>
					<a href="#" @click.prevent="sales_view = true" v-show="sales.length || sales_not_sync.length"  class='sm-btn-main hide-on-large-only'>Sales</a>
					<a href="#" @click.prevent="reservation_list_view = true" v-show="reservations.length" class='sm-btn-main hide-on-large-only'>Reserved</a>
					<a href="#" @click.prevent="queue_list_view = true" v-show="queue_list.length" class='sm-btn-main hide-on-large-only'>Pending</a>
					<a href="admin/terminal.php" v-show="terminal_id == '0'" class='sm-btn-main red-text sm-btn-main hide-on-large-only'>Set Terminal First</a>
					<ul id="nav-mobile" class="right hide-on-med-and-down">

						<li v-show="cart.length"><a href="#" @click.prevent="showCart" >Cart({{cart.length}})</a></li>
						<li  v-show="terminal_id == '0'" ><a href="admin/terminal.php" v-show="!terminal_id || terminal_id == '0'" class='red darken-4' >Set Terminal First</a></li>
						<li v-show="sales.length || sales_not_sync.length"><a href="#" v-show="terminal_id != '0'" @click.prevent="sales_view = true">Sales</a></li>
						<li v-show="reservations.length"><a href="#" v-show="terminal_id != '0'" @click.prevent="reservation_list_view = true">Reserved</a></li>
						<li v-show="queue_list.length"><a href="#" v-show="terminal_id != '0'" @click.prevent="queue_list_view = true">Pending</a></li>

					</ul>

				</div>
			</nav>
		</div>
		<div id='cart-con-col-1' >

			<div id="cart-action-con" class='hide-on-med-and-down'>
				<ul class="collection" id='categ-list'>
					<li @click="goParent" class='collection-item avatar red-text'>
						<img src="css/img/category.jpg" alt="" class="square categ-url ">
						<strong class="title categ-title"  v-show="categ_cur == categories">CATEGORY</strong>
						<strong class="title categ-title"   v-show="categ_cur != categories">BACK</strong>
					</li>
					<li class="collection-item avatar" v-bind:class="(cur_categ == categ.id) ? 'grey lighten-4' : ''"  v-for="categ in sortedCategory" @click="getItems(categ.id)">
						<img v-bind:src="categ.url" alt="" class="square categ-url hide-on-med-and-down">
						<span class="title categ-title">{{categ.name}}</span>
					</li>
				</ul>
			</div>

			<div class='hide-on-large-only' style='overflow-x: auto;overflow-y: hidden;white-space: nowrap;-webkit-overflow-scrolling: touch;' >

				<button class="btn grey lighten-3 btn-menu black-text btn-small truncate"  @click="goParent"  v-show="categ_cur != categories">BACK</button>
				<button class="btn grey lighten-3 btn-menu  black-text btn-small truncate"  @click="getItems(-1)" v-show="categ_cur == categories">Show All</button>
				<button class='btn black btn-menu  btn-small truncate' v-for="categ in sortedCategory"  @click="getItems(categ.id)">{{categ.name}}</button>


			</div>

		</div>

		<div id='cart-con-col-2' class='z-depth-3'>
			<div id="cart-con">

				<div class="row">

					<div class="input-field col s12">
						<i class="material-icons prefix" v-show="search_query == ''">search</i>
						<i class="material-icons prefix mpointer" @click.prevent="clearSearch"  v-show="search_query != ''">close</i>

						<input type="text" id="autocomplete-input" class="autocomplete" v-model='search_query' @keyup="updateSearch">
						<label id='label-autocomplete' for="autocomplete-input">Search Item</label>

					</div>

				</div>


				<div id='cart-con-list' >
					<div class="row" v-show="myItems.length">
						<paginate
							ref="paginator"
							name="paged_items"
							:list="myItems"
							:per="8"
							v-if="myItems.length"
							></paginate>
						<div class="col s6 l3 m4" v-for="item in paginated('paged_items')">
							<div class="card">


								<div class="card-image">
									<div class='cart-image noselect'>
										<img v-bind:src="item.url" class='responsive-img '  @click="addCart(item)">
									</div>
								</div>
								<div class="card-content">

									<span v-bind:title="item.description" class='item-name truncate'>{{item.description}}</span>
									<div>&#8369; {{number_format(item.price,2)}}</div>
									<div class='center-align' style='height:40px;'>
										<button  @click="addCart(item)" v-show="item.qty == 0" class='btn blue btn-block truncate'>
											<span class='hide-on-large-only'>Add</span>
											<span class='hide-on-med-and-down'>	Add To Cart</span>

										</button>
										<div class="row">
											<div class="col s4"  style='margin:0px !important;padding:0px !important;'>
												<button v-show="item.qty" @click="deductQty(item)" class="btn-floating  black"><i class="material-icons">remove</i></button>
											</div>
											<div class="col s4 center-align"  style='margin:0px !important;padding:0px !important;'>
												<span  style='font-size:29px;font-weight: bold;' v-show="item.qty">
													{{item.qty}}
												</span>
											</div>
											<div class="col s4"  style='margin:0px !important;padding:0px !important;'><button v-show="item.qty" @click="addQty(item)"  class="btn-floating  black"><i class="material-icons">add</i></button>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<div style='clear:both'></div>
						<div class='center-align' v-show="moreThanOnePage">
							<paginate-links for="paged_items"
							                :classes="{
	                                        'ul': 'pagination','li':'cpointer'}
	                                        "
							                :limit="8"
							                v-if="myItems.length"
								>
							</paginate-links>
						</div>
					</div>
					<div v-show="!myItems.length && loader == false" id='no-item-con'><div class='alert alert-warning'>No Record Found</div></div>
				</div>

			</div>
		</div>

		<div id='cart-con-col-3' v-show="cart_view">
			<a id='cart-close' class='grey-text text-darken-3' @click="showCart"><i class="material-icons large">close</i></a>
			<div id="cart-total-con">
				<h5 class='center-align'>
					<i class="material-icons" >&#xE8CC;</i>
					<span> Your Cart
						<span style='font-size:15px;' v-show="cart.length" > {{ cart.length}} item<span v-show="cart.length > 1">s</span> </span>
                    </span>
				</h5>

				<div class="card-panel black white-text">
					<div class="row" style='margin-bottom: 0;'>
						<div class="col s6">
							Total: {{ number_format(cartTotal,2) }}
						</div>
						<div class="col s6 right-align">
							<a class="white-text" href="#" v-show="terminal_id != '0' && cart.length"  style='margin-right:15px;' @click.prevent="reserveItems">Reserve</a>
							<a class="white-text" href="#" v-show="terminal_id != '0' && cart.length"   @click.prevent="checkout">Check Out</a>
						</div>
					</div>
				</div>
				<p>
					<span style='font-weight: bold;'>Invoice:</span> <span class='red-text'>{{ctrl_number.invoice }}</span>
					<span style='font-weight: bold;'> DR:</span>  <span class='red-text'>{{ctrl_number.dr }}</span>
					<span style='font-weight: bold;'> PR:</span>  <span class='red-text'>{{ctrl_number.pr }}</span>
				</p>
				<div id='cart-content' v-show="cart.length">

					<ul class="collection swing"  >
						<li class="collection-item item-list" v-bind:class="c.show ? 'show' : '' " v-for="c in cart">
							<div style='padding:10px;!important;'>
								<div class="row">
									<div class="col m6 s12">
										<span class="title" >{{c.description}} <br> &#8369; {{ number_format(c.price,2) }}</span>
										<p v-if="c.discount || c.agent_id">
										<p><span v-show="c.discount">( &#8369; {{ number_format(getDiscount(c),2)}})</span></p>
										<p v-if="!c.agent_list.length">
											<span v-show="c.agent_id">Agent:  {{ c.agent_name }}</span></p>
										</p>
										<p v-if="c.agent_list.length">
											<span style='display:block;' v-for="al in c.agent_list">{{al.agent_name}} - QTY: <span class='red-text'>{{al.qty}}</span></span>
										</p>
									</div>
									<div class="col m6 s12">
										<div class="row center-align" v-show="c.edit_qty == 0">
											<div class="col s4">
												<button v-show="c.qty" @click="deductQty(c)" class="btn-floating  waves-effect waves-light black"><i class="material-icons">remove</i></button>
											</div>
											<div class="col s4">
												<div  style='font-size:29px;font-weight: bold;margin-top:10px;' v-show="c.qty">
													<span @click="updateQtyInput(c)">{{c.qty}}</span>
												</div>
											</div>
											<div class="col s4">
												<button v-show="c.qty" @click="addQty(c)" class="btn-floating waves-effect waves-light black"><i class="material-icons">add</i></button>
											</div>
										</div>
										<div  class="row center-align" v-show="c.edit_qty == 1">
											<div class="col s6">
												<input type="number" v-bind:id="'txt' + c.id" @keyup="checkQty(c)" v-model='c.qty' style='width: 100%;padding: 5px;border: 1px solid #f2f2f2;border-radius: 2px;height: 3rem;' class='browser-default'>
											</div>
											<div class="col s6">
												<button class='btn blue' @click="c.edit_qty = 0">Done</button>
											</div>
										</div>
									</div>
									<div class="col s12">

										<a href="#!" class="text-lighten-3 btn-update  " @click.prevent="editItem(c)"> <br> <i class="material-icons">create</i></a>

									</div>
								</div>
							</div>


							<div class='cart-button'>


								<div class="btn-cart center-align">
									<a href="#!" v-show="c.is_bundle == '1'"  @click.prevent="showBundleItem(c)" class="text-lighten-3">
										<i class="material-icons">list</i>
									</a>
									<a   href="#!" class="text-lighten-3" v-show="c.is_bundle == '0'">&nbsp;</a>
								</div>


							</div>
							<div style='clear:both;'></div>

						</li>
					</ul>
					<div class='row'>
						<div class="col s12">
							<a href="#" v-show="terminal_id != '0'" @click.prevent="emptyCart">Empty Cart</a>
						</div>
					</div>

					<div v-show="queues.length">
						<div class="row" >

							<div class="col m3 s6">
								<select class='browser-default' v-model='form_queue.queue_id' name="queue_id" id="queue_id">
									<option value="">Select Area</option>
									<option v-for="q in queues" v-bind:value="q.id">{{q.name}}</option>
								</select>
								<small class='grey-text'>Area</small>
							</div>

							<div class="col m3 s6">
								<input type="number" value="" v-model='form_queue.hrs' style='width: 100%;padding: 5px;border: 1px solid #f2f2f2;border-radius: 2px;height: 3rem;' class='browser-default'>
								<div class="row">
									<div class="col s4">
										<small class='grey-text'>Hour(s)</small>
									</div>
									<div class="col s4 right-align">
										<a href='#' v-show="form_queue.hrs" class='cpointer' @click.prevent="form_queue.hrs--"><i class="material-icons">remove</i></a>
									</div>
									<div class="col s4 right-align">
										<a  href='#' class='cpointer' @click.prevent="form_queue.hrs++"><i class="material-icons">add</i></a>
									</div>
								</div>
							</div>

							<div class="col m3 s6">
								<input type="time" value="" v-model='form_queue.start'  style='width: 100%;padding: 5px;border: 1px solid #f2f2f2;border-radius: 2px;height: 3rem;' class='browser-default'>
								<div class="row">
									<div class="col s6">
										<small class='grey-text'>Starts at</small>
									</div>
									<div class="col s6 right-align">
										<a href="" @click.prevent="getCurrentTime">Current Time</a>
									</div>
								</div>
							</div>
							<div class="col m3 s6">
								<select class='browser-default' v-model='form_queue.agent_id' id='form_queue_agent_id'>
									<option value="">None</option>
									<option v-for="ag in allAgents" v-bind:value="ag.agent_id">{{ag.agent_name}}</option>
								</select>
								<small class='grey-text'>Choose Agent</small>
							</div>


						</div>

						<div class="row">
							<div class="col m12 s12">
								<div v-show="queues.length != 1">
									<div v-show="!form_queue_arr.length">
										<input type="checkbox" class="browser-default" v-show="!moreThanOneArea" id='moreThanOneArea' v-model="moreThanOneArea"> <label for="moreThanOneArea">More than one area</label>
									</div>
									<a href="#" class='btn btn-small' v-show="moreThanOneArea" @click="addArea()">Add Area</a>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col s12">
								<a href="#" @click.prevent="resetQueueSelection()">Reset Area Selection</a>
							</div>
						</div>

					</div>
					<div v-show="form_queue_arr.length">

						<table   class='table bordered'>
							<thead>
							<tr><th>Area</th><th>Hr(s)</th><th>Starts At</th><th>Agent</th></tr>
							</thead>
							<tbody>
							<tr v-for="fq in form_queue_arr">
								<td>{{ getQueueName(fq.queue_id)}}</td>
								<td>{{ fq.hrs}}</td>
								<td>{{ fq.start}}</td>
								<td>{{ (fq.agent_name) ? fq.agent_name : 'None'}}</td>
							</tr>
							</tbody>
						</table>
					</div>
					<div id="form_queue_container"></div>
					<div v-show="!queues.length" class='card-panel'>
						No available area for service. See occupied <a href='#' @click.prevent="queue_list_view = true">list</a>.
					</div>
				</div>
				<div  class='card-panel' v-show="!cart.length">No record found</div>
				<br>
			</div>
		</div>
	</div>


	<!-- Modal Structure -->
	<div id="modalUpdate" class="modal">
		<div class="modal-content">

			<a style='position:absolute;top:0px;right:0px;' @click="closeEditModal" href='#'><i class="material-icons medium">close</i></a>
			<div class="row">

				<div class="col m11 s10">
					<p><strong>Item: </strong>{{ cur_item_cart.description }} <strong>Price: </strong> &#8369; {{ number_format(cur_item_cart.price,2) }} <strong>Total: </strong>&#8369;{{ number_format(getItemTotal(cur_item_cart),2) }} </p>
				</div>

			</div>

			<div class="row">

				<div class="input-field col s12 m6">
					<br><br>
					<input placeholder="Discount" id="edit_discount" v-model='cur_item_cart.discount' @keyup="validateDiscount(cur_item_cart)" type="text" >
					<label for="edit_discount">Item Discount</label>
					<a href='#'  class='cpointer' @click.prevent="cur_item_cart.discount = '0' ">Reset Discount</a>
				</div>
				<div class="col s12 m6">
					<div class="row">
						<div class="col s12"><span>Discount Shortcut</span></div>
					</div>
					<div class="row">
						<div class="col s3">
							<button class='btn btn-small blue btn-amount' @click="addDiscount(1, true,false)">1%</button>
						</div>
						<div class="col s3">
							<button class='btn btn-small blue btn-amount'  @click="addDiscount(5, true,false)">5%</button>
						</div>
						<div class="col s3">
							<button class='btn btn-small blue btn-amount'  @click="addDiscount(10, true,false)">10%</button>

						</div>
						<div class="col s3">
							<button class='btn btn-small blue btn-amount'  @click="addDiscount(50, true,false)">50%</button>
						</div>
					</div>
					<div class="row">
						<div class="col s3">
							<button class='btn btn-small btn-amount' @click="addDiscount(1,false,false)">1</button>
						</div>
						<div class="col s3">
							<button class='btn btn-small btn-amount' @click="addDiscount(5,false,false)">5</button>
						</div>
						<div class="col s3">
							<button class='btn btn-small btn-amount' @click="addDiscount(10,false,false)">10</button>
						</div>
						<div class="col s3">
							<button class='btn btn-small btn-amount' @click="addDiscount(50,false,false)">50</button>
						</div>
						<div class="col s3">
							<button class='btn btn-small btn-amount' @click="addDiscount(100,false,false)">100</button>
						</div>
						<div class="col s3">
							<button class='btn btn-small btn-amount' @click="addDiscount(500,false,false)">500</button>
						</div>
					</div>

				</div>

				<div class="col m12 s12" v-show="users.length">
					<div class="row">
						<div class="col s12"><span>Select Agent</span></div>
						<div class='col s4 m3' v-for="user in users">
							<div class="card-panel truncate nopaddingcard" @click="changeUser(user)" v-bind:class="user.active == 1 ? 'grey darken-4 white-text' : ''">
								{{user.name}}
							</div>
						</div>
					</div>
				</div>
				<div v-show="!users.length">
					<div class="input-field col  s12">
						<span>Agent Name</span>
						<input placeholder="Enter Agent" id="agent_id" v-model='cur_item_cart.agent_id'  style='width:100%;' type="text" >
					</div>
				</div>

				<div class=" input-field col  s12">
					<span>Agent Qty</span>
					<input placeholder="Enter Qty" id="agent_qty" v-model='cur_item_cart.agent_qty'  @keyup="agentQtyChange" class="form-control" type="number" >
					<div class="row">
						<div class="col s6">
							<a href='#'  class='cpointer' @click.prevent="resetAgent()">Reset Agent</a>
						</div>
						<div class="col s6">
							<div class="row">
								<div class="col s6">
									<a href="#" v-show="cur_item_cart.agent_qty > 1" @click.prevent= "cur_item_cart.agent_qty = cur_item_cart.agent_qty - 1" ><i class="material-icons">remove</i></a>
									<span v-show="cur_item_cart.agent_qty <= 1" class='grey-text'><i class="material-icons">remove</i></span>

								</div>
								<div class="col s6">
									<a href="#" v-show="cur_item_cart.agent_qty < cur_item_cart.qty"  @click.prevent= "cur_item_cart.agent_qty = cur_item_cart.agent_qty + 1" ><i class="material-icons">add</i></a></a>
									<span class='grey-text' v-show="cur_item_cart.agent_qty >= cur_item_cart.qty"  ><i class="material-icons">add</i></span></a>
								</div>
							</div>

						</div>
					</div>

				</div>

				<div class="input-field col s12">
					<div class="row">
						<div class="col s6">
							<button  class='btn red' v-show="cur_item_cart.agent_qty != cur_item_cart.qty || (cur_item_cart.agent_list && cur_item_cart.agent_list.length)" @click="addAgent()" >Add Agent</button>
						</div>
						<div class="col s6 right-align">
							<button class='btn black' @click="closeEditModal">Done</button>
						</div>
					</div>

				</div>


			</div>
			<div v-show="cur_item_cart.agent_list && cur_item_cart.agent_list.length">
				<table  class='table bordered'>
					<tr><th>Agent</th><th>Qty</th></tr>
					<tr v-for="al in cur_item_cart.agent_list">
						<td>{{al.agent_name}}</td>
						<td>{{al.qty}}</td>
					</tr>
				</table>
			</div>



		</div>
	</div>

	<!-- Payment Type -->
	<div id="payment-container-overlay"  v-show="payment_con">
		<div id='payment-container' class='grey lighten-4'>
			<a href="#"  class='payment-container-close' @click.prevent="paymentContainerToggle()"><i class="material-icons medium" >close</i></a>
			<div class="row">

				<div class="col m10 s10 l3"><input type="text" id='member_id' v-model='member_id' style='width:100%;'></div>
				<div class="col m10 s10 l3">
					<a href="#" @click.prevent="member_con = true">Not registered?</a>
				</div>

				<div class="col l3 m10 s10">

					<input type="text" id='referred_by'  v-model='referred_by' style='width:100%;display:none;'>

				</div>
				<div class="col l3 m10 s10">
					<select name="doc_type" id="doc_type"  v-model='doc_type'>
						<option value="1">Invoice</option>
						<option value="2">DR</option>
						<option value="3">PR</option>
					</select>

				</div>

				<div class="col m12" v-if="subs.id">
					<p><span v-for=""><strong class=''>{{subs.description}}</strong> - <strong class='red-text'>Valid until:</strong> {{subs.end_date}} <strong class='red-text'>Extension:</strong> {{subs.extends_to}}</span> </p>
				</div>
			</div>


			<?php include 'includes/payment-form.php'; ?>
			<div class='white-text' id='grand-total-holder'>
				<button class='btn blue darken-2 btn-width truncate' data-position="top" data-delay="50" data-tooltip="Grand Total">&#8369; {{grand_total}}</button>
				<button v-bind:class="[validPayment ? 'green' : 'blue darken-2']" data-position="top" data-delay="50" data-tooltip="Payment" class='btn waves-effect waves-light btn-width' @click="finalizeTransaction">
					&#8369; {{number_format(cartTotal,2)}} <i class="material-icons right" v-show="validPayment">check</i> <i class="material-icons right" v-show="!validPayment">close</i>
				</button>
			</div>
			<div class='white-text' id='change-holder' v-show="hasChange">
				<button  data-position="top" data-delay="50" data-tooltip="Change" class='btn red btn-width truncate '>&#8369; {{ number_format(hasChange,2)}}</button>
			</div>
		</div>
	</div>
	<div id='bundle-view' v-show="bundle_view">
		<i id='bundle-close' class="material-icons mpointer" @click.prevent="bundle_view = false">close</i>

		<div id='bundle-content'>

			<p class='left-align'>
				<strong>Bundle Name: {{ bundle.name }}</strong> <br>Qty: {{ bundle.qty }}
			</p>

			<table class='striped'>
				<thead>
				<tr><th>Item</th><th>Bundle Qty</th><th>Total</th><th>Used</th></tr>
				</thead>
				<tbody>
				<tr v-for="bund in bundle_list">
					<td>{{bund.description}}<br><small class='red-text'>{{bund.description}}</small></td>
					<td>{{bund.child_qty }}</td>
					<td>
						{{bund.total_qty }}
					</td>

					<td><input class='center-align' onclick="this.select()" type="text" v-model='bund.used_qty'></td>
				</tr>
				</tbody>
			</table>
			<hr>
			<div class='center-align'>
				<button class='btn' @click="saveBundle">SAVE</button>
			</div>
		</div>

	</div>
	<div id='open-bundle-view' v-show="open_bundle_view">


		<div id='open-bundle-content'>

			<p class='left-align'>
				<strong>Bundle Name: {{ open_bundle.name }}</strong> <br>Qty: {{ open_bundle.qty }}
			</p>

			<table class='striped'>
				<thead>
				<tr><th>Use</th><th>Item</th><th>Qty</th></tr>
				</thead>
				<tbody>
				<tr v-for="bund in open_bundle_list">
					<td><input style='opacity: 1;pointer-events: auto;' type="checkbox" v-model="bund.is_chosen"></td>
					<td>{{bund.description}}<br><small class='red-text'>{{bund.description}}</small></td>
					<td>
						<input class='center-align' onclick="this.select()" v-model="bund.qty"  type="text">
					</td>
				</tr>
				</tbody>
			</table>
			<hr>
			<div class='center-align'>
				<button class='btn' @click="saveOpenBundle">SAVE</button>
			</div>
		</div>

	</div>
	<div  v-show="member_con"  id='mem-con-bg-overlay'></div>
	<div  v-show="member_con"  id='mem-con-bg'></div>
	<div  v-show="member_con">
		<div class="row">
			<div class="col s12 m8"></div>
			<div class="col s12 m4">
				<div id="member-container-new" class='grey lighten-3' v-bind:class="(member_type == 2) ? 'full-height' : ''">
					<div class="">
						<i class="material-icons right mpointer" @click.prevent="member_con = false">close</i> <br>
						<div class='center-align'>

							<input class="with-gap" value="1" v-model="member_type" type="radio" id="member_walkin" checked />
							<label for="member_walkin"  class="with-gap">Walk In</label>

							<input class="with-gap" value="2" v-model="member_type" type="radio" id="member_reg"  />
							<label for="member_reg">Member</label>
						</div>
						<h5 class='center-align'>Client Details</h5>

						<div class="row">
							<div class="input-field col s12 m12">
								<input value="" id="member_name" type="text" v-model="form_member.name">
								<label  for="member_name">Client Name</label>
							</div>
							<div class="input-field col s12 m12">
								<input value="" id="member_bday" type="text" v-model="form_member.bday" >
								<label  for="member_bday">Birth Date</label>
							</div>
							<div class="input-field col s12 m12">
								<select name="member_gender" id="member_gender"  v-model="form_member.gender" >
									<option value="">Choose Gender</option>
									<option value="Male">Male</option>
									<option value="Female">Female</option>
								</select>
								<label  for="member_gender">Gender</label>
							</div>
							<div class="input-field col s12 m12">
								<input value="" id="member_contact" type="text" v-model="form_member.contact" >
								<label  for="member_contact">Contact Number</label>
							</div>
							<div v-show='member_type == 2'>
								<div class="input-field col s12 m12">
									<input value="" id="member_email" type="text" v-model="form_member.email">
									<label  for="member_email">Email</label>
								</div>
								<div class="input-field col s12 m12">
									<input value="" id="member_address" type="text" v-model="form_member.address">
									<label  for="member_address">Address</label>
								</div>
								<h5 class='center-align'>Login Details</h5>
								<div class="input-field col s12 m12">
									<input value="" id="member_username" type="text" v-model="form_member.username">
									<label  for="member_username">Username</label>
								</div>
								<div class="input-field col s12 m12">
									<input value="" id="member_password" type="text" v-model="form_member.password">
									<label  for="member_password">Password</label>
								</div>
							</div>



							<div class="input-field col s12 m12 center-align">
								<button @click="createClientAccount" class='btn waves-effect waves-light red'>Submit</button>
							</div>

						</div>



					</div>
				</div>
			</div>
		</div>


	</div>

	<div id='queue_list_container' class='scale-transition scale-out' v-bind:class="{'scale-in' : queue_list_view}">

		<i id='queue-close' class="material-icons cpointer grey-text text-darken-3" @click="queue_list_view= false">close</i>

		<h5 class='center-align'>
			<i class="material-icons" >access_alarms</i>
			<span> Occupied List </span>
		</h5>

		<div class="card-panel black white-text">
			<div class="row" style='margin-bottom: 0;'>
				<div class="col s12">
					Current Time: <strong>{{current_time}}</strong>
				</div>

			</div>
		</div>


		<table class='table bordered' v-show="queue_list.length">
			<tr><th>Name</th><th>Time In</th><th>Time Out</th><th>Will be completed</th><th></th></tr>
			<tr v-for="pqueue in queue_list">
				<td>{{pqueue.name}}</td>
				<td>{{pqueue.time_in}}</td>
				<td>{{ (pqueue.checkout != '0') ? pqueue.time_out : 'Pending'}}</td>
				<td>{{ remainingTime(pqueue)}}</td>
				<td><button class='btn blue btn-small' @click="queueComplete(pqueue)"><i class='material-icons'>check</i></button></td>
			</tr>
		</table>
		<div v-show="!queue_list.length">
			<div class='alert alert-warning'>No Record Found</div>
		</div>
	</div>
	<div id='reservation_list_container' class='scale-transition scale-out' v-bind:class="{'scale-in' : reservation_list_view}">

		<i id='queue-close' class="material-icons cpointer grey-text text-darken-3" @click="reservation_list_view= false">close</i>

		<h5 class='center-align'>
			<i class="material-icons" >access_alarms</i>
			<span> Reservation List </span>
		</h5>

		<div class="card-panel black white-text">
			<div class="row" style='margin-bottom: 0;'>
				<div class="col s12">
					Check out by tapping the total amount
				</div>

			</div>
		</div>


		<table class='table bordered' v-show="reservations.length">
			<tr><th></th><th>Cart Items</th><th>Total</th></tr>
			<tr v-for="(r,index) in reservations">
				<td>

					{{ r.queue_names }} <br> {{ r.agent_names }}
				</td>
				<td>
					<table>
						<tr><th>Item</th><th>Qty</th><th>Price</th><th>Discount</th><th>Total</th></tr>
						<tr v-for="ci in JSON.parse(r.cart)">
							<td>{{ci.description}}</td>
							<td>{{ci.qty}}</td>
							<td>{{number_format(ci.price,2)}}</td>
							<td>{{number_format(ci.discount,2)}}</td>
							<td>{{ number_format((ci.qty * ci.price) - ci.discount,2)}}</td>
						</tr>
					</table>

				</td>
				<td>
					<strong class='red-text cpointer' @click="reservePayment(r,index)">{{ number_format(reserveTotal(r.cart),2)}}</strong>
					<br><br><span class='blue-text cpointer' @click="cancelReservation(r,index)">cancel</span>
				</td>
			</tr>
		</table>
		<div v-show="!reservations.length">
			<div class='alert alert-warning'>No Record Found</div>
		</div>
	</div>

	<div id='sales_container' class='scale-transition scale-out' v-bind:class="{'scale-in' : sales_view}">

		<i id='queue-close' class="material-icons cpointer grey-text text-darken-3" @click="sales_view= false">close</i>

		<h5 class='center-align'>

			<span> Monitoring</span>
		</h5>

		<div class="card-panel black white-text">
			<div class="row" style='margin-bottom: 0;'>
				<div class="col s12">
					Today's sales
				</div>
			</div>
		</div>


		<table class='table highlight' v-show="sales.length">
			<tr><th>Ctr Num</th><th>Item</th><th class='right-align'>Qty</th><th class='right-align'>Price</th><th class='right-align'>Discount</th><th class='right-align'>Total</th></tr>
			<tr v-for="r in sales">
				<td  v-bind:class="(r.payment_id == '0') ? '' : 'border-top'">{{r.ctr}}</td>
				<td v-bind:class="(r.payment_id == '0') ? '' : 'border-top'">{{ r.description}}</td>
				<td v-bind:class="(r.payment_id == '0') ? '' : 'border-top'" class='right-align'>{{ r.qtys }}</td>
				<td v-bind:class="(r.payment_id == '0') ? '' : 'border-top'" class='right-align'>{{ r.price }}</td>
				<td v-bind:class="(r.payment_id == '0') ? '' : 'border-top'" class='right-align'>{{ r.discount }}</td>
				<td v-bind:class="(r.payment_id == '0') ? '' : 'border-top'" class='right-align'>{{ r.total }}</td>
			</tr>
		</table>

		<table class='table bordered' v-show="!isOnline">

			<tr><th>Ctr Num</th><th>Items</th></tr>
			<tr v-for="s in sales_not_sync">
				<td>{{ s.ctrl_number }}</td>
				<td>
					<table class='table bordered'>
						<tr>
							<th>Item</th>
							<th>Description</th>
							<th>Qty</th>
							<th>Price</th>
							<th>Discount</th>
							<th>Total</th>
						</tr>
						<tr v-for="c in JSON.parse(s.cart)">
							<td style='width:15%;'>{{c.item_code}}</td>
							<td style='width:25%;'>{{c.description}}</td>
							<td style='width:10%;'>{{c.qty}}</td>
							<td style='width:10%;'>{{ number_format(c.price,2)}}</td>
							<td style='width:10%;'>{{number_format(c.discount,2)}}</td>
							<td style='width:10%;'>{{ number_format(getItemTotal(c),2) }}</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div v-show="!sales.length && !sales_not_sync.length">
			<div class='alert alert-warning'>No Record Found</div>
		</div>
	</div>
	<div id='loader' v-show="loader">
		<div class="preloader-wrapper active">
			<div class="spinner-layer spinner-red-only">
				<div class="circle-clipper left">
					<div class="circle"></div>
				</div><div class="gap-patch">
					<div class="circle"></div>
				</div><div class="circle-clipper right">
					<div class="circle"></div>
				</div>
			</div>
		</div>
	</div>

	<div  style='position: fixed;bottom: 2px;left:2px;'>
		<span class='green-text' v-show="sync">
			Sync
		</span>
		<span class='red-text' v-show="!sync" @click="syncPending">Not sync</span>
	</div>

</div>
<script type="text/javascript" src="js/jquery211.js" ></script>
<script type="text/javascript" src="js/materialize01.js"></script>
<script type="text/javascript" src="js/vue244.js"></script>
<script type="text/javascript" src="js/moment.js"></script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript" src="js/swipe.js"></script>
<script src='js/vue-paginate.js'></script>
<script src='js/localForage.js'></script>
<script src="upup.min.js"></script>
<script src="upup.sw.min.js"></script>
<script>
	UpUp.start({
		'cache-version': 'v25',
		'content-url': 'pos.php',
		'assets': [
			'fonts/roboto/Roboto-Black.ttf',
			'fonts/roboto/Roboto-BlackItalic.ttf',
			'fonts/roboto/Roboto-Bold.ttf',
			'fonts/roboto/Roboto-BoldItalic.ttf',
			'fonts/roboto/Roboto-Italic.ttf',
			'fonts/roboto/Roboto-Light.ttf',
			'fonts/roboto/Roboto-LightItalic.ttf',
			'fonts/roboto/Roboto-Medium.ttf',
			'fonts/roboto/Roboto-MediumItalic.ttf',
			'fonts/roboto/Roboto-Regular.ttf',
			'fonts/roboto/Roboto-Thin.ttf',
			'fonts/roboto/Roboto-ThinItalic.ttf',
			'css/fonts/mi.woff2',
			'css/materialize.min.css',
			'css/font.css',
			'css/animate.css',
			'css/select2.css',
			'js/jquery211.js',
			'js/materialize01.js',
			'js/vue244.js',
			'js/moment.js',
			'js/select2.js',
			'js/swipe.js',
			'js/vue-paginate.js',
			'js/localForage.js',
			'worker.js',
		]
	});
</script>
<script>
	$(function(){
		$('#main-cart').show();
	});

	var vue = new Vue({
		el:'#app',
		data: {

			paginate: ['paged_items'],
			loader: false,
			bundle_view: false,
			referred_by:'',
			doc_type:'1',
			subs:{},
			terminal_id:'0',
			company_name:'',
			form_member: {name:'',contact:'', email:'' , username:'',password:'',gender:'',bday:'',address:''},
			payment_con: false,
			member_con: false,
			items: [],
			member_type: '1',
			categories:[],
			categ_cur:[],
			categ_child:[],
			cur_items:[],
			cart: [],
			cur_categ:1,
			search_query:'',
			cur_item_cart:{},
			member_id:"",
			cheque_form: {date:'',amount:'',bank:'',number:''},
			card_form: {date:'',amount:'',bank:'',type:''},
			bt_form: {date:'',amount:'',bank:''},
			deduction_form: {amount:'',type:''},
			cash:"",
			member_credit: "",
			cheque:[],
			credit_card:[],
			bank_transfer:[],
			deductions:[],
			bundle_list:[],
			open_bundle_list:[],
			bundle:{name:'',qty:''},
			open_bundle:{name:'',qty:''},
			open_bundle_view: false,
			cart_view:false,
			queue_list_view:false,
			queues:[],
			queue_list:[],
			form_queue:{queue_id:'',hrs:'1', start:'', agent_id:'',agent_name:''},
			form_queue_arr:[],
			current_time:'',
			users:[],
			moreThanOneArea: false,
			reservation_list_view: false,
			reservations: [],
			reservation_id: 0,
			reservation_index: 0,
			sales: [],
			sales_not_sync: [],
			sales_view: false,
			ctrl_number:{invoice:0,dr:0,pr:0},
			is_online:false,
			sync: false


		},
		computed: {

			isOnline:function(){
				return this.is_online;
			},
			allAgents: function(){
				var self = this;
				var items = self.cart;
				var arr_agent = [];
				for(var i in items){
					if(items[i].agent_list.length > 0){
						var agent_list = items[i].agent_list;
						for(var j in agent_list){
							if(agent_list[j].agent_id && agent_list[j].agent_name){
								arr_agent.push({agent_id:agent_list[j].agent_id, agent_name:agent_list[j].agent_name});
							}
						}
					} else {
						if(items[i].agent_id && items[i].agent_name){
							arr_agent.push({agent_id:items[i].agent_id, agent_name:items[i].agent_name});
						}

					}
				}
				return arr_agent;
			},
			moreThanOnePage: function(){
				return Math.ceil((this.myItems.length)/8) > 1
			},
			myItems: function(){
				return this.cur_items;
			},
			agent_list_comp: function(){
				if(this.cur_item_cart.agent_list && this.cur_item_cart.agent_list.length){
					return this.cur_item_cart.agent_list;
				} else {
					return [];
				}
			},
			sortedCategory:function(){
				return this.categ_cur.sort(function(a, b){
					var aname = a.name.toLocaleLowerCase();
					var bname = b.name.toLocaleLowerCase();
					if(aname < bname) return -1;
					if(aname > bname) return 1;
					return 0;
				})
			},
			cartTotal: function(){
				var self = this;
				var cart = self.cart;
				var total = 0;
				for(var i in cart){
					var d = self.getDiscount(cart[i]);
					total = parseFloat(total) + parseFloat(cart[i].price * cart[i].qty) - d;
				}
				return total;
			},
			cheque_total: function(){
				var self = this;
				var total = 0;

				for(var i in self.cheque){
					total = parseFloat(total) + parseFloat(self.cheque[i].amount);
				}
				return self.number_format(total,2);
			},
			credit_total: function(){
				var self = this;
				var total = 0;

				for(var i in self.credit_card){
					total = parseFloat(total) + parseFloat(self.credit_card[i].amount);
				}

				return self.number_format(total,2);
			},
			bt_total: function(){
				var self = this;
				var total = 0;

				for(var i in self.bank_transfer){
					total = parseFloat(total) + parseFloat(self.bank_transfer[i].amount);
				}

				return self.number_format(total,2);
			},
			deduction_total: function(){
				var self = this;
				var total = 0;

				for(var i in self.deductions){
					total = parseFloat(total) + parseFloat(self.deductions[i].amount);
				}

				return self.number_format(total,2);
			},
			cash_total: function(){
				var cash = this.cash;
				if(isNaN(cash)) cash = 0;

				return this.number_format(cash,2);
			},
			member_credit_total: function(){
				var mc = this.member_credit;
				if(isNaN(mc)) mc = 0;

				return this.number_format(mc,2);
			},
			grand_total: function(){
				var self = this;
				var cash = self.number_format(self.cash_total,2,".","");
				var cheque = self.number_format(self.cheque_total,2,".","");
				var credit = self.number_format(self.credit_total,2,".","");
				var bt = self.number_format(self.bt_total,2,".","");
				var deduction = self.number_format(self.deduction_total,2,".","");
				var mc = self.number_format(self.member_credit_total,2,".","");

				var total = parseFloat(cash) + parseFloat(cheque) + parseFloat(credit) + parseFloat(bt) - parseFloat(deduction) + parseFloat(mc);

				return self.number_format(total,2);

			},
			validPayment: function(){
				var self = this;
				var cart =  self.number_format(self.cartTotal,2,".","");
				var total =self.number_format(self.grand_total,2,".","");

				if(parseFloat(total) >= parseFloat(cart)){
					return true;
				}
				return false;


			},
			hasChange: function(){
				var self = this;
				var cart =  self.number_format(self.cartTotal,2,".","");
				var total =self.number_format(self.grand_total,2,".","");

				if(parseFloat(total) > parseFloat(cart)){
					return parseFloat(total) - parseFloat(cart);
				}
				return 0;
			}
		},
		mounted:function(){
			moment.relativeTimeThreshold('m', 60);

			var self = this;

			var mem = $('#member_id');
			var agent = $('#agent_id');
			var referred_by = $('#referred_by');

			self.company_name = localStorage['company_name'];

			$('select').material_select();
			$('#member_gender').on('change', function() {
				self.form_member.gender = $('#member_gender').val();
			});
			$('#doc_type').on('change', function() {
				self.doc_type = $('#doc_type').val();
			});

			mem.select2({
				placeholder: 'Search Client', allowClear: true, minimumInputLength: 2,

				ajax: {
					url: 'ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname + ", " + item.sales_type_name,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});
			agent.select2({
				placeholder: 'Search Agent', allowClear: true, minimumInputLength: 2,

				ajax: {
					url: 'ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'users'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.firstname + " " + item.lastname,
									slug: item.firstname + ", " + item.lastname,
									id: item.id
								}
							})
						};
					}
				}
			});
			mem.change(function(){
				self.member_id = mem.val();
			});
			agent.change(function(){
				self.cur_item_cart.agent_id = agent.val();
				self.cur_item_cart.agent_name = agent.select2('data').text;

			});

			referred_by.select2({
				placeholder: 'Referred by', allowClear: true, minimumInputLength: 2,
				ajax: {
					url: 'ajax/ajax_json.php', dataType: 'json', type: "POST", quietMillis: 50, data: function(term) {
						return {
							q: term, functionName: 'members'
						};
					}, results: function(data) {
						return {
							results: $.map(data, function(item) {

								return {
									text: item.lastname + ", " + item.sales_type_name,
									slug: item.lastname + ", " + item.firstname + " " + item.middlename,
									id: item.id
								}
							})
						};
					}
				}
			});

			referred_by.change(function(){
				self.referred_by = referred_by.val();
				self.checkSubs();
			});


			self.getCategories();
			setTimeout(function(){
				//	self.getProducts();
			},500);


			$('.modal').modal();
			$('ul.tabs').tabs();

			self.terminal_id = (!localStorage['terminal_id']) ? '0' :localStorage['terminal_id'] ;

			self.current_time = moment().format('MMMM Do YYYY, h:mm:ss a');
			setInterval(function(){
				self.current_time = moment().format('MMMM Do YYYY, h:mm:ss a');
				self.checkCompleted();
			},1000);



			self.mainItemSwipe();
			self.cartSwipe();
			self.getSales();
			self.getCtrlNumber();
			var w;
			if (typeof(w) == "undefined") {
				w = new Worker("worker.js");
				w.onmessage = function(event){

					self.is_online = (event.data == 1) ? true : false;
				};
			}
			self.isSync();
			self.localSales();

		},
		methods:{
			trimQueues: function(){
				var self = this;
				localforage.getItem('queue_list',function(err,data){
					var len = data.length;
					var q_count = self.queues.length;
					var rem = (q_count * 2);
					if(rem > 0 && len > rem){
						data = data.slice(-1 * rem);
						localforage.setItem('queue_list',data);
					}
				});
			},
			syncQueues: function(){
				var self = this;
				localforage.getItem('queue_list',function(err,data){
					var not_yet_sync = [];
					for(var i in data){
						if(data[i].sync == 0){
							not_yet_sync.push(data[i])
						}
					}
					if(not_yet_sync.length){
						$.ajax({
							url:'ajax/ajax_pos.php',
							type:'POST',
							data: {functionName:'syncQueues', queues: JSON.stringify(not_yet_sync)},
							success: function(d){
								var ids = JSON.parse(d);
								var not_sync_count = 0;
								for(var up in ids){
									for(var i in data){
										if(data[i].queue_id == ids[up].queue_id && data[i].checkin == ids[up].checkin){
											data[i].sync = 1;
											data[i].id = ids[up].id;
										}
									}
								}

								for(var jj in data){
									if(data[jj].sync == 0){
										not_sync_count = parseInt(not_sync_count) + parseInt(1);
									}
								}

								if(not_sync_count == 0){

									var len = data.length;
									var q_count = 4;
									var rem = (q_count * 2);
									if(rem > 0 && len > rem){
										data = data.slice(-1 * rem);
									}

								}

								localforage.setItem('queue_list', data,function(err){

								});

							},
							error:function(){

							}
						});
					}

				});

			},
			syncSales: function(){

				var self = this;

				localforage.getItem('sales',function(err,data){
					$.ajax({
						url:'ajax/ajax_pos.php',
						type:'POST',
						data: {functionName:'syncSales', sales: JSON.stringify(data)},
						success: function(d){
							if(data && data.length){
								data = [];
								localforage.setItem('sales', data,function(err){
									if(!err){

									}
									self.isSync();
									self.getSales();

								});
							} else {

							}

						},
						error:function(e){
                            console.log(e);
						}
					});
				});
			},
			syncPending: function(){
				var self = this;
				if(self.is_online){
					self.syncQueues();
					self.syncSales();
				} else {
					self.localSales();
				}
			},
			localSales: function(){
				var self = this;
				localforage.getItem('sales',function(err,data){
					if(data && data.length){
						self.sales_not_sync = data.reverse();
					} else {
						self.sales_not_sync = [];
					}

				});
			},
			isSync: function(){

				var self =this;

				localforage.getItem('sales',function(err,data){
					if(data && data.length){
						self.sync = false;
					} else {
						self.sync = true;
					}
				});

			},
			cancelReservation: function(r,index){
				var self = this;
				self.reservations.splice(index,1);
				localforage.setItem('reservations',self.reservations,function(err){});
				/*
				$.ajax({
					url:'ajax/ajax_pos.php',
					type:'POST',
					data: {functionName:'cancelReservation',id: r.id},

					success: function(data){
						//self.reservation_list_view = false;
						self.getReservation();
					},
					error:function(){

					}
				}); */


			},
			getSales: function(){
				var self = this;
				$.ajax({
					url:'ajax/ajax_pos.php',
					type:'POST',
					data: {functionName:'getStoreSales'},
					dataType:'json',
					success: function(data){
						self.sales = data;
					},
					error:function(){

					}
				});
			},
			reservePayment: function(r,index){
				var self = this;
				self.reservation_list_view = false;
				self.reservation_id = r.id;
				self.reservation_index = index;

				for(var j in self.items){

					self.items[j].qty = 0;
					self.items[j].discount = 0;
					self.items[j].agent_id = 0;
					self.items[j].agent_name = 0;
					self.items[j].agent_list = [];

				}

				self.cart = JSON.parse(r.cart);

				for(var i in self.cart){
					for(var j in self.items){
						if(self.items[j].item_id == self.cart[i].item_id){
							self.items[j].qty = self.cart[i].qty;
						}
					}
				}
				self.checkout();
			},
			reserveTotal: function(cart){
				var self = this;
				var cart = JSON.parse(cart);
				var total = 0;
				for(var i in cart){
					var d = self.getDiscount(cart[i]);
					total = parseFloat(total) + parseFloat(cart[i].price * cart[i].qty) - d;
				}
				return total;
			},
			saveReserve: function(){

				var self = this;
				self.saveQueues();
				var queue_names = "";
				var agent_names = "";

				if(self.form_queue_arr.length > 0){
					for(var i in self.form_queue_arr){
						if(self.form_queue_arr[i].agent_id){
							if(!self.form_queue.agent_name){
								agent_names += self.getAgentName(self.form_queue_arr[i].agent_id) + ", ";
							} else {
								agent_names += self.form_queue_arr[i].agent_name + ", ";
							}

						}
						if(self.form_queue_arr[i].queue_id && self.form_queue_arr[i].queue_id != 0){
							queue_names +=  self.getQueueName(self.form_queue_arr[i].queue_id) + ", ";
						}

					}
					if(agent_names)
						agent_names = agent_names.slice(0,-2);
					if(queue_names)
						queue_names = queue_names.slice(0,-2);
				} else {
					if(self.form_queue.agent_id){
						if(!self.form_queue.agent_name){
							agent_names = self.getAgentName(self.form_queue.agent_id);
						} else {
							agent_names = self.form_queue.agent_name;
						}
					}
					if(self.form_queue.queue_id){
						queue_names =  self.getQueueName(self.form_queue.queue_id);
					}
				}
				var cur =  moment().format('MM/DD/YYYY HH:mm').valueOf();
				cur = self.toUnix(cur);

				var obj = {
					id:cur,
					queue_names:queue_names,
					agent_names:agent_names,
					cart:JSON.stringify(self.cart),
					queues:JSON.stringify(self.form_queue),
					queues_list:JSON.stringify(self.form_queue_arr),
				};

				localforage.getItem('reservations',function(err,data){
					if(!err){
						if(data && data.length){
							data.push(obj);
						} else {
							data = [obj];
						}
					} else {
						data = [obj];
					}
					localforage.setItem('reservations',data,function(err){
						for(var j in self.cart){
							self.cart[j].qty = 0;
							self.cart[j].agent_id = 0;
							self.cart[j].agent_name = '';
							self.cart[j].agent_list= [];
						}

						self.cart =[];
						self.resetQueueSelection();
						self.form_queue_arr = [];
						self.getReservation();
						self.showCart();
						Materialize.toast('Reservation inserted successfully', 1000,'green');
					});
				});

			},

			reserveItems: function(){

				var c = confirm("Are you sure you want to reserve this request?");

				if(c){
					var self = this;
					self.saveReserve();
				}


				/*

				self.cart_view = false;
				self.loader = true;

				$.ajax({
					url:'ajax/ajax_pos.php',
					type:'POST',
					dataType:'json',
					data:
					{

						functionName:'insertReservation',
						cart:JSON.stringify(self.cart),
						queues:JSON.stringify(self.form_queue),
						queues_list:JSON.stringify(self.form_queue_arr),
					},
					success: function(data){

						if(data.success){

							Materialize.toast('Reservation inserted successfully', 1000,'green')
							for(var j in self.cart){
								self.cart[j].qty = 0;
								self.cart[j].agent_id = 0;
								self.cart[j].agent_name = '';
								self.cart[j].agent_list= [];
							}

							self.cart =[];
							self.resetQueueSelection();
							self.form_queue_arr = [];
							self.getQueues();
							self.getReservation();

						} else {
							Materialize.toast('Invalid Request', 1000,'red');
						}
						self.loader = false;
					},
					error:function(e){
						self.loader = false;
						alert(JSON.stringify(e));
					}
				});
				*/
			},
			queueComplete: function(qq){
				var self = this;
				/*	$.ajax({
						url:'ajax/ajax_pos.php',
						type:'POST',
						data: {functionName:'queueComplete',queue:JSON.stringify(qq)},
						success: function(data){
							Materialize.toast(data, 500,'green');
							self.getQueues();
						},
						error:function(){

						}
					});*/

				localforage.getItem('queue_list', function(err,data){
					for(var i in data){
						if(data[i].queue_id == qq.queue_id && parseInt(data[i].checkin) == parseInt(qq.checkin)){
							var cur =  moment().format('MM/DD/YYYY HH:mm').valueOf();
							cur = self.toUnix(cur);
							data[i].checkout = cur;
							data[i].sync = 0;


						} else {

						}
					}
					localforage.setItem("queue_list",data,function(err){
						self.initQueues();
					});

				});

			},
			cartSwipe: function(){
				var self = this;
				$("#cart-con-col-3").swipe( {
					//Generic swipe handler for all directions
					excludedElements: "label, button, input, select, textarea",
					allowPageScroll: 'vertical',
					swipe:function(event, direction, distance, duration, fingerCount, fingerData) {

						if(parseInt(distance) > 100){
							if(direction == 'left' ){
								self.showCart();
							} else if (direction == 'right'){
								self.checkout();
							}
						}

					}
				});
			},
			mainItemSwipe: function(){
				var self = this;
				$("#cart-con-list").swipe( {
					//Generic swipe handler for all directions
					allowPageScroll: 'vertical',
					excludedElements: "label, button, input, select, textarea",
					fingers:$.fn.swipe.fingers.ALL,
					swipe:function(event, direction, distance, duration, fingerCount, fingerData) {
						var pp = 0;
						pp = parseInt(self.$refs.paginator.currentPage);
						pp+=1;
						var fc = parseInt(fingerCount);
						if(parseInt(distance) > 100 && fc == 1){
							$('#cart-con-list').removeClass("animated slideInLeft slideInRight");
							if(direction == 'left' ){

								if(pp < self.$refs.paginator.lastPage){
									pp = pp + 1;
									self.$refs.paginator.goToPage(pp)
									$('#cart-con-list').addClass("animated slideInRight faster");
								}
								$("#cart-con-list").animate({ scrollTop: 0}, 100);

							} else if (direction == 'right'){

								if(pp > 1){
									pp = pp - 1;
									self.$refs.paginator.goToPage(pp);
									$('#cart-con-list').addClass("animated slideInLeft faster");
								}

								self.$refs.paginator.goToPage(pp)
								$("#cart-con-list").animate({ scrollTop: 0}, 100);
							}


						}

						if(parseInt(distance) > 100 && direction == 'right' && fc == 2){
							self.showCart()
						}

					}
				});
			},
			checkQty: function(c){
				if(c.item_type == -1 && parseInt(c.qty) > parseInt(c.inv_qty)){
					c.qty = parseInt(c.inv_qty);
					Materialize.toast('Not enough stocks', 1000,'red');
				}
			},
			updateQtyInput: function(c){
				c.edit_qty = 1;
				setTimeout(function(){
					$('#txt'+ c.id).focus();
					$('#txt'+ c.id).select();
				},100);

			},
			getQueueName: function(id){
				var self = this;
				var name = "";
				for(var i in self.queues){
					if(self.queues[i].id == id){
						name = self.queues[i].name;
						break;
					}
				}
				return name;
			},
			getAgentName: function(id){
				var self = this;
				var name = "";
				for(var i in self.users){
					if(self.users[i].id == id){
						name = self.users[i].name;
						break;
					}
				}
				return name;
			},
			addArea: function(){
				var self = this;
				if(self.form_queue.queue_id && self.form_queue.start && self.form_queue.hrs){

					var agent_name = $('#form_queue_agent_id option:selected').text();
					var hit = false;
					for(var i in self.form_queue_arr){
						if(self.form_queue_arr[i].queue_id == self.form_queue.queue_id){
							hit = true;
						}
						if(self.form_queue.agent_id){
							if(self.form_queue_arr[i].agent_id == self.form_queue.agent_id){
								hit = true;
							}
						}

					}
					if(hit){
						Materialize.toast('Duplicate record', 500,'red')
					} else {
						self.form_queue_arr.push({queue_id:self.form_queue.queue_id,start:self.form_queue.start,hrs:self.form_queue.hrs,agent_id:self.form_queue.agent_id,agent_name:agent_name});
						self.resetQueueSelection();

						setTimeout(function(){
							$('#cart-content').scrollTop($('#cart-content')[0].scrollHeight);
						},10);
					}



				} else {
					Materialize.toast('Please complete the form', 500,'red')
				}


			},
			resetAgent: function(){
				this.userInActive();
				this.cur_item_cart.agent_id =0;
				this.cur_item_cart.agent_qty = this.cur_item_cart.qty;
				this.cur_item_cart.agent_list = [];
			},
			addDiscount: function(v,t,c){
				var self =this;
				if(!c){
					var d = 0;
					if(!t){
						d = (parseFloat(self.cur_item_cart.discount) + parseFloat(v));
					} else {
						var total = self.getItemTotal(self.cur_item_cart);
						total = parseFloat(total) + parseFloat(self.cur_item_cart.discount);
						var computed_discount = (v/100) * total;
						d = (parseFloat(self.cur_item_cart.discount) + parseFloat(computed_discount));

					}

					self.cur_item_cart.discount = self.number_format(d,2,'.','');

					self.validateDiscount(self.cur_item_cart);
				} else {
					var total = self.getItemTotal(c);
					total = parseFloat(total) + parseFloat(c.discount);
					var computed_discount = (v/100) * total;
					d = (parseFloat(c.discount) + parseFloat(computed_discount));
					c.discount = self.number_format(d,2,'.','');
				}

			},
			userInActive: function(){
				var self = this;
				for(var i in self.users){
					self.users[i].active = 0;
				}

			},changeUser: function(u){
				var self = this;

				if(u.active == 1){
					self.cur_item_cart.agent_id = 0;
					self.cur_item_cart.agent_name = '';
					u.active = 0;
				} else {
					self.userInActive();
					u.active = 1;
					self.cur_item_cart.agent_id = u.id;
					self.cur_item_cart.agent_name = u.name;
				}


			},
			addPaymentAmount: function(c,amount){
				var self = this;


				self[c] = (self[c]) ? self[c] :0;

				self[c] = parseFloat(self[c]) + parseFloat(amount);
				self[c] = parseFloat(self.number_format(self[c],2,'.',""));

			},
			resetQueueSelection: function(){
				this.form_queue.start = '';
				this.form_queue.queue_id = '';
				this.form_queue.hrs = 1;
				this.form_queue.agent_name = "";
				this.form_queue.agent_id = "";
			},
			getCurrentTime: function(){
				this.form_queue.start =  moment().format('HH:mm');

				$('#form_queue_start').val(this.form_queue.start);
			},
			checkCompleted: function(){
				var self = this;
				var pending = self.queue_list;
				for(var i in pending){
					self.remainingTime(pending[i]);
				}
			},
			remainingTime: function(p){
				if(p.checkout != '0'){
					return moment(p.time_out, "MM/DD/YYYY h:mm").fromNow();
				} else {
					return  "None";
				}

			},
			getDiscount: function(c){
				var d = 0;
				var t = 0;
				if(c.discount && c.discount.indexOf('%') > -1){
					t = c.discount.trim().replace('%','');
					d = c.price * (t/100);
				} else {
					d = c.discount;
				}
				d= (parseFloat(d)) ? parseFloat(d) : 0;
				return d;
			},
			validateDiscount: function(c){


				var total = c.price * c.qty;

				if(parseFloat(c.discount) > parseFloat(total)){
					Materialize.toast('Invalid discount', 1000,'red')
					c.discount = 0;
				}

			},
			showBundleItem: function(c){
				var self = this;
				self.bundle_view = true;
				self.bundle.name = c.description;
				self.bundle.qty = c.qty;
				self.cur_item_cart = c;

				self.bundle_list= JSON.parse(c.bundle_arr);
				for(var i in self.bundle_list){
					self.bundle_list[i]['used_qty'] = self.bundle_list[i]['child_qty'] * c.qty;
					self.bundle_list[i]['total_qty'] = self.bundle_list[i]['child_qty'] * c.qty;
				}


			},
			saveBundle: function(){
				var self = this;
				self.cur_item_cart.bundle_arr = JSON.stringify(self.bundle_list);
				self.bundle_view = false;

			},
			saveOpenBundle: function(){
				var self = this;
				self.open_bundle_view = false;
				for(var i in self.open_bundle_list){
					if(self.open_bundle_list[i].is_chosen){
						self.open_bundle_list[i].show = true;
						self.cart.push(self.open_bundle_list[i]);
					}
				}

			},
			checkSubs: function(){
				var self = this;
				if(self.referred_by){
					$.ajax({
						url:'ajax/ajax_pos.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'checkSubs',member_id:self.referred_by},
						success: function(data){
							if(data.id){
								self.subs = data;
							} else {
								self.subs = {};
								Materialize.toast('No current subscription', 2000,'red')
							}
						},
						error:function(){
							self.subs = {};
						}
					});
				} else {
					self.subs = {};
				}

			},
			goParent: function(){
				var self = this;
				if(self.categ_cur == self.categories){
					this.getItems(-1);
				}
				self.categ_cur = self.categories;


			},
			createClientAccount: function(){
				var self = this;
				if(self.form_member.name && self.form_member.contact  ){
					$.ajax({
						url:'ajax/ajax_pos.php',
						type:'POST',
						dataType:'json',
						data: {functionName:'createClient', member_data: JSON.stringify(self.form_member)},
						success: function(data){
							$('#member_id').select2('data',{id: data.id, text:data.name});
							self.member_id = data.id;
							self.member_con = false;
							self.form_member= {name:'',contact:'', email:'' , username:'',password:'',gender:'',bday:'',address:''};
						},
						error:function(){

						}
					});
				}

			},
			getItemTotal:function (item){
				var qty = parseFloat(item.qty);
				qty = (qty) ? qty : 1;
				var price = item.price;
				var discount = item.discount;
				var d = this.getDiscount(item);

				d= (d) ? d : 0;
				var total = (parseFloat(price) * parseFloat(qty)) - parseFloat(d);
				return total;
			},
			resetPayment: function(){
				var self = this;
				self.cheque_form=  {date:'',amount:'',bank:'',number:''};
				self.card_form = {date:'',amount:'',bank:'',type:''};
				self.bt_form = {date:'',amount:'',bank:''};
				self.deduction_form = {amount:'',type:''};
				self.cash ="";
				self.member_credit= "";
				self.member_id= "";
				self.referred_by= "";
				self.subs={};
				$('#member_id').select2('val',null);
				$('#referred_by').select2('val',null);
				self.cheque = [];
				self.credit_card = [];
				self.bank_transfer = [];
				self.deductions=[];
			},
			numberOnly: function(c,e,extra){
				if(!extra)
					var c = this[c];
				else
					var c = this[c][extra];
				if(isNaN(c) || parseFloat(c) < 1){
					$('#'+e).val(0);
					c = 0;
					Materialize.toast('Invalid amount.', 2000,'red')
				}

			},
			addCheque: function(){
				var self = this;
				self.cheque.push(self.cheque_form);
				self.cheque_form = {date:'',amount:'',bank:'',number:''};
			},
			addCard: function(){
				var self = this;
				self.credit_card.push(self.card_form);
				self.card_form = {date:'',amount:'',bank:'',type:''};
			},
			addBt: function(){
				var self = this;
				self.bank_transfer.push(self.bt_form);
				self.bt_form = {date:'',amount:'',bank:''};
			},
			addDeduction: function(){
				var self = this;
				self.deductions.push(self.deduction_form);
				self.deduction_form = {type:'',amount:''};
			},
			clearSearch: function(){
				this.search_query = '';
				$('#label-autocomplete').removeClass('active');
				$('input.autocomplete').val('');


				this.getItems(-1);
			},
			updateSearch: function(){
				if(this.search_query){
					this.getItems(0);
				} else {
					this.getItems(-1);
				}
			},
			getCategories: function(){
				var self = this;
				self.loader=true;
				$.ajax({
					url:'ajax/ajax_pos.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getCategories'},
					success: function(data){

						localforage.setItem('data', data, function (err) {});

						localforage.setItem('queues', data.queues, function (err) {});

						self.categories = data.main;
						self.categ_cur = data.main;
						self.categ_child = data.child;
						self.items = data.items;
						self.queues = data.queues;
						self.queue_list = data.queue_list;
						self.search_query = '';
						self.users = data.users;
						self.loader=false;
						self.getItems(-1);
						self.initQueues();
						self.getReservation();

						if(data.length == 0) location.href ='login.php';

					},
					error:function(){

						self.loader=false;

						localforage.getItem('data', function (err, data) {

							self.categories = data.main;
							self.categ_cur = data.main;
							self.categ_child = data.child;
							self.items = data.items;
							self.queues = data.queues;
							self.queue_list = data.queue_list;
							self.search_query = '';
							self.users = data.users;
							self.loader=false;
							self.initQueues();
							self.getItems(-1);
							self.getReservation();

						});
					}
				});
			},
			getProducts: function(){
				var self = this;
				$.ajax({
					url:'ajax/ajax_pos.php',
					type:'POST',
					dataType:'json',
					data: {functionName:'getProduct'},
					success: function(data){
						self.items = data.items;
						self.search_query = '';

					},
					error:function(){

					}
				});
			},
			number_format: function(number, decimals, dec_point, thousands_sep) {

				number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
				var n = !isFinite(+number) ? 0 : +number, prec = !isFinite(+decimals) ? 0 : Math.abs(decimals), sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep, dec = (typeof dec_point === 'undefined') ? '.' : dec_point, s = '', toFixedFix = function(n, prec) {
					var k = Math.pow(10, prec);
					return '' + (Math.round(n * k) / k).toFixed(prec);
				};
				// Fix for IE parseFloat(0.55).toFixed(0) = 0;
				s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
				if(s[0].length > 3) {
					s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
				}
				if((s[1] || '').length < prec) {
					s[1] = s[1] || '';
					s[1] += new Array(prec - s[1].length + 1).join('0');
				}
				return s.join(dec);
			},
			getItems: function(c){



				if(!c) c = 0;
				var self = this;

				var categ_child = self.categ_child;
				var arr = [];

				for(var i in categ_child){
					if(categ_child[i].parent == c){
						arr.push(categ_child[i]);
					}
				}


				if(arr.length > 0){

					self.categ_cur = arr;

					c = self.sortedCategory[0].id;
				}

				self.cur_categ =c;


				self.cur_items = self.items.filter(function(i){

					var r =  i.categ_id == c;

					if(self.search_query !== ''){
						return (i.description).toLowerCase().indexOf(self.search_query.toLowerCase()) !== -1;
					}


					if(c == -1){
						return true;
					}
					return r;

				});




			},
			addCart: function(i){
				var self = this;
				i.discount =0;
				if(self.isAlreadyAdded(i.id)){

				} else {

					if(i.has_open_bundle == 1){
						self.open_bundle_view = true;
						$.ajax({
							url:'ajax/ajax_pos.php',
							type:'POST',
							dataType:'json',
							data: {functionName:'getOpenBundleList',id: i.id},
							success: function(data){
								self.open_bundle_list = data;
							},
							error:function(){

							}
						});
					}

					i.qty = 1;
					self.cart.push(i);
					var cur = self.cart.length -1;
					$("#cart-content").animate({ scrollTop: $('#cart-content').prop("scrollHeight")}, 100);


					self.cart[cur].show = true;



				}

			},
			isAlreadyAdded: function(item_id) {
				for(var i in this.cart) {
					if(this.cart[i].id == item_id) {
						if(this.cart[i].item_type == -1 && parseInt(this.cart[i].qty) > parseInt(this.cart[i].inv_qty)){
							Materialize.toast('Not enough stocks', 1000,'red');
						} else {
							this.cart[i].qty = parseInt(this.cart[i].qty) + 1;
						}


						return true;
					}
				}
				return false;
			},
			deductQty: function(i){
				i.qty = i.qty - 1;
				if(!i.qty){
					i.agent_list = [];
					i.agent_id = 0;
					i.agent_name = '';
					this.removeItem(i);
				}
			},
			addQty: function(i){

				if(parseInt(i.qty) + 1 > parseInt(i.inv_qty) && i.item_type == -1){
					Materialize.toast('Not enough stocks', 1000,'red');
				} else {
					i.qty = parseInt(i.qty) + 1;
				}

			},
			removeItem: function(c){

				var self = this;

				var cart = self.cart;

				c.show = false;

				self.cart = self.cart.filter(function(i){
					return i.id != c.id;
				});

			},
			addAgent: function(){

				if(this.cur_item_cart.agent_qty && this.cur_item_cart.agent_id){
					var agent_checker = this.checkAgentListExists(this.cur_item_cart.agent_id);
					if(agent_checker){
						Materialize.toast('Agent already added', 1000,'red');
						return;
					}
					var totalincart= this.checkAgentListQty();

					if(parseFloat(this.cur_item_cart.qty) - totalincart  < parseFloat(this.cur_item_cart.agent_qty)){
						Materialize.toast('Invalid Qty', 1000,'red');
					} else {

						var a = {qty: this.cur_item_cart.agent_qty, agent_id:this.cur_item_cart.agent_id, agent_id:this.cur_item_cart.agent_id, agent_name:this.cur_item_cart.agent_name}

						$('#agent_id').select2('val',null);
						$('#agent_qty').val(1);

						this.cur_item_cart.agent_qty = 1;
						this.cur_item_cart.agent_id = '';
						this.cur_item_cart.agent_name = '';
						this.userInActive();

						this.cur_item_cart.agent_list.push(a);
						setTimeout(function(){
							$('#modalUpdate').scrollTop($('#modalUpdate')[0].scrollHeight);
						},10);
					}

				} else {
					Materialize.toast('Enter client and quantity first.', 1000,'red');
				}

			},
			checkAgentListExists: function(agent_id){
				var ret= false;
				if(this.cur_item_cart.agent_list.length){
					for(var i in this.cur_item_cart.agent_list){
						if(this.cur_item_cart.agent_list[i].agent_id == agent_id){
							ret = true;
						}
					}
				}
				return ret;

			},
			checkAgentListQty: function(){

				var total_qty = 0;
				if(this.cur_item_cart.agent_list.length){
					for(var i in this.cur_item_cart.agent_list){
						total_qty = parseFloat(total_qty) + parseFloat(this.cur_item_cart.agent_list[i].qty);
					}
				}
				return total_qty;

			},
			agentQtyChange: function(){
				if(this.cur_item_cart.agent_qty){
					if(isNaN(this.cur_item_cart.agent_qty)){
						this.cur_item_cart.agent_qty= 1;
						Materialize.toast('Invalid Qty', 1000,'red');
					} else {
						this.cur_item_cart.agent_qty =(this.cur_item_cart.agent_qty).trim();
					}
				}
			},
			editItem: function(c){

				this.cur_item_cart = c;

				this.cur_item_cart.agent_qty = c.qty;

				if(!this.cur_item_cart.agent_id){

					$('#agent_id').select2('val',null);
					this.userInActive();

				} else {
					$('#agent_id').select2('data',{id: this.cur_item_cart.agent_id, text:this.cur_item_cart.agent_name});
				}
				$('#modalUpdate').modal('open');

			},
			closeEditModal: function(){
				$('#modalUpdate').modal('close');
			},
			emptyCart: function(){
				var c = confirm("Are you sure you want to clear your cart?");
				if(c){
					this.cart = [];
					for(var i in this.items) {
						this.items[i].show = false;
						this.items[i].qty = 0;
					}
					this.resetQueueSelection();
					this.showCart();
				}

			},
			showCart: function(){

				this.cart_view = !this.cart_view;


			},
			print_con_data: function(data){
				var form = $('#txtLayout').val();
				var custom_width = localStorage['custom_width'];
				custom_width = (custom_width) ? custom_width : 300;
				form = JSON.parse(form);

				var ret_html = "";
				var prev = false;

				for(var i in form){
					var label = "";

					if(data[form[i].key]){
						if(form[i].type == 'table'){


							if(form[i].style){
								var ob;
								try{
									ob = form[i].style;
									for(var o in ob){
										styles += o +":"+ ob[o] +";";
									}

								}catch(e){

								}
							}
							ret_html += "<table style='margin:0 auto;width:"+custom_width+"px;'>";
							var items = data.items;
							for(var arr in items){
								var divs = (form[i].div).split("|");
								for(var j in divs){
									var props = (divs[j]).split(',');
									ret_html += "<tr>";
									for(var p in props){
										ret_html += "<td style='"+styles+"padding:3px;'>"+items[arr][props[p]]+"</td>";
									}
									ret_html += "</tr>";
								}
							}

							ret_html += "</table>";
						} else {
							var styles = "";
							var has_float = false;

							if(form[i].style){
								var ob;
								try{
									ob = form[i].style;
									for(var o in ob){
										var extra = "";
										if(o == "float"){
											has_float = true;
											prev = true;
										}
										styles += o +":"+ ob[o] +";";
									}

								}catch(e){

								}
							}
							if(!has_float && prev){
								ret_html += "<div style='clear:both;'>&nbsp;</div>";
								prev = false;
							}
							if(form[i].label){
								label = form[i].label + " ";
							}
							ret_html += "<div style='"+styles+"'>"+label+data[form[i].key]+"</div>";
						}
					}
				}

				this.Popup("<div style='width:"+custom_width+"px;' >"+ret_html+"</div>");

			},
			Popup: function(data)
			{
				var mywindow = window.open('', 'new div', '');
				mywindow.document.write('<!DOCTYPE html><html><head><title></title><style></style>');
				mywindow.document.write('</head><body style="padding:0;margin:0;">');
				mywindow.document.write(data);
				mywindow.document.write('</body></html>');

				mywindow.print();
				mywindow.close();
				return true;


			},
			toUnix: function(dt){
				return moment(dt, "MM/DD/YYYY h:mm").valueOf() / 1000;
			},
			toDate: function(dt){
				return moment.unix(dt).format("MM/DD/YYYY HH:mm")
			},
			finalizeTransaction: function(){
				var self = this;

				var total = parseFloat(self.cartTotal);
				var grand_total = parseFloat(self.number_format(self.grand_total,2,'.',''));

				self.cash = (self.cash) ? self.cash : 0;
				var grand_total_withoutcash = grand_total - self.cash;

				if(parseFloat(grand_total_withoutcash) > parseFloat(total)){
					Materialize.toast('Invalid Payment', 1000,'red');
					return;
				}
				if(parseFloat(grand_total) < parseFloat(total)){
					Materialize.toast('Invalid Payment', 1000,'red');
					return;
				}
				self.cash = parseFloat(self.cash) - parseFloat(self.hasChange);

				// START TEST
				var cur_date = Date.now() /1000;
				var d = new Date(cur_date * 1000);
				var month = d.getMonth()+1;
				var day = d.getDate();
				var output = (month<10 ? '0' : '') + month + '/' +
					(day<10 ? '0' : '') + day + '/' + d.getFullYear();


				var item_list = [];
				var self = this;
				var cart = self.cart;
				var pagetotal = 0;
				for(var j in cart){
					var total = cart[j].qty * cart[j].price;
					pagetotal = parseFloat(pagetotal) + parseFloat(total);
					item_list.push({qty:cart[j].qty,item_code:cart[j].description,price:cart[j].price,total:total.toFixed(2),unit_name:'pcs',discount:0,agent_id:0});
				}
				var vat = 1.12;
				var subtotal = (pagetotal / vat);
				var vatable = parseFloat(pagetotal) - parseFloat(subtotal);
				subtotal = subtotal.toFixed(2);
				vatable = vatable.toFixed(2);
				pagetotal = pagetotal.toFixed(2);


				var ctrl_number = self.getCurrentCtrl();


				var awdata = {
					company: 'XYZ Company',
					date: output,
					tin: '1234-1234-1234',
					contact: '02-234-2345',
					ctr_no: ctrl_number,
					items: item_list,
					sub_total: subtotal,
					vat: vatable,
					total: pagetotal,
					remarks: '',
					test: '',
					address: '',
					member: ''
				};

				//this.insertSales();
				this.saveSales();

				if ($(window).width() > 750) {
					//this.print_con_data(awdata);
				}

			},
			getCurrentCtrl: function(){
				var self = this;
				var ctrl_number = '';
				if(self.doc_type == 1){
					ctrl_number = self.ctrl_number.invoice;

				} else if(self.doc_type == 2){
					ctrl_number = self.ctrl_number.dr;
					ctr_number_label = 'DR';
				} else if(self.doc_type == 3){
					ctrl_number = self.ctrl_number.pr;
				}
				return ctrl_number;

			},
			getQueues: function(){
				var self= this;
				$.ajax({
					url:'ajax/ajax_pos.php',
					type:'POST',
					data: {functionName:'getQueues'},
					dataType:'json',
					success: function(data){
						self.queues = data.queues;
						self.queue_list = data.queue_list;


					},
					error:function(){

					}
				});
			},
			getReservation: function(){
				var self= this;
				/*	$.ajax({
						url:'ajax/ajax_pos.php',
						type:'POST',
						data: {functionName:'getReservation'},
						dataType:'json',
						success: function(data){
							self.reservations = data;

						},
						error:function(){

						}
					}); */
				localforage.getItem('reservations',function(err,data){
					if(data && data.length){
						self.reservations = data;
					}


				});
			},
			getCtrlNumber: function(){
				if(localStorage['terminal_id']) {
					var vuecon = this;
					localforage.getItem('sales',function(err,data){

						if(data && data.length){

							localforage.getItem('ctrl_number',function(err,data){
								vuecon.ctrl_number = data;
							});
						}	else {
							$.ajax({
								url: "ajax/ajax_get_branchAndTerminal.php",
								type: "POST",
								data: {cid: localStorage['terminal_id'], type: 3},
								success: function(data) {
									var invarr = data.split(":");
									localStorage["invoice"] = invarr[0];
									localStorage["end_invoice"] = invarr[1];
									localStorage["dr"] = invarr[2];
									localStorage["end_dr"] = invarr[3];
									localStorage["invoice_limit"] = invarr[4];
									localStorage["dr_limit"] = invarr[5];
									localStorage["ir"] = invarr[6];
									localStorage["end_ir"] = invarr[7];
									localStorage["ir_limit"] = invarr[8];
									localStorage["speed_opt"] = invarr[9];
									localStorage["use_printer"] = invarr[10];
									localStorage["data_sync"] = invarr[11];
									localStorage["news_print"] = invarr[12];
									localStorage["print_inv"] = invarr[13];
									localStorage["print_dr"] = invarr[14];
									localStorage["print_ir"] = invarr[15];
									localStorage["pref_inv"] = invarr[16];
									localStorage["pref_dr"] = invarr[17];
									localStorage["pref_ir"] = invarr[18];
									localStorage["suf_inv"] = invarr[19];
									localStorage["suf_dr"] = invarr[20];
									localStorage["suf_ir"] = invarr[21];
									localStorage["sv"] = invarr[22];
									localStorage["sv_limit"] = invarr[23];
									localStorage["suf_sv"] = invarr[24];
									localStorage["pref_sv"] = invarr[25];
									localStorage["sr"] = invarr[26];
									localStorage["sr_limit"] = invarr[27];
									localStorage["suf_sr"] = invarr[28];
									localStorage["pref_sr"] = invarr[29];
									localStorage["ts"] = invarr[30];
									localStorage["ts_limit"] = invarr[31];
									localStorage["suf_ts"] = invarr[32];
									localStorage["pref_ts"] = invarr[33];

									if(localStorage['invoice']) {
										vuecon.ctrl_number.invoice = parseInt(localStorage['invoice']) + 1;
									}
									if(localStorage['dr']) {
										vuecon.ctrl_number.dr = parseInt(localStorage['dr']) + 1;
									}
									if(localStorage['ir']) {
										vuecon.ctrl_number.pr = parseInt(localStorage['ir']) + 1;
									}

									localforage.setItem('ctrl_number', vuecon.ctrl_number, function (err) {

									});


								}, error: function(){
									localforage.getItem('ctrl_number', function (err, data) {
										vuecon.ctrl_number = data;
									});
								}
							});
						}
					})


				} else {

				}
			},
			saveSales: function(){
				var self = this;
				self.saveQueues();
				var obj = {
					referrals:JSON.stringify(self.subs),
					terminal_id:self.terminal_id,
					doc_type:self.doc_type,
					ctrl_number:self.getCurrentCtrl(),
					reservation_id:self.reservation_id,
					member_id:self.member_id,
					cash:self.cash,
					member_credit:self.member_credit,
					cart:JSON.stringify(self.cart),
					cheque:JSON.stringify(self.cheque),
					credit_card:JSON.stringify(self.credit_card),
					bank_transfer:JSON.stringify(self.bank_transfer),
					deductions:JSON.stringify(self.deductions),
					queues:JSON.stringify(self.form_queue),
					queues_list:JSON.stringify(self.form_queue_arr),
					sync: 0,
				};

				localforage.getItem('sales', function (err, data) {
					if(err){
						// set first

						localforage.setItem('sales', [obj], function (err) {
							self.syncPending();
							self.isSync();
						});
					} else {
						if(!data){
							data = [obj]
						} else {
							data.push(obj);
						}
						localforage.setItem('sales', data, function (err) {

							self.syncPending();
							self.isSync();
						});
					}
				});



				self.incrementCtrlNumber(self.doc_type);
				self.resetCart();
				self.resetQueueSelection();
				self.form_queue_arr = [];
				self.cart_view = false;
				self.updateReservation();
				self.paymentContainerToggle();




			},
			updateReservation:function(){
				var self = this;
				if(self.reservation_id){
					if(self.reservations.length){
						self.reservations.splice(self.reservation_index,1);
						localforage.setItem('reservations',self.reservations,function(err){

						});
					}
				}

			},
			currentDate:function(){
				return moment().format('MM/DD/YYYY');
			},
			initQueues: function(){
				var self = this;
				localforage.getItem('queue_list',function(err,data){
					if(data && data.length){
						var cur =  moment().format('MM/DD/YYYY HH:mm').valueOf();
						cur = self.toUnix(cur);
						var queues_pending = [];
						self.queue_list = [];
						for(var i in data){
							if(parseInt(data[i].checkout) > parseInt(cur) ){
								queues_pending.push(parseInt(data[i].queue_id));
								var time_in = self.toDate(data[i].checkin);
								var time_out = self.toDate(data[i].checkout);


								data[i]['time_in'] = time_in;
								data[i]['time_out'] = time_out;
								self.queue_list.push(data[i]);
							}
						}
						localforage.setItem('queue_list',data);

						localforage.getItem('queues',function(err,d){
							self.queues = d;

							self.queues = self.queues.filter(function(v){

								if(queues_pending.indexOf(parseInt(v.id)) == -1){
									return true;
								}
								return false;
							});
						});


					}
				});
			},
			saveQueues: function(){
				var self = this;
				if(self.form_queue_arr.length > 0){

					var arr = [];
					for(var i in self.form_queue_arr){
						if(self.form_queue_arr[i].queue_id && self.form_queue_arr[i].start && self.form_queue_arr[i].hrs){
							var checkin = self.currentDate() + " " + self.form_queue_arr[i].start;
							checkin = self.toUnix(checkin);
							var checkout = parseInt(checkin) + parseInt(self.form_queue_arr[i].hrs) * 3600;
							var obj = {
								queue_id: self.form_queue_arr[i].queue_id,
								checkin: checkin,
								checkout:checkout,
								name:self.getQueueName(self.form_queue_arr[i].queue_id),
								agent_id:self.form_queue_arr[i].agent_id,
								sync: 0,
								id: 0,
							};
							arr.push(obj);
						}
					}
					self.insertQueue(arr);

				} else {

					if(self.form_queue.queue_id && self.form_queue.start && self.form_queue.hrs){
						var checkin = self.currentDate() + " " + self.form_queue.start;
						checkin = self.toUnix(checkin);
						var checkout = parseInt(checkin) + parseInt(self.form_queue.hrs) * 3600;
						var obj = {
							queue_id: self.form_queue.queue_id,
							checkin: checkin,
							name:self.getQueueName(self.form_queue.queue_id),
							checkout:checkout,
							agent_id:self.form_queue.agent_id,
							sync: 0,
							id: 0,

						};
						self.insertQueue([obj]);
					}
				}

			},
			insertQueue:function(arr){
				var self = this;
				localforage.getItem('queue_list', function (err, data) {
					if(err){
						// set first

						localforage.setItem('queue_list', arr, function (err) {
							self.initQueues();
						});
					} else {
						if(!data){

							data = arr;
						} else {
							for(var i in arr){
								data.push(arr[i]);
							}

						}
						localforage.setItem('queue_list', data, function (err) {
							self.initQueues();
						});
					}
				});
			},
			insertSales:function(){

				var self = this;
				self.cart_view = false;
				self.loader = true;
				var ctrl_number = self.getCurrentCtrl();

				$.ajax({
					url:'ajax/ajax_pos.php',
					type:'POST',
					dataType:'json',
					data:
					{

						functionName:'insertSales',
						referrals:JSON.stringify(self.subs),
						terminal_id:self.terminal_id,
						doc_type:self.doc_type,
						ctrl_number:ctrl_number,
						reservation_id:self.reservation_id,
						member_id:self.member_id,
						cash:self.cash,
						member_credit:self.member_credit,
						cart:JSON.stringify(self.cart),
						cheque:JSON.stringify(self.cheque),
						credit_card:JSON.stringify(self.credit_card),
						bank_transfer:JSON.stringify(self.bank_transfer),
						deductions:JSON.stringify(self.deductions),
						queues:JSON.stringify(self.form_queue),
						queues_list:JSON.stringify(self.form_queue_arr),
					},
					success: function(data){

						if(data.success){

							Materialize.toast('Sales inserted successfully', 2000,'green');
							self.incrementCtrlNumber(self.doc_type);
							self.resetCart();
							self.resetQueueSelection();
							self.form_queue_arr = [];
							self.getQueues();
							self.getReservation();
							self.paymentContainerToggle();
							self.getSales();


						} else {
							Materialize.toast('Invalid Request', 1000,'red');
						}
						self.loader = false;
					},
					error:function(e){
						self.loader = false;
						alert(JSON.stringify(e));
					}
				});



			},
			incrementCtrlNumber: function(d){
				var self = this;
				if(d == 1){
					self.ctrl_number.invoice += 1;
				} else if(d == 2){
					self.ctrl_number.dr += 1;
				} else if (d == 3){
					self.ctrl_number.pr += 1;
				}
				localforage.setItem('ctrl_number', self.ctrl_number, function (err) {

				});

			},
			resetCart: function(){
				var self = this;
				for(var j in self.cart){
					self.cart[j].qty = 0;
					self.cart[j].agent_id = 0;
					self.cart[j].discount = 0;
					self.cart[j].agent_name = '';
					self.cart[j].agent_list= [];
				}
				self.cart =[];
			},
			checkout: function(){
				var self = this;
				self.resetPayment();
				self.payment_con = true;
				return;


			},
			paymentContainerToggle: function(){
				var self = this;
				self.payment_con = !self.payment_con;

				if(self.reservation_id != 0){
					self.reservation_id = 0;
					self.reservation_index = 0;
					self.resetCart();
					for(var i in self.items){
						self.items[i].qty = 0;
						self.items[i].agent_id = 0;
						self.items[i].discount = 0;
						self.items[i].agent_name = '';
						self.items[i].agent_list= [];

					}
				}
			}
		}
	});



</script>
</body>
</html>