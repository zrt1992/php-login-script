<?php
//Written by Dominick Lee
//Last Modified 2/27/2017
//Enable the below two lines to show errors:
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
include("database.class.php");	//Include MySQL database class
include("mysql.sessions.php");	//Include PHP MySQL sessions
$handler = new Session();	//Start a new PHP MySQL session

//echo "<br><br>".$handler->Login("zrt1992","zrt1992");"<br><br>";
//
//$handler->Login("zrt1992","zrt1992");

if($handler->isLogin()){
    echo "yes ".$handler->sessionid(). " ".$handler->getUserID();
}else{
echo "no";
}

//Store variable as usual
//$_SESSION['user'] = "johnsmith@example.com";
//$_SESSION['age'] = "i am young";
////Show stored user
//echo $_SESSION['user'];
//echo "<br>".session_id()."<br>";

//The following functions are used for sign-out:

//Clear session data (only data column)
//session_unset();

//Destroy the entire session
//session_destroy();
?>