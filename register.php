<?php



if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $conn = new mysqli("localhost", "root", "", "auth");
    if ($conn->connect_error) {
        http_response_code(500);
        header("Content-Type:application/json");
        echo json_encode(array("message"=>"cant connect to database", "status"=>"error"));
        die();
    }
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['hwid']) && isset($_POST['license'])) {
        $hashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $sql = "SELECT * FROM licenses WHERE license = '" . $_POST['license'] . "'";
        $sql2 = "SELECT * FROM users WHERE username = '".  $_POST['username']."'";
        $sql3 = "INSERT INTO users(username,password,hwid) VALUES('". $_POST['username'] ."', '". $hashed  ."', '". $_POST['hwid'] ."')";
        $sql4 = "DELETE FROM licenses WHERE license= '".$_POST['license']."'";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        if ($res->num_rows > 0) {
            $res2 = $conn->query($sql2);
            $row2 = $res2->fetch_assoc();
            if ($res2->num_rows>0) {
                http_response_code(404);
                header("Content-Type:application/json");
                echo json_encode(array("message"=>"username already exists", "status"=>"failed"));
                die();
            } else {
                $res3 = $conn->query($sql3);
                if ($res3 === true) {
                    $conn->query($sql4);
                    http_response_code(200);
                    header("Content-Type:application/json");
                    echo json_encode(array("message"=>"successfully registered", "status"=>"success"));
                    die();
                }
            }
        } else {
            http_response_code(404);
            header("Content-Type:application/json");
            echo json_encode(array("message"=>"invalid license", "status"=>"failed"));
            die();
        }
    } else {
        http_response_code(404);
        header("Content-Type:application/json");
        echo json_encode(array("message"=>"missing argument", "status"=>"error"));
        die();
    }

} else {
    http_response_code(404);
    header("Content-Type:application/json");
    echo json_encode(array("message"=>"invalid request method", "status"=>"error"));
    die();
}



?>