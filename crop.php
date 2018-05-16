<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title></title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap-responsive.css">
	<link rel="stylesheet" href="css/croppie.css">
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<script src="js/bootstrap.js" type="text/javascript"></script>
	<script src="js/croppie.js"></script>

</head>

<body>
<div class="container">
  <div class="panel panel-default">
    <div class="panel-body">

      <div class="row">
		<div class="span3">
			<div id="upload-demo" style="width:200px"></div>
        </div>
        <div class="span3" style="padding-top:30px;">
			<strong>Select Image:</strong>
			<br/>
			<input type="file" id="upload">
			<br/>
			<button class="btn btn-success upload-result">Upload Image</button>
        </div>
        <div class="span3" style="">
			<div id="upload-demo-i" style="background:#e1e1e1;width:200px;height:200px;"></div>
        </div>
      </div>

    </div>
  </div>
</div>

<script type="text/javascript">
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
      }).then(function(){
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
			url: "ajaxpro.php",
			type: "POST",
			data: {"image":resp},
			success: function (data) {
					html = '<img src="' + resp + '" />';
					$("#upload-demo-i").html(html);
					}
		});
  });
});

</script>
</body>
</html>