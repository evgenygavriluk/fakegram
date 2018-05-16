<?php
session_start();
if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} else {
    include 'dbsettings.inc.php';
    $id = $_SESSION['id'];
    $topic_id = $_POST['topic_id'];
    // Еще раз проверяем, принадлежит ли паблик из которого удаляем топик админу
    $sql = "SELECT comm.comm_admin FROM communities comm INNER JOIN topics top ON top.community = comm.id WHERE top.id =\"$topic_id\"";
    $res = mysqli_query($link, $sql) or die(mysqli_error($link));
    $comm = mysqli_fetch_assoc($res);
    $owner = $comm['comm_admin'];
    if ($owner == $id) {
        $sql = "UPDATE topics SET del = '1' WHERE id = '$topic_id'";
        $res = mysqli_query($link, $sql) or die(mysqli_error($link));
        mysqli_close($link);
        echo '1';
    } else echo '0';
}