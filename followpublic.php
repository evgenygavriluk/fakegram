<?php

session_start();
if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} else {
    include 'dbsettings.inc.php';
    $user_id = $_SESSION['id'];
    $public_id = $_POST['public_id'];
    $follow_status = $_POST['follow_status'];

    if ($follow_status == 1) {
        // Добавляем пользователя в таблицу фолловеров
        $sql = "INSERT INTO followers (public_id, user_id) VALUES('$public_id', '$user_id')";
        $res = mysqli_query($link, $sql) or die(mysqli_error($link));

        $sql = "UPDATE communities SET all_users = all_users+1 WHERE id = '$public_id'";
        $res = mysqli_query($link, $sql) or die(mysqli_error($link));

    } else
        if ($follow_status == 0) {
            // Удаляем пользователя из таблицы фолловеров
            $sql = "DELETE FROM followers WHERE user_id = '$user_id'";
            $res = mysqli_query($link, $sql) or die(mysqli_error($link));

            $sql = "UPDATE communities SET all_users = all_users-1 WHERE id = '$public_id'";
            $res = mysqli_query($link, $sql) or die(mysqli_error($link));
        }
    mysqli_close($link);
    echo $follow_status;
}