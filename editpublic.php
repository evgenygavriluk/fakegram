<?php
session_start();
if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} else $id = $_SESSION['id'];
// Параметры базы данных
include 'dbsettings.inc.php';

$public_id = $_GET['pb'];


// Если нажата кнопка обнавляем запись в базе
if (isset($_POST['comm_update'])) {
    $comm_name = $_POST['comm_name'];
    $comm_description = $_POST['comm_description'];
    $date = @date("Y-m-d");
    $image = $_SESSION['publicImage'];


    $sql = "UPDATE communities SET comm_name='$comm_name', comm_description='$comm_description' WHERE id = '$public_id'";
    $res = mysqli_query($link, $sql) or die(mysqli_error($link));

    list($type, $image) = explode(';', $image);
    list(, $image) = explode(',', $image);
    if (isset($image)) {
        $data = base64_decode($image);
        $imageName = $public_id . '.jpg';
        file_put_contents('publics-logo/' . $imageName, $data);
    }
    /*
    else {
        copy('publics-logo/avatar.jpg', 'users-photoes/'.$avatar_id.'.jpg');
    }
    */

    mysqli_close($link);

// и переходим в паблик
    header('Location:' . $public_id);
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <?php include 'header.inc.php'; ?>
</head>

<body>
<script>
    function show_modal(id) {
        $(id).modal('show');
    };
</script>
<?php
    $sql_comminfo = "SELECT id, comm_name, comm_description FROM communities WHERE id=\"$public_id\"";
    $res_comminfo = mysqli_query($link, $sql_comminfo) or die(mysqli_error($link));
    $comminfo = mysqli_fetch_assoc($res_comminfo);
    $comm_name = $comminfo['comm_name'];
    $comm_description = $comminfo['comm_description'];
?>
<?php include 'mainmenu.tpl'; ?>
<div id="main">
    <h1 class="mainaccount_h1">Edit public <?= $public_id; ?></h1>
    <div id="avatar_photo" class="center">
        <div id="avatar_photo_img"><img src="publics-logo/<?= $public_id; ?>.jpg" width="150px"
                                        class="img-circle"></div>
        <div class="center"></div>
        <div class="center"><a href="#" onclick="show_modal('#ImageUpload')">
                <small><?= $_SESSION['lang']['upload_image']; ?></small>
            </a></div>
    </div>
    <form class="center navbar-form" action="" method="post">
        <label>Commynity name</label>
        <input class="width20" type="text" placeholder="Name" name="comm_name" value="<?= $comm_name; ?>"
               required>
        <label>Description</label>
        <textarea class="width20" rows="5" maxlength="255" placeholder="Description" name="comm_description"
                  required><?= $comm_description; ?></textarea><br>
        <button class="up btn btn-primary btn-info" type="submit" name="comm_update" class="btn-info">Save</button>
    </form>
</div>
<?php include 'imageupload.tpl'; ?>
<script>
    // кроппинг
    $uploadCrop = $('#upload-demo').croppie({
        enableExif: true,
        viewport: {
            width: 200,
            height: 200,
            type: 'circle'
        },
        boundary: {
            width: 200,
            height: 200
        }
    });

    $('#upload').on('change', function () {
        var reader = new FileReader();
        reader.onload = function (e) {
            $uploadCrop.croppie('bind', {
                url: e.target.result
            }).then(function () {
                console.log('jQuery bind complete');
            });

        }
        reader.readAsDataURL(this.files[0]);
    });

    $('.upload-result').on('click', function (ev) {
        $uploadCrop.croppie('result', {
            type: 'canvas',
            size: 'viewport'
        }).then(function (resp) {
            $.ajax({
                url: "ajaxpro-publicslogo.php",
                type: "POST",
                data: {"image": resp},
                success: function (data) {
                    $('#ImageUpload').modal('hide')
                    html = '<img src="' + resp + '" width="150px" class="img-circle"><div class="center"><p><a href="#" onclick="show_modal(\'#ImageUpload\')"><small><?php echo $_SESSION['lang']['upload_image']; ?></small></a></div>';
                    $("#ImageAvatar").html(html);
                    html = '<img src="' + resp + '" width="150px" class="img-circle">';
                    $("#avatar_photo_img").html(html);
                }
            });
        });
    });
</script>
</body>
</html>