
<!DOCTYPE html>
<html lang="en">
<head>
<?php
	include('module/header.php');
?>
<script>
function showHint(str, type) {
	if(type == "M"){
		if (str.length == 0) { 
			document.getElementById("txtHintMonth").innerHTML = "";
			return;
		} else {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("txtHintMonth").innerHTML = this.responseText;
				}
			};
			xmlhttp.open("GET", "module/gethintMonth.php?m=" + str, true);
			xmlhttp.send();
		}
	}
	if(type == "Y"){
		if (str.length == 0) { 
			document.getElementById("txtHintYear").innerHTML = "";
			return;
		} else {
			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
				if (this.readyState == 4 && this.status == 200) {
					document.getElementById("txtHintYear").innerHTML = this.responseText;
				}
			};
			xmlhttp.open("GET", "module/gethintMonth.php?y=" + str, true);
			xmlhttp.send();
		}
	}
}
</script>
</head>
<body>
<?php
	include('module/headerCommon.php');
	include('functions.php');
	
	$mainHeading = "Add Month and Year";
	$action = "Add";
	$monthValue = "";
	$yearValue = "";
	$descValue = "";
	$monthId = 0 ;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' && isset($_REQUEST['monthId']) && $_REQUEST['monthId'] > 0){
		$action = "Edit";
		$mainHeading = "Edit Month and Year";
		$monthId = $_REQUEST['monthId'];
		$resultArr = $classVar->getMonthById($monthId);
		if(!$resultArr){
			$mainHeading = "Edit Month and Year, No Record exist";
		}
		else{
			$monthValue = $resultArr['name'];
			$yearValue = $resultArr['year'];
			$descValue = $resultArr['description'];
		}
	}
	
?><!--new page start-->
<table>
	<thead>
		<tr class="table100-head">
			<th class="column1" colspan="2">
				<a href="index.php"><button class="btn"><i class="fa fa-home"></i> House Budjet</button></a>
				<span> : <?php print($mainHeading); ?></span>
			</th>
		</tr>
	</thead>
	 
	<tbody>
		<tr>
			<td>
				<div class="container">
				<form action="?monthForm=true">
			  
					<div class="row">
						<div class="col-25">
							<label for="fmonth">Month</label>
						</div>
						<div class="col-25">
							<input type="text" id="fmonth" name="txtMonth" value="<?php print($monthValue); ?>" onkeyup="showHint(this.value, 'M')">
						</div>
						<div class="col-25">
							<label>&nbsp;&nbsp;Suggestions: <span id="txtHintMonth"></span></label>
						</div>
					</div>
			  
					<div class="row">
						<div class="col-25">
							<label for="year">Year</label>
						</div>
						<div class="col-75">
							<select id="year" name="txtYear">
								<option value="2018" <?php if($yearValue == '2018'){ print('selected="selected"');}?>>2018</option>
								<option value="2019" <?php if($yearValue == '2019'){ print('selected="selected"');}?>>2019</option>
							</select>
						</div>
					</div>
					
					<div class="row">
						<div class="col-25">
							<label for="desc">Description</label>
						</div>
						<div class="col-75">
						<textarea id="desc" name="txtDesc" placeholder="Write something.." style="height:200px"><?php print($descValue); ?></textarea>
						</div>
					</div>
					<div class="row">
						<input type="submit" value="Submit">
					</div>
					
					<input type="hidden" name="action" value="<?php print($action); ?>">
					<input type="hidden" name="monthId" value="<?php print($monthId); ?>">
					
				</form>
				</div>
			</td>
		</tr><?php
	?></tbody>
</table>
<!--new page end--><?php

//Form Field validation
$month = "";
$year = "";
$desc = "";
$action = "";
$monthId = "";
if(isset($_REQUEST['txtMonth'])){
	$month = $_REQUEST['txtMonth'];
}
if(isset($_REQUEST['txtYear'])){
	$year = $_REQUEST['txtYear'];
}
if(isset($_REQUEST['txtDesc'])){
	$desc = $_REQUEST['txtDesc'];
}
if(isset($_REQUEST['action'])){
	$action = $_REQUEST['action'];
}
if(isset($_REQUEST['monthId'])){
	$monthId = $_REQUEST['monthId'];
}
if($month != "" && $year != ""){
	if($action = "Edit" && $monthId > 0){
		$result = $classVar->updateMonth($month, $year, $desc, $monthId);
	}
	else{
		$result = $classVar->insertMonth($month, $year, $desc);
	}
	
	if($result){
		header("Location:index.php");
		exit; 
	}
	else{
		print("Please submit form again, some mysql error happend");
	}
}
else{
	//print("Records are empty, please fill the form");
}


include('module/footer.php');
?>
</body>
</html>
