<?php
/**
 * @author gdxkc
 * @copyright 2012
 */
 error_reporting(0);
session_start();
if($_SESSION['logtag'] != 'in') {
    header("location:login.php");
}else {
    include_once('class.isay.php');
    include_once('config.php');
    if($_GET['id']) {
    	//删除id
        $id = $_GET['id'];
        $isay = new isay;
        $isay->config($server,$user,$password,$db);
        if($isay->connect()) {
            if($isay->deleteItem($id)) {
                header("location:error.php?info=5");
            }else{
                header("location:error.php?info=6");
            }
        }else{
            header("location:error.php?info=1");
        }
    }else {
        header("location:error.php?info=7");
    }
}
?>