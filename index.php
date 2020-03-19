<?php
    require_once "request_handler.php";
    header("Content-Type: application/json"); // set the response type as json data

    $requestHandler =  new RequestHandler();
    if($requestHandler->validateRequest()){
        echo $_SERVER["REQUEST_METHOD"];
        $requestHandler->handleRequest();
    }
?>


