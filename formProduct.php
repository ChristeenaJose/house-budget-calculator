
<!DOCTYPE html>
<html lang="en">
<head><?php
	include('module/header.php');
?>
<script>
function myFunction() {
  var x = document.getElementById("myDIV");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}

function myFunctionCatName(catName) {
	var a = document.getElementById('fname'); 
	a.value = catName;
}

</script>
</head>
<body>
<?php
	include('module/headerCommon.php');
	include('functions.php');
	
	$mainHeading = "Add Product details";
	$action = "Add";
	$proId = 0 ;
	
	$nameValue = "";
	$dateValue = "";
	$priceValue = "";
	$descValue = "";
	$catValue = "";
	$makeCat = false;
	
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'update' && isset($_REQUEST['proId']) && $_REQUEST['proId'] > 0){
		$action = "Edit";
		$mainHeading = "Edit Product details";
		$proId = $_REQUEST['proId'];
		$resultArr = $classVar->getProductById($proId);
		if(!$resultArr){
			$mainHeading = "Edit Product details, No Record exist";
		}
		else{
			$nameValue = $resultArr['name'];
			$dateValue = $resultArr['date'];
			$priceValue = $resultArr['price'];
			$descValue = $resultArr['description'];
			$catValue = $resultArr['catOrNot'];
			if($catValue == 'N'){
				$makeCat = true;
			}
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
				<form action="?productForm=true">
			  
					<div class="row">
						<div class="col-25">
							<label for="fmonth">Month</label>
						</div>
						<div class="col-25"><?php
								$monthDetails = $classVar->getMonthById($_REQUEST['monthId']);
								print($monthDetails['name']);
							?><input type="hidden" id="fmonth" name="monthId" value="<?php print($_REQUEST['monthId']); ?>">
						</div>
					</div><?php
			  
					if(isset($_REQUEST['catId'])){
						?><div class="row">
							<div class="col-25">
								<label for="fcat">Category</label>
							</div>
							<div class="col-25"><?php
									$productDetails = $classVar->getProductById($_REQUEST['catId']);
									print($productDetails['name']);
								?><input type="hidden" id="fcat" name="catid" value="<?php print($_REQUEST['catId']); ?>">
							</div>
						</div><?php
						
					}
					
					?><div class="row">
						<div class="col-25">
							<label for="fname">Name</label>
						</div>
						<div class="col-25">
							<input type="text" id="fname" name="txtName" placeholder="Enter Product Name.." value="<?php print($nameValue); ?>">
						</div>
						<div class="col-15">
							<input type="checkbox" name="chkCatNameOrNot" onclick="myFunctionCatName('<?php print($productDetails['name']) ?>')" />Use Same Category name for Product
						</div>
						
					</div>
					
					<div class="row" id="myDIV">
						<div class="col-25">
							<label for="fprice">Price</label>
						</div>
						<div class="col-75">
							<input type="text" id="fprice" name="txtPrice" placeholder="Enter Product Price.." value="<?php print($priceValue); ?>">
						</div>
					</div>
					
					<div class="row">
						<div class="col-25">
							<label for="catOrNot">Category</label>
						</div>
						<div class="col-75">							
							<input type="checkbox" name="chkCatOrNot" <?php if($catValue == 'Y'){ print("checked='checked'");} if($action != "Edit"){ print('onclick="myFunction()"'); }?> />
						</div>
					</div>
					
					<div class="row" id="myDIV">
						<div class="col-25">
							<label for="fdate">Date</label>
						</div>
						<div class="col-75">
							<input type="text" id="fdate" name="txtDate" placeholder="Enter Date (0000-00-00)" value="<?php print($dateValue); ?>">
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
					
					<input type="hidden" name="action" value="<?php print($action); ?>">
					<input type="hidden" name="proId" value="<?php print($proId); ?>">
					<input type="hidden" name="makeCat" value="<?php print($makeCat); ?>">
					
					<div class="row">
						<input type="submit" value="Submit">
					</div>
				</form>
				</div>
			</td>
		</tr><?php
	?></tbody>
</table>
<!--new page end--><?php

//Form Field validation
$monthId = 0;
$catid = 0;
$name = "";
$price = "";
$chkCatOrNot = "N";
$action = "";
$proId = "";
$makeCat = "";
if(isset($_REQUEST['monthId'])){
	$monthId = $_REQUEST['monthId'];
}
if(isset($_REQUEST['catid'])){
	$catid = $_REQUEST['catid'];
}
if(isset($_REQUEST['txtName'])){
	$name = $_REQUEST['txtName'];
}
if(isset($_REQUEST['txtPrice'])){
	$price = $_REQUEST['txtPrice'];
}
if(isset($_REQUEST['txtDate'])){
	$date = $_REQUEST['txtDate'];
}
if(isset($_REQUEST['chkCatOrNot'])){
	$chkCatOrNot = $_REQUEST['chkCatOrNot'];
	if($chkCatOrNot == 'on'){
		//$price = 0;
		$chkCatOrNot = "Y";
	}
}
if(isset($_REQUEST['txtDesc'])){
	$desc = $_REQUEST['txtDesc'];
}
if(isset($_REQUEST['action'])){
	$action = $_REQUEST['action'];
}
if(isset($_REQUEST['proId'])){
	$proId = $_REQUEST['proId'];
}
if(isset($_REQUEST['makeCat'])){
	$makeCat = $_REQUEST['makeCat'];
}
if($monthId != 0 && $name != "" && ($price != "" || $price == 0)){
	$arrRs = array();
	$arrRs['monthId'] = $monthId;
	$arrRs['catid'] = $catid;
	$arrRs['name'] = $name;
	$arrRs['price'] = $price;
	$arrRs['date'] = $date;
	$arrRs['catOrNot'] = $chkCatOrNot;
	$arrRs['desc'] = $desc;
	$arrRs['makeCat'] = $makeCat;
	
	if($action = "Edit" && $proId > 0){
		$result = $classVar->updateProduct($arrRs, $proId);
	}
	else{
		$result = $classVar->insertProduct($arrRs);
	}
	
	if($result){
		$headerDetails = $monthId;
		if($catid > 0){
			$headerDetails .= "&catId=".$catid;
		}
		header("Location:monthdetails.php?monthId=".$headerDetails);
		exit; 
	}
	else{
		print("Please submit form again, some mysql error happend");
	}
}
else{
	print("Records are empty, please fill the form");
}


include('module/footer.php');
?>
</body>
</html>
