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

$response = \Httpful\Request::get($url)
    ->expectsJson()
	->authenticateWith($email, $password)
    ->send();
$section =$response->body;

echo "<h2>section</h2>";
echo "<p>".$section->x1."/".$section->y1." - ".$section->x2."/".$section->y2." | center $centerX/$centerY</p>";

?>
<style>
.field {
	width: 40px;
	height: 40px;
}
.field0 {
	background-color: #5FA9C2;
}
.field1 {
	background-color: #78C25F;
}
.field2 {
	background-color: #A95FC2;
}
.field3 {
	background-color: #C2785F;
}
.field4 {
	background-color: #D1CE38;
}
.field5 {
	background-color: #38D181;
}
</style>
<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

</script>

<div class="row">
<div class="col-md-8">
<table cellpadding=0 cellspacing=2>
<?php
$i = 0;
for ($y = $section->y1; $y<=$section->y2; $y++) {
	echo "<tr>";
	for ($x = $section->x1;$x<=$section->x2;$x++) {
		$field = $section->fields[$i];
		$tooltip = "id: ".$field->fieldId.", type: ".$field->type." (".$field->x."/".$field->y.")";
		echo "<td class='field field".$field->type."'>";
		echo "<div data-toggle='tooltip' title='".$tooltip."'>";
		if (is_null($field->objectId)) {
		echo "&nbsp;";
		} else {
		echo "<a href='index.php?page=camp&id=".$field->objectId."'><b>T</b></a>";
		}
		//echo "(".$field->x."/".$field->y.")";
		//echo "(".$x."/".$y.")";
		echo "</div></td>";
		$i++;
	}
	echo "</tr>";
}
?>
</table>
</div>
	<div class="col-md-4">
	<a href="index.php?page=field&x=<?php echo $centerX-5;?>&y=<?php echo $centerY;?>">left</a>&nbsp;|&nbsp;
	<a href="index.php?page=field&x=<?php echo $centerX+5;?>&y=<?php echo $centerY;?>">right</a>&nbsp;|&nbsp;
	<a href="index.php?page=field&x=<?php echo $centerX;?>&y=<?php echo $centerY+10;?>">up</a>&nbsp;|&nbsp;
	<a href="index.php?page=field&x=<?php echo $centerX;?>&y=<?php echo $centerY-10;?>">down</a>&nbsp;|&nbsp;
	
	</div>
</div>