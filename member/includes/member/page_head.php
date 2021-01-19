<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Safehouse</title>
	<link rel="stylesheet" href="css/materialize.min.css">
	<link rel="stylesheet" href="css/morris.css">
	<link rel="stylesheet" href="css/dropzone2.css">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<meta name="theme-color" content="#9e9e9e">
	<link rel='shortcut icon' href='img/logo.jpg?v=2'/>

	<style>
	.userCon{
		height: 40px;
		position: relative;
		cursor: pointer;

	
	}
	.userCon:hover{
		background: #dedede;
	}
	.chatAvatar{
		position: absolute;
		left: 5px;
		height: 34px !important;
		padding-top: 0px;
		border: 1px solid #ccc;
		top:4px;
	}
	.chatName{
		height: 34px !important;
		position: absolute;
		left: 50px;
		top:7px;
	}
	#online-list{
		width:280px;
	
		

		 background-color:#fff;
	    -webkit-box-shadow: 0px 0px 0px px #aaaaaa;
	    -moz-box-shadow: -2px 0px 2px 1px #aaaaaa;
	    box-shadow:1px 0px 1px 1px #aaaaaa;
	    z-index:9999;
	    padding:5px;
		overflow-y: auto;
		height: 500px;
	}
	#msg{
		height: 100%;
		
	}
	#txtMessage{
		position: relative;
		bottom: 0px;
	}
	#message-container{
		height:80% !important;
		overflow-y: auto;
		width:100%;
		
	

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

			#no-more-tables tr {
				border: 1px solid #ccc;
			}

			#no-more-tables td {
				/* Behave  like a "row" */
				border: none;
				border-bottom: 1px solid #eee;
				position: relative;
				padding-left: 50%;
				white-space: normal;
				text-align: left;
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
				text-align: left;
				font-weight: bold;
			}

			/*
			Label the data
			*/
			#no-more-tables td:before {
				content: attr(data-title);
			}
		}
		#con_title{
			position: absolute;
			top: 2px;
			left: 10px;

		}
		#con_before{
			position: absolute;
			top: 40%;
			left: 10px;

		}
		#con_after{
			position: absolute;
			top: 40%;
			right: 10px;
		
		}
		.main_heading{
			background-image: linear-gradient(141deg,#232526 0,#414345 100%);
			position: relative;
			height: 250px;
		}
		#con_member_since{
			position: absolute;
			bottom: 10px;
			left: 10px;

		}
		#con_expi{
			position: relative;
			top: 60px;
			
		}
		#con_sessions{
			position: absolute;
			bottom: 30px;
			right: 30px;
		
		}
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
			#con_member_since{
			position: absolute;
			bottom: 10px;
			left: 10px;

		}
		#con_sessions{
			position: absolute;
			top: 10px;
			right: 30px;
		
		}
			#con_before{
			position: static;
			

			}
			#con_after{
				position: static;
				
			
			}
			#con_title{
				position: static;
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
		/* chat */
		@import url(http://fonts.googleapis.com/css?family=Lato:100,300,400,700);
@import url(http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);

html, body {
    background: #e5e5e5;
    font-family: 'Lato', sans-serif;
    margin: 0px auto;
}
::selection{
  background: rgba(82,179,217,0.3);
  color: inherit;
}
a{
  color: rgba(82,179,217,0.9);
}

/* M E N U */

.menu {
    position: fixed;
    top: 0px;
    left: 0px;
    right: 0px;
    width: 100%;
    height: 50px;
    background: rgba(82,179,217,0.9);
    z-index: 100;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.back {
    position: absolute;
    width: 90px;
    height: 50px;
    top: 0px;
    left: 0px;
    color: #fff;
    line-height: 50px;
    font-size: 30px;
    padding-left: 10px;
    cursor: pointer;
}
.back img {
    position: absolute;
    top: 5px;
    left: 30px;
    width: 40px;
    height: 40px;
    background-color: rgba(255,255,255,0.98);
    border-radius: 100%;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    margin-left: 15px;
    }
.back:active {
    background: rgba(255,255,255,0.2);
}
.name{
    position: absolute;
    top: 3px;
    left: 110px;
    font-family: 'Lato';
    font-size: 25px;
    font-weight: 300;
    color: rgba(255,255,255,0.98);
    cursor: default;
}
.last{
    position: absolute;
    top: 30px;
    left: 115px;
    font-family: 'Lato';
    font-size: 11px;
    font-weight: 400;
    color: rgba(255,255,255,0.6);
    cursor: default;
}

/* M E S S A G E S */

.chat {
    list-style: none;
    background: none;
    margin: 0;
    padding: 0 0 50px 0;
    margin-top: 0px;
    margin-bottom: 0px;
}
.chat li {
    padding: 0.5rem;
    overflow: hidden;
    display: flex;
}
.chat .avatar {
    width: 40px;
    height: 40px;
    position: relative;
    display: block;
    z-index: 2;
    border-radius: 100%;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    background-color: rgba(255,255,255,0.9);
}
.chat .avatar img {
    width: 40px;
    height: 40px;
    border-radius: 100%;
    -webkit-border-radius: 100%;
    -moz-border-radius: 100%;
    -ms-border-radius: 100%;
    background-color: rgba(255,255,255,0.9);
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}
.chat .day {
    position: relative;
    display: block;
    text-align: center;
    color: #c0c0c0;
    height: 20px;
    text-shadow: 7px 0px 0px #e5e5e5, 6px 0px 0px #e5e5e5, 5px 0px 0px #e5e5e5, 4px 0px 0px #e5e5e5, 3px 0px 0px #e5e5e5, 2px 0px 0px #e5e5e5, 1px 0px 0px #e5e5e5, 1px 0px 0px #e5e5e5, 0px 0px 0px #e5e5e5, -1px 0px 0px #e5e5e5, -2px 0px 0px #e5e5e5, -3px 0px 0px #e5e5e5, -4px 0px 0px #e5e5e5, -5px 0px 0px #e5e5e5, -6px 0px 0px #e5e5e5, -7px 0px 0px #e5e5e5;
    box-shadow: inset 20px 0px 0px #e5e5e5, inset -20px 0px 0px #e5e5e5, inset 0px -2px 0px #d7d7d7;
    line-height: 38px;
    margin-top: 5px;
    margin-bottom: 20px;
    cursor: default;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.other .msg {
    order: 1;
    border-top-left-radius: 0px;
    box-shadow: -1px 2px 0px #D4D4D4;
}
.other:before {
    content: "";
    position: relative;
    top: 0px;
    right: 0px;
    left: 40px;
    width: 0px;
    height: 0px;
    border: 5px solid #fff;
    border-left-color: transparent;
    border-bottom-color: transparent;
}

.self {
    justify-content: flex-end;
    align-items: flex-end;
}
.self .msg {
    order: 1;
    border-bottom-right-radius: 0px;
    box-shadow: 1px 2px 0px #D4D4D4;
}
.self .avatar {
    order: 2;
}
.self .avatar:after {
    content: "";
    position: relative;
    display: inline-block;
    bottom: 19px;
    right: 0px;
    width: 0px;
    height: 0px;
    border: 5px solid #fff;
    border-right-color: transparent;
    border-top-color: transparent;
    box-shadow: 0px 2px 0px #D4D4D4;
}

.msg {
    background: white;
    min-width: 50px;
    padding: 10px;
    border-radius: 2px;
    box-shadow: 0px 2px 0px rgba(0, 0, 0, 0.07);
}
.msg p {
    font-size: 0.8rem;
    margin: 0 0 0.2rem 0;
    color: #777;
}
.msg img {
    position: relative;
    display: block;
    width: 450px;
    border-radius: 5px;
    box-shadow: 0px 0px 3px #eee;
    transition: all .4s cubic-bezier(0.565, -0.260, 0.255, 1.410);
    cursor: default;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}
@media screen and (max-width: 800px) {
    .msg img {
    width: 300px;
}
}
@media screen and (max-width: 550px) {
    .msg img {
    width: 200px;
}
}

.msg time {
    font-size: 0.7rem;
    color: #ccc;
    margin-top: 3px;
    float: right;
    cursor: default;
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}
.msg time:before{
    content:"\f017";
    color: #ddd;
    font-family: FontAwesome;
    display: inline-block;
    margin-right: 4px;
}

emoji{
    display: inline-block;
    height: 18px;
    width: 18px;
    background-size: cover;
    background-repeat: no-repeat;
    margin-top: -7px;
    margin-right: 2px;
    transform: translate3d(0px, 3px, 0px);
}
emoji.please{background-image: url(http://imgur.com/ftowh0s.png);}
emoji.lmao{background-image: url(http://i.imgur.com/MllSy5N.png);}
emoji.happy{background-image: url(http://imgur.com/5WUpcPZ.png);}
emoji.pizza{background-image: url(http://imgur.com/voEvJld.png);}
emoji.cryalot{background-image: url(http://i.imgur.com/UUrRRo6.png);}
emoji.books{background-image: url(http://i.imgur.com/UjZLf1R.png);}
emoji.moai{background-image: url(http://imgur.com/uSpaYy8.png);}
emoji.suffocated{background-image: url(http://i.imgur.com/jfTyB5F.png);}
emoji.scream{background-image: url(http://i.imgur.com/tOLNJgg.png);}
emoji.hearth_blue{background-image: url(http://i.imgur.com/gR9juts.png);}
emoji.funny{background-image: url(http://i.imgur.com/qKia58V.png);}

@-webikt-keyframes pulse {
  from { opacity: 0; }
  to { opacity: 0.5; }
}

::-webkit-scrollbar {
    min-width: 12px;
    width: 12px;
    max-width: 12px;
    min-height: 12px;
    height: 12px;
    max-height: 12px;
    background: #e5e5e5;
    box-shadow: inset 0px 50px 0px rgba(82,179,217,0.9), inset 0px -52px 0px #fafafa;
}

::-webkit-scrollbar-thumb {
    background: #bbb;
    border: none;
    border-radius: 100px;
    border: solid 3px #e5e5e5;
    box-shadow: inset 0px 0px 3px #999;
}

::-webkit-scrollbar-thumb:hover {
    background: #b0b0b0;
  box-shadow: inset 0px 0px 3px #888;
}

::-webkit-scrollbar-thumb:active {
    background: #aaa;
  box-shadow: inset 0px 0px 3px #7f7f7f;
}

::-webkit-scrollbar-button {
    display: block;
    height: 26px;
}

/* T Y P E */

input.textarea {
    position: fixed !important;
    bottom: -20px  !important;
    left: 300px  !important;
    right: 0px  !important;
    width: 80%  !important;
    height: 50px  !important;
    z-index: 99  !important;
    background: #fafafa  !important;
    border: none  !important;
    outline: none  !important;
    
    padding-right: 70px  !important;
    color: #666  !important;
    font-weight: 400  !important;
}
@media only screen and (max-width : 992px) {
	input.textarea {
    left: 0px  !important;
   
	}
}
.emojis {
    position: fixed;
    display: block;
    bottom: 8px;
    left: 7px;
    width: 34px;
    height: 34px;
    background-image: url(http://i.imgur.com/5WUpcPZ.png);
    background-repeat: no-repeat;
    background-size: cover;
    z-index: 100;
    cursor: pointer;
}
.emojis:active {
    opacity: 0.9;
}
	</style>
	

</head>
<body>