<?php
session_start();
if (isset($_SESSION['id'])) {
    include 'dbsettings.inc.php';
    $email = trim($_SESSION['email']);
    //$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
    $sql = "SELECT id FROM users WHERE email LIKE '$email'";
    $res = mysqli_query($link, $sql) or die(mysqli_error($link));
    $user = mysqli_fetch_assoc($res);

    $data = $_POST['image'];
    list($type, $data) = explode(';', $data);
    list(, $data) = explode(',', $data);
    $data = base64_decode($data);
    $imageName = $user['id'] . '.jpg';
    file_put_contents('users-photoes/' . $imageName, $data);
    echo 'done';
}