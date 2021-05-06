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
	include('functions.php');
?>
<!--new page start-->				
<table>
	<thead>
		<tr class="table100-head">
			<th class="column1" colspan="4">
				<span>House Budjet</span>
				
				<a style="
				float: right;
				border: none;
				background-color: #5cb85c;
				margin-right: 25px;" href="formMonth.php">
					<button class="btn"><i class="fa fa-folder">&nbsp;Add new</i></button>
				</a>
			</th>
			
		</tr>
		
	</thead>
	<tbody><?php	
			
			$resultArr = $classVar->getMonth();
			if(!$resultArr){
				?><tr>
					<td class="column1" colspan="3">No Record exist</td>
				</tr><?php	
			}
			else{
				
				?><tr>
					<td class="column1"><b>Month Name</b></td>
					<td class="column1"><b>Total Expense</b></td>
					<td class="column1"><b>Last Update</b></td>
					<td class="column1"><b>Action</b></td>
				</tr><?php
		
				foreach($resultArr AS $key => $value){
				?><tr>
					
					<td class="column1">
						<a href="monthdetails.php?monthId=<?php print($value["id"]); ?>"><?php print($value["name"] . '- ' . $value["year"]); ?></a>
					</td>
					
					<?php
					
					//Total Price
					$resultPrice = $classVar->getProductSum($value["id"]);					
					?><td class="column1"><?php if($resultPrice > 0 ){ print($resultPrice); }?></td><?php
					
					//Last Update
					?><td class="column1"><?php if($resultPrice > 0 ){ print($value["lastupdate"]); }?></td><?php
					
					?><td class="column2" >
						<a href="?deleteMonth=TRUE&monthId=<?php print($value["id"]); ?>">
						<button class="btn"><i class="fa fa-trash"></i> Trash</button>
						</a>
						
						<a href="formMonth.php?action=update&monthId=<?php print($value["id"]); ?>">
						<button class="btn"><i class="fa fa-bars"></i> Edit</button>
						</a>
					</td>
				</tr><?php	
				}
			}
	?></tbody>
</table>
<!--new page end--><?php
include('module/footer.php');

$flagMonthDelete = false;
$monthId = 0;
if(isset($_REQUEST['deleteMonth']) && isset($_REQUEST['monthId'])){
	$flagMonthDelete = $_REQUEST['deleteMonth'];
	$monthId = $_REQUEST['monthId'];
}
if($flagMonthDelete == "TRUE"){
	$result = $classVar->deleteMonth($monthId);
	if(!$result){
		print("Please delete again, some mysql error happend");
	}
	else{
		header("Location:index.php");
		exit; 
	}
}
else{
	print("Records are empty, please fill the form");
}
?>
</body>
</html>