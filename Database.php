<?php
require 'DBInterface.php';
ini_set('display_errors', 'Off');

class Database implements DBInterface
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    function connectToDatabase()
    {
        try {
            $conn = new mysqli($this->config["host"], $this->config["user"], $this->config["password"],
                $this->config["database"], $this->config["port"]);
        } catch (Exception $ex) {
            // the user will get empty output in case of connection error
            // but for us we have errors log file and should be out of document root .
            file_put_contents("./errors.log", $ex->getMessage(), FILE_APPEND);
            $conn = false;
        }
        if ($conn->connect_errno) {
            $err = $conn->connect_errno."  :  ".$conn->connect_error.PHP_EOL;
            file_put_contents("./errors.log", $err, FILE_APPEND);
            return false;
        }
        return $conn;
    }

    function disconnect($link)
    {
        $link->close();
    }

    function selectUsers()
    {
        $conn = $this->connectToDatabase();
        if (!$conn) {
            return false;
        }
        $queryStringSelect = "select * from users;";
        $result = $conn->query($queryStringSelect);
        $this->disconnect($conn);
        return $result->num_rows != 0 ? $result : false;
    }

    function selectUser($id)
    {
        $conn = $this->connectToDatabase();
        if (!$conn) {
            return false;
        }
        $stmt = $conn->prepare("select * from users where id = ?;");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $this->disconnect($conn);
        return $result->num_rows != 0 ? $result : false;
    }

    function insertUser($firstName, $email, $gender, $receiveEmails)
    {
        $conn = $this->connectToDatabase();
        if (!$conn) {
            return false;
        }
        $stmt = $conn->prepare("insert into users (first_name,email,gender,receive_emails) values (? , ? ,? ,?);");
        $stmt->bind_param("ssss", $firstName, $email, $gender, $receiveEmails);
        return $this->executeStatement($stmt, $conn);
    }

    function updateUser($id, $firstName, $email, $gender, $receiveEmails)
    {
        $conn = $this->connectToDatabase();
        if (!$conn) {
            return false;
        }
        $stmt = $conn->prepare("update users set first_name=?,email = ?,gender=?,receive_emails=? where id = ?;");
        $stmt->bind_param("ssssi", $firstName, $email, $gender, $receiveEmails, $id);
        return $this->executeStatement($stmt, $conn);
    }

    function deleteUser($id)
    {
        $conn = $this->connectToDatabase();
        if (!$conn) {
            return false;
        }
        $stmt = $conn->prepare("delete from `users` where `id` = ?;");
        $stmt->bind_param("i", $id);
        return $this->executeStatement($stmt, $conn);
    }

    // executes the statement and then closes the connection
    private function executeStatement($stmt, $conn)
    {
        $stmt->execute();
        if ($stmt->affected_rows === 0) $res = false;
        else $res = true;
        $stmt->close();
        $this->disconnect($conn);
        return $res;
    }
}

