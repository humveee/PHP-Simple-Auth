<?php


if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $conn = new mysqli("localhost", "root", "", "auth");
    if ($conn->connect_error) {
        http_response_code(500);
        header("Content-Type:application/json");
        echo json_encode(array("message"=>"cant connect to database", "status"=>"error"));
        die();
    }
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['hwid'])) {
        $sql = "SELECT * FROM users WHERE username = '" . $_POST['username'] . "'";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        if ($res->num_rows > 0) {
            if (password_verify($_POST['password'], $row['password'])) {
                if ($_POST['hwid'] == $row['hwid']) {
                    http_response_code(200);
                    header("Content-Type:application/json");
                    echo json_encode(array("message"=>"authorized", "status"=>"success"));
                    die();
                } else {
                    http_response_code(404);
                    header("Content-Type:application/json");
                    echo json_encode(array("message"=>"invalid hwid", "status"=>"failed"));
                    die();
                }
            } else {
                http_response_code(404);
                header("Content-Type:application/json");
                echo json_encode(array("message"=>"invalid password", "status"=>"failed"));
                die();
            }
        } else {
            http_response_code(404);
            header("Content-Type:application/json");
            echo json_encode(array("message"=>"invalid username", "status"=>"failed"));
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