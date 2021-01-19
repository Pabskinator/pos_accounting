<?php
 $http_host = $_SERVER['HTTP_HOST'];
 if($http_host == 'demo.apollosystems.com.ph') {
  // error_reporting(E_ALL);
  // ini_set('display_errors', 1);
  error_reporting(0);
  $mysql_username = "apollosy_demo";
  $mysql_password = "409186963@StephenWang";
  $mysql_database = "apollosy_demo2";


 }else if($http_host == 'zenspa.apollosystems.com.ph') {
  // error_reporting(E_ALL);
  // ini_set('display_errors', 1);
  error_reporting(0);
  $mysql_username = "apollosy_zenspa";
  $mysql_password = "409186963@StephenWang";
  $mysql_database = "apollosy_zenspa";


 } else if($http_host == 'zamaryan.apollosystems.net') {
  // error_reporting(E_ALL);
  // ini_set('display_errors', 1);
  error_reporting(0);
  $mysql_username = "apollos1_sr";
  $mysql_password = "409186963@StephenWang";
  $mysql_database = "apollos1_saintroche";

 } else {
   error_reporting(0);
   $mysql_username = "root";
   $mysql_password = "";
   $mysql_database = "vit_new";
 }
date_default_timezone_set('Asia/Manila');
 $conn = mysqli_connect("localhost",$mysql_username,$mysql_password,$mysql_database);

// Check connection
	if (mysqli_connect_errno())
  {
  die ("Failed to connect to MySQL");
  }
 
?>