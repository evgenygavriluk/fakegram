<?php
session_start();
if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} else $id = $_SESSION['id'];
include 'dbsettings.inc.php';
$public_id = $_POST['public_id'];
$topics_count = 10 + $_POST['topics_count'];

/* для отладки
$topic_id = $_POST['topic_id'];
$fp = fopen("log_async.log", 'w+');
$str_post = "1: topic_id=$topic_id";
fwrite($fp, $str_post);
fclose($fp);
*/

$sql_topicslist = "SELECT id, text, creator, photo FROM offertopics WHERE community = \"$public_id\" and del is null ORDER BY id DESC LIMIT $topics_count";
$res_topicslist = mysqli_query($link, $sql_topicslist) or die(mysqli_error($link));
// Выводим список топиков

while ($topic = mysqli_fetch_assoc($res_topicslist)) {
    $topic_id = $topic['id'];
    $topic_text = $topic['text'];
    $topic_type = $topic['photo'];

    $data[] = array(
        'topic_id' => $topic_id,
        'topic_text' => $topic_text,
        'topic_type' => $topic_type
    );
}

if (isset($data)) echo json_encode($data, JSON_UNESCAPED_UNICODE);