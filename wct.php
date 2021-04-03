<?php 

$server = $_SERVER;
$request = $_REQUEST;
$post = $_POST;

$result = array();
$result['server'] = $server;
$result['request'] = $request;
$result['post'] = $post;


echo json_encode($result);


?>