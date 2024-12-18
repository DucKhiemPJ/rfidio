

<?php
session_start();
if (!isset($_SESSION['Admin-name'])) {
  header("location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Goods</title>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<link rel="icon" type="image/png" href="images/favicon.png">
	<link rel="stylesheet" type="text/css" href="css/managegoods.css">

    <script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.3.1.js"
	        integrity="sha1256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60="
	        crossorigin="anonymous">
	</script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.js"></script>
	<script src="js/manage_goods.js"></script>
	<script>
	  	$(window).on("load resize ", function() {
		    var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
		    $('.tbl-header').css({'padding-right':scrollWidth});
		}).resize();
	</script>
	<script>
	  $(document).ready(function(){
	  	  $.ajax({
	        url: "manage_goods_up.php"
	        }).done(function(data) {
	        $('#manage_goods').html(data);
	      });
	    setInterval(function(){
	      $.ajax({
	        url: "manage_goods_up.php"
	        }).done(function(data) {
	        $('#manage_goods').html(data);
	      });
	    },5000);
	  });
	</script>
</head>
<body>
<?php include'header.php';?>
<main>
	<h1 class="slideInDown animated">Add new Goods or update its information <br> or remove its</h1>
	<div class="form-style-5 slideInDown animated">
		<form enctype="multipart/form-data">
			<div class="alert_user"></div>
			<fieldset>
				<legend><span class="number">1</span> Goods Info</legend>
				<input type="hidden" name="good_id" id="good_id">
				<input type="text" name="good" id="good" placeholder="Identify...">
				<input type="text" name="number" id="number" placeholder="Serial Number...">
				<input type="text" name="origin" id="origin" placeholder="Good origin...">
			</fieldset>
			<fieldset>
			<legend><span class="number">2</span> Additional Info</legend>
			<label>
				<label for="Device"><b>Good Department:</b></label>
                    <select class="dev_sel" name="dev_sel" id="dev_sel" style="color: #000;">
                      <option value="0">All Departments</option>
                      <?php
                        require'connectDB.php';
                        $sql = "SELECT * FROM devices ORDER BY device_name ASC";
                        $result = mysqli_stmt_init($conn);
                        if (!mysqli_stmt_prepare($result, $sql)) {
                            echo '<p class="error">SQL Error</p>';
                        } 
                        else{
                            mysqli_stmt_execute($result);
                            $resultl = mysqli_stmt_get_result($result);
                            while ($row = mysqli_fetch_assoc($resultl)){
                      ?>
                              <option value="<?php echo $row['device_uid'];?>"><?php echo $row['device_dep']; ?></option>
                      <?php
                            }
                        }
                      ?>
                    </select>
				<input type="radio" name="fragile" class="fragile" value="Non Fragile">Non Fragile
				<input type="radio" name="fragile" class="fragile" value="Fragile" checked="checked">Fragile

	      	</label >
			</fieldset>
			<button type="button" name="good_add" class="good_add">Add Goods</button>
			<button type="button" name="good_upd" class="good_upd">Update Goods</button>
			<button type="button" name="good_rmo" class="good_rmo">Remove Goods</button>
		</form>
	</div>

	<!--User table-->
	<div class="section">
		
		<div class="slideInRight animated">
			<div id="manage_goods"></div>
		</div>
	</div>
</main>
</body>
</html>