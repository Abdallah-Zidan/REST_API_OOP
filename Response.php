<?php
require "config.php";
require "Database.php";

class Response {
    private $db;
    public function __construct() {
        $this->db = new Database(DB_CONFIG);
    }

    function returnError($error,$code){
        http_response_code($code);
        $res = json_encode(array("data"=>"","error"=>$error));
        die($res);
    }

    function  returnData($data,$code){
        http_response_code($code);
        $res =json_encode(array("data"=>$data,"error"=>""));
        die($res);
    }

    public function getAll() {
        /* get all users in case no id is provided*/
        $users= array();
        $res = $this->db->selectUsers();
        if($res){
            while ($row = $res->fetch_assoc()) {
                $user= array();
                foreach ($row as $key=>$value) {
                    $user[$key] = $value;
                }
                $users[]=$user;
            }
            $this->returnData($users,200);
        }
        else
            $this->returnError("the query returned empty result",406);
    }
    public function get($id){
        /* get user by id */
        $res = $this->db->selectUser($id);
        $user = $res->fetch_assoc();

        if($user)
            $this->returnData($user,200);

        else
            $this->returnError("user doesn't exist",404);
    }

    public function deleteUser($id){
        $res = $this->db->selectUser($id);
        if(!$res)
            $this->returnError("user doesn't exist",400);

        else{
             $res=$this->db->deleteUser($id);
             $this->queryResponse($res);
        }
    }

    public function updateUser($id,$parameters){
        $res = $this->db->selectUser($id);
        if(!$res)
            $this->returnError("resource not found",404);

        else{
            extract($parameters);
            $res=$this->db->updateUser($id,$first_name,$email,
                $gender,$receive_emails);
            $this->queryResponse($res);
        }
    }

    public  function  insertUser($parameters){
        extract($parameters);
        //($parameters["first_name"],$parameters["email"],$parameters["gender"],$parameters["receive_emails"]);
        $res=$this->db->insertUser($first_name , $email , $gender , $receive_emails);
        $this->queryResponse($res);
    }

    private function queryResponse($res)
    {
        if ($res)
            $this->returnData("success", 201);

        else
            $this->returnError("something went wrong", 406);
    }
}
