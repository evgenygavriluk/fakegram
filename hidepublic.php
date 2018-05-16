<?php
session_start();
if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} else {
    include 'dbsettings.inc.php';
    $id = $_SESSION['id'];
    $public_id = $_POST['public_id'];
    $hide_status = $_POST['hide_status'];
    $sql = "UPDATE communities SET comm_active = '$hide_status' WHERE id = '$public_id'";
    $res = mysqli_query($link, $sql) or die(mysqli_error($link));
    mysqli_close($link);
    echo $hide_status;
}