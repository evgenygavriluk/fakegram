<?php
session_start();
if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} else $id = $_SESSION['id'];
include 'dbsettings.inc.php';
$user_id = $_POST['user_id'];
$topics_count = 3 + $_POST['topics_count'];

/* для отладки
$topic_id = $_POST['topic_id'];
$fp = fopen("log_async.log", 'w+');
$str_post = "1: topic_id=$topic_id";
fwrite($fp, $str_post);
fclose($fp);
*/

//$sql_topicslist = "SELECT id, text, photo, likes, repost_id, date, del FROM topics WHERE community = \"$public_id\" and del is null ORDER BY id DESC LIMIT $topics_count";
$sql_topicslist = "SELECT id, text, community, photo, likes, repost_id, date, del FROM topics WHERE (community IN (SELECT public_id FROM followers WHERE user_id = $user_id) OR community IN (SELECT id FROM communities WHERE comm_admin = $user_id))  AND del is null AND repost_id is null ORDER BY id DESC LIMIT $topics_count";

$res_topicslist = mysqli_query($link, $sql_topicslist) or die(mysqli_error($link));
// Выводим список топиков

while ($topic = mysqli_fetch_assoc($res_topicslist)) {
    $topic_id = $topic['id'];
    $topic_text = $topic['text'];
    $topic_community = $topic['community'];
    $topic_time = $topic['date'];
    $topic_type = $topic['photo'];
    $topic_likes = $topic['likes'];
    $topic_repost = $topic['repost_id'];
    // Если пост был репостнут, то кол-во лайков берем из оригинального поста
    $commnamesql = "SELECT comm_name FROM communities WHERE id=$topic_community";
    $commnameres = mysqli_query($link, $commnamesql) or die(mysqli_error($link));
    $commname = mysqli_fetch_assoc($commnameres);
    $topic_comm_name = $commname['comm_name'];

    if (isset($topic_repost)) {
        $likesql = "SELECT likes FROM topics WHERE id=$topic_repost";
        $likeres = mysqli_query($link, $likesql) or die(mysqli_error($link));
        $likes = mysqli_fetch_assoc($likeres);
        $topic_likes = $likes['likes'];
    }

    $data[] = array(
        'topic_id' => $topic_id,
        'topic_text' => $topic_text,
        'topic_community' => $topic_community,
        'topic_comm_name' => $topic_comm_name,
        'topic_type' => $topic_type,
        'topic_likes' => $topic_likes,
        'topic_repost' => $topic_repost,
        'topic_time' => $topic_time);
}

if (isset($data)) echo json_encode($data, JSON_UNESCAPED_UNICODE);