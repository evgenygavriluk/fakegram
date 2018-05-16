<?php
session_start();
if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} else $id = $_SESSION['id'];
include 'dbsettings.inc.php';
$topic_id = $_POST['topic_id'];


/* для отладки
$topic_id = $_POST['topic_id'];
$fp = fopen("log_async.log", 'w+');
$str_post = "1: topic_id=$topic_id";
fwrite($fp, $str_post);
fclose($fp);
*/

/* уже не нужен. определял кол-во записей в базе
$q="SELECT count(*) FROM comments WHERE topic_id = \"$topic_id\"";
$res=mysqli_query($link, $q);
$row=mysqli_fetch_row($res);
$comments_count = $row[0];
*/

//$fp = fopen("comm.log", 'w+');
// Выводим список комментариев
$qwery = 'SELECT id, user, touser, text, msg_type, likes, date FROM comments WHERE topic_id = \'' . $topic_id . '\'';
$comments = mysqli_query($link, $qwery) or die(mysqli_error($link));
while ($comment = mysqli_fetch_assoc($comments)) {
    // Получаем имя юзера по его id
    $sqlvu = 'SELECT id, firstname FROM users WHERE id LIKE "' . $comment['user'] . '"';
    $resvu = mysqli_query($link, $sqlvu) or die(mysqli_error($link));
    $vu_user = mysqli_fetch_assoc($resvu);


    //fwrite($fp, implode($comment));

    if ($comment['touser'] != 0) {
        $sqltu = 'SELECT id, firstname FROM users WHERE id LIKE "' . $comment['touser'] . '"';
        $restu = mysqli_query($link, $sqltu) or die(mysqli_error($link));
        $tu_user = mysqli_fetch_assoc($restu);
    }
    if (isset($tu_user['firstname'])) $toUser = $tu_user['firstname']; else $toUser = 0;

    $data[] = array(
        'id' => $vu_user['id'],
        'name' => $vu_user['firstname'],
        'touser_name' => $toUser,
        'msgtext' => $comment['text'],
        'msg_foto' => $comment['id'],
        'msg_type' => $comment['msg_type'],
        'msg_likes' => $comment['likes'],
        'msg_date' => $comment['date']);

    //fwrite($fp, json_encode($data, JSON_UNESCAPED_UNICODE));
}
if (isset($data)) echo json_encode($data, JSON_UNESCAPED_UNICODE);
//fclose($fp);