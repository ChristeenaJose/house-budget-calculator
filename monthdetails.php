<!DOCTYPE html>
<html lang="en">
<head>
<?php
	include('module/header.php');
?>
</head>
<body>
<?php
	include('module/headerCommon.php');
?>
<?php
include('functions.php');
if(isset($_REQUEST['monthId']) && $_REQUEST['monthId'] > 0){
	
	//Month details
	$monthId = $_REQUEST['monthId'];
	$monthDetails = $classVar->getMonthById($monthId);
		
	//Category details
	$catId = 0;
	$catDetails = array();
	if(isset($_REQUEST['catId']) && $_REQUEST['catId'] > 0){
		$catId = $_REQUEST['catId'];
		$catDetails = $classVar->getProductById($catId);
		$catDetailsSec = $classVar->getProductById($catId);
	}
	
	?><!--new page start-->
	<table>
		<thead>
			<tr class="table100-head">
				<th class="column1" colspan="4">
					<a href="index.php"><button class="btn"><i class="fa fa-home"></i> Budget</button></a>
					<span>
					
						<a href="monthdetails.php?monthId=<?php print($monthDetails["id"]); ?>"><?php 
							print( ' - ' . $monthDetails['name']); 
						?></a><?php
						
						if($catId > 0){
							while($catDetailsSec['catid'] > 0 ){
								$catDetailsSec = $classVar->getProductById($catDetailsSec['catid']);
								?><a href="monthdetails.php?monthId=<?php print($monthDetails["id"]); ?>&catId=<?php print($catDetailsSec["id"]); ?>"><?php 
									print(' / '. $catDetailsSec['name']);
								?></a><?php
							}
							print(' / '. $catDetails['name']);
						}
					?></span>
					<a style="
					float: right;
					border: none;
					background-color: #5cb85c;
					margin-right: 25px;" href="formProduct.php?monthId=<?php print($monthId); 
						if($catId > 0){
							print('&catId='.$catId);
						}?>">
						<button class="btn"><i class="fa fa-folder">&nbsp;Add new</i></button>
					</a>
				</th>
				
			</tr>
		</thead>
		<tbody><?php	
				
				$resultArr = $classVar->getProduct($monthId, $catId);
				if(!$resultArr){
					?><tr>
						<td class="column1" colspan="4">No Record exist</td>
					</tr><?php	
				}
				else{
					foreach($resultArr AS $key => $value){
					?><tr>
						
							
							<?php 
							if($value["catOrNot"] == 'Y'){
								?><td class="column1" colspan="2">
									<a href="monthdetails.php?monthId=<?php print($value["monthid"]); ?>&catId=<?php print($value["id"]); ?>"><?php 
										print($value["name"]); 
									?></a>
								</td><?php
								
								$resultSum = $classVar->getProductSum($value["monthid"], $value["id"]);
								?><td class="column1" colspan="2"><?php if($resultSum > 0){ print($resultSum); }?></td><?php
								
								?><!--<td class="column1"></td><?php
								
								?><td class="column2">
									<a href="?deleteProCat=TRUE&prodCatId=<?php print($value["id"]); ?>">
									<button class="btn"><i class="fa fa-trash"></i></button>
									</a>
								</td>--><?php
						
							}
							else{
								?><td class="column1"><?php
									print($value["name"]); 
								?></td><?php
								
								?><td class="column1"><?php if($value["price"] > 0){ print($value["price"]); }?></td><?php
								
								?><td class="column1"><?php print($value["newdate"]); ?></td><?php
								
								?><td class="column2">
								
								<a href="?deletePro=TRUE&proId=<?php print($value["id"]); ?>&monthId=<?php print($monthId); ?>&catId=<?php print($catId); ?>">
									<button class="btn"><i class="fa fa-trash"></i> Trash</button>
								</a>
								
								<a href="formProduct.php?action=update&proId=<?php print($value["id"]); ?>&monthId=<?php print($monthId); ?>&catId=<?php print($catId); ?>">
									<button class="btn"><i class="fa fa-bars"></i> Edit</button>
								</a>
								
								</td><?php
							}	
						
						?></tr><?php	
					}
				}
		?></tbody>
	</table>
	<!--new page end--><?php
	include('module/footer.php');
	
	//Delete Product
	$flagProDelete = false;
	$proId = 0;
	
	$flagProCatDelete = false;
	$proCatId = 0;
	
	if(isset($_REQUEST['deletePro']) && isset($_REQUEST['proId'])){
		$flagProDelete = $_REQUEST['deletePro'];
		$proId = $_REQUEST['proId'];
	}
	else if(isset($_REQUEST['deleteProCat']) && isset($_REQUEST['proCatId'])){
		$flagProCatDelete = $_REQUEST['deleteProCat'];
		$proCatId = $_REQUEST['proCatId'];
	}
	
	if($flagProDelete == "TRUE" || $flagProCatDelete == "TRUE"){
		if($flagProDelete == "TRUE") { $result = $classVar->deleteProduct($proId); }
		else if($flagProCatDelete == "TRUE"){ $result = $classVar->deleteProCat($proCatId); }
		
		if(!$result){
			print("Please delete again, some mysql error happend");
		}
		else{
			header("Location:monthdetails.php?monthId=" . $monthId . "&catId=" . $catId);
			exit; 
		}
	}
}
else{
	header("Location:index.php");
    exit; 
}
?>
</body>
</html>