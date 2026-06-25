<?php
    session_start();
    require_once('../config/config.php');   
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $stmt = $db->prepare("SELECT * FROM tbllogininformation WHERE (`username` = :username OR `contact` = :username OR `email` = :username) AND `password` = :password
");
    $stmt->bindParam('username', $username);
    $stmt->bindParam('password', $password);
    $stmt->execute();
    $data = $stmt->fetchAll();

    if(count($data) >= 1){

        if($data[0]['remarks'] == "active"){
            $usertype = $data[0]['accounttype'];
            $userid = $data[0]['id'];
            $_SESSION['userid'] = $userid;
            $_SESSION['usertype'] = $usertype;
            echo 1;
        }

        else{
            echo "Your Account is temporary suspended";
        }
        
    }
    else{
        echo "Invalid Credential";
    }
?>