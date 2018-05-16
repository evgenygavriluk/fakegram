<?php
session_start();
if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} else {
    include 'dbsettings.inc.php';
    $id = $_SESSION['id'];
    $topic_id = $_POST['topic_id'];
    $sql = "UPDATE offertopics SET del = '1' WHERE id = $topic_id";
    $res = mysqli_query($link, $sql) or die(mysqli_error($link));
    mysqli_close($link);
    echo '1';
}