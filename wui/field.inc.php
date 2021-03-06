<style>
.caption {
	font-size: .8em;
	background-color: white;
	margin-bottom: 0px;
	margin-top: auto;
}
.hex {
    float: left;
    margin-right: -26px;
    margin-bottom: -50px;
}
.hex .left {
    float: left;
    width: 0;
    border-right: 30px solid #6C6;
    border-top: 52px solid transparent;
    border-bottom: 52px solid transparent;
	z-index: 99;
}
.hex .middle {
    float: left;
    width: 60px;
    height: 104px;
    background: #6C6;
	z-index: 100;
}
.hex .right {
    float: left;
    width: 0;
    border-left: 30px solid #6C6;
    border-top: 52px solid transparent;
    border-bottom: 52px solid transparent;
	z-index: 99;
}
.hex-row {
    clear: left;
}
.hex.even {
    margin-top: 53px;
}

.field {
	width: 40px;
	height: 40px;
	padding: 10px;
}
.hex .field0 {
	background-image: url("images/lake.png");
	background-repeat: no-repeat;
}
.hex .field1 {
	background-image: url("images/grass.png");
	background-repeat: no-repeat;
}
.hex .field2 {
	background-image: url("images/bushes.png");
	background-repeat: no-repeat;
}
.hex .field3 {
	background-image: url("images/lake.png");
	background-repeat: no-repeat;
}
.hex .field4 {
	background-image: url("images/rocks.png");
	background-repeat: no-repeat;
}
.hex .field5 {
	background-image: url("images/deer.png");
	background-repeat: no-repeat;
}
.hex .fieldObject {
	background-image: url("images/tree.png");
	background-repeat: no-repeat;
}
.status0 {
	background-color: #AAAAAA;
}
.status1 {
	background-color: red;
}
.status2 {
	background-color: green;
}
.status3 {
	background-color: orange;
}
.status4 {
	background-color: blue;
}

</style>
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

</script>
<?php
if (isset($_GET["x"])) {
	$centerX = $_GET["x"];
} else {
	$centerX = 50005;
}
if (isset($_GET["y"])) {
	$centerY = $_GET["y"];
} else {
	$centerY = 50010;
}

$url = $baseUrl."?a=section&x=".$centerX."&y=".$centerY;
var_dump($url);

/*
$response = \Httpful\Request::get($url)
    ->expectsJson()
	->authenticateWith($email, $password)
    ->send();
$section =$response->body;
*/
$section = $service->getSection($centerX, $centerY);
?>
<div class="row">
	<div class="col-md-3"><h2>section</h2></div>
	<div class="col-md-3"><?php echo "<p>".$section->x1."/".$section->y1." - ".$section->x2."/".$section->y2." | center $centerX/$centerY</p>"; ?></div>
	<div class="col-md-3">
	<a href="index.php?page=field&x=<?php echo $centerX-5;?>&y=<?php echo $centerY;?>">left</a>&nbsp;|&nbsp;
	<a href="index.php?page=field&x=<?php echo $centerX+5;?>&y=<?php echo $centerY;?>">right</a>&nbsp;|&nbsp;
	<a href="index.php?page=field&x=<?php echo $centerX;?>&y=<?php echo $centerY+10;?>">up</a>&nbsp;|&nbsp;
	<a href="index.php?page=field&x=<?php echo $centerX;?>&y=<?php echo $centerY-10;?>">down</a>&nbsp;|&nbsp;
	</div>
</div>
<?php
$i = 0;
$j = 0;
for ($y = $section->y1; $y<=$section->y2; $y++) {
	echo "<div class=\"hex-row\">";
	$j++;
	$divider = $j%2;
	for ($x = $section->x1;$x<=$section->x2;$x++) {
		if (!isset($section->fields[$i])) continue;
		$field = $section->fields[$i];
		$tooltip = "id: ".$field->fieldId.", type: ".$field->type." (".$field->x."/".$field->y.")";
		$isObject = !is_null($field->objectId);
		$even = "";		
		
		if ($i%2==$divider) {
			$even = " even";
		}
		echo "<div class='hex$even'>";
		echo "<div class='left'></div>";
		echo "<div class='middle field ";
		if ($isObject) {
			echo "fieldObject";
		} else {
			echo "field".$field->type;
		}
		echo "'>";
		
		echo "<div data-toggle='tooltip' title='".$tooltip."'>";
		if (!$isObject) {
			echo "&nbsp;";
		} else {
			echo "<div class=\"caption\"><span class=\"status".$field->clan["status"]."\">&nbsp;&nbsp;</span>";
			echo "<a href='index.php?page=camp&id=".$field->objectId."'>";
			echo "".$field->camp["name"]."</a><br/>".$field->camp["points"]."</div>";
		}
		//echo "(".$field->x."/".$field->y.")";
		//echo "(".$x."/".$y.")";
		echo "</div>";
		echo "</div>";
		echo "<div class='right'></div>";
		echo "</div>";
		$i++;
	}
	echo "</div>";
}
?>
