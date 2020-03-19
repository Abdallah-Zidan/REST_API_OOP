<?php
require './Response.php';

class RequestHandler
{
    private $method;
    private $resource;
    private $resource_id;
    private $parameters = array();
    private $allowed_methods = array();
    private $response;

    public function __construct()
    {
        // example url => localhost/index.php/users/1
        $this->method = $_SERVER["REQUEST_METHOD"];
        $url_pieces = explode("/", $_SERVER["REQUEST_URI"]);
        $this->resource = isset($url_pieces[3]) ? $url_pieces[3] : -1;
        $this->resource_id = isset($url_pieces[4]) ? $url_pieces[4] : -1;
        $this->allowed_methods = ["GET", "POST", "PUT", "DELETE"];

        if ($this->method == "POST" || $this->method == "PUT")
            $this->parameters = json_decode(file_get_contents("php://input"), true);

        $this->response = new Response();
    }

    public function validateRequest()
    {
        if (!(in_array($this->method, $this->allowed_methods)))
            $this->response->returnError("method not allowed", 405);

        else if ($this->resource != "users")
            $this->response->returnError("resource not found", 404);

        else if (!is_numeric($this->resource_id))
            $this->response->returnError("resource not found", 404);

        else if (($this->method == "POST" || $this->method == "PUT") &&
            !$this->validParameters($this->parameters))
                $this->response->returnError("invalid user data ", 400);
        else
            $this->handleRequest();
    }

    private function validParameters()
    {
        return (isset($this->parameters["first_name"]) && isset($this->parameters["email"]) && isset($this->parameters["gender"]) && isset($this->parameters["receive_emails"]));
    }

    public function handleRequest()
    {
        switch ($this->method) {
            case "GET":

                if ($this->resource_id == -1)
                    $this->response->getAll();

                else
                    $this->response->get($this->resource_id);

                break;
            case "DELETE":

                if ($this->resource_id == -1)
                    $this->response->returnError("resource not found", 404);

                else
                    $this->response->deleteUser($this->resource_id);

                break;
            case "PUT":

                if ($this->resource_id == -1)
                    $this->response->returnError("resource not found", 404);

                else
                    $this->response->updateUser($this->resource_id, $this->parameters);

                break;
            case "POST":
                if ($this->resource_id != -1)
                    $this->response->returnError("resource not found", 404);

                else{
                    $this->response->insertUser($this->parameters);
                }

                break;
        }
    }
}
