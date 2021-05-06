<!DOCTYPE html>
<html>
<head>
</head>
<body>
<?php

//Class for db connection
class dbConnection{
	
	function dbConnection(){
		$this->conn = $this->OpenCon();
		$this->debug = false;
		if($this->debug) print("Connected Successfully");
	} 
	function OpenCon(){
		$database = include('config.php');
		
		$dbhost = $database['host'];
		$dbuser = $database['user'];
		$dbpass = $database['pass'];
		$db = $database['name'];
		$this->conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $this->conn->error);
		return $this->conn;
	}
	function CloseCon($conn){
		$conn -> close();
	}
	
	//Function for fetching data from Month table
	function getMonth(){
		$sql = "SELECT *
				FROM month ";
				
		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		if ($result->num_rows > 0) {
			
			// output data of each row
			$newArray = array();
			$i = 1;
			while($row = $result->fetch_assoc()) {
				$newArray[$i]["id"] = $row["id"];
				$newArray[$i]["name"] = $row["name"];
				$newArray[$i]["year"] = $row["year"];
				$newArray[$i]["lastupdate"] = $row["lastupdate"];
				$i++;
			}
			return $newArray;
		} 
		else {
			return false;
		}
	}
	function getMonthById($monthId){
		$sql = "SELECT *
				FROM month WHERE id = " . $monthId;
				
		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		if ($result->num_rows > 0) {
			
			// output data of each row
			$newArray = array();
			$row = $result->fetch_assoc();				
			return $row;
		} 
		else {
			return false;
		}
	}
	
	function insertMonth($month, $year, $desc){
		$sql = "INSERT INTO month (name, year, description)
		VALUES ('". $month . "', '" . $year . "', '" . $desc . "')";

		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		
		if ($result === TRUE) {
			return $result;
		} else {
			print( "Error: " . $sql . "<br>" . $conn->error);
			return false;
		}
	}
	
	function updateMonth($month, $year, $desc, $monthId){
		
		$sql = "UPDATE month SET name = '" . $month . "', year = '" . $year . "' , description = '" . $desc . "' WHERE id = " . $monthId;
		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		
		if ($result === TRUE) {
			return $result;
		} else {
			print( "Error: " . $sql . "<br>" . $this->$conn->error);
			return false;
		}
	}
	
	function deleteMonth($id){
		$sql = "DELETE FROM month WHERE id=" . $id;

		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		
		if ($result === TRUE) {
			return $result;
		} else {
			print( "Error: " . $sql . "<br>" . $conn->error);
			return false;
		}
	}
	
	//Function for fetching data from Product table
	function getProduct($monthId, $catId = 0, $catChk = ''){
		
		$sql = "SELECT * , DATE_FORMAT(date, '%d - %M - %Y') newdate FROM product WHERE monthid = " . $monthId . " AND catid = " . $catId;
		
		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		if ($result->num_rows > 0) {
			
			// output data of each row
			$newArray = array();
			$i = 1;
			while($row = $result->fetch_assoc()) {
				$newArray[$i]["id"] = $row["id"];
				$newArray[$i]["monthid"] = $row["monthid"];
				$newArray[$i]["catid"] = $row["catid"];
				$newArray[$i]["name"] = $row["name"];
				$newArray[$i]["price"] = $row["price"];
				$newArray[$i]["catOrNot"] = $row["catOrNot"];
				$newArray[$i]["date"] = $row["date"];
				$newArray[$i]["newdate"] = $row["newdate"];
				$i++;
			}
			return $newArray;
		} 
		else {
			return false;
		}
	}
	
	function getProductById($productId){
		$sql = "SELECT *
				FROM product WHERE id = " . $productId;
				
		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		if ($result->num_rows > 0) {
			
			// output data of each row
			$newArray = array();
			$row = $result->fetch_assoc();				
			return $row;
		} 
		else {
			return false;
		}
	}
	
	//Function for fetching data from Product table
	function getProductSum($monthId, $catId = 0){
		
		$sql = "SELECT SUM(price) totalPrice
				FROM product WHERE monthid = " . $monthId ;
		
		if($catId > 0){
			$sql .= " AND catid = " . $catId;
		}
		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		
		if ($result->num_rows > 0) {
			
			// output data of each row
			$row = $result->fetch_assoc();				
			$totalPrice =  $row['totalPrice'];
			
			$resultArr = $this->getProduct($monthId, $catId, 'Y');
			if($resultArr && $catId > 0){
				foreach($resultArr AS $key => $value){
					$totalPrice += $this->getProductSum($value["monthid"], $value["id"]);
				}
			}
			return $totalPrice;
		} 
		else {
			return false;
		}
	}
	
	function insertProduct($details){
		
		$monthid = $details['monthId'];
		$catid = $details['catid'];
		$name = $details['name'];
		$price = $details['price'];
		$date = $details['date'];
		$catOrNot = $details['catOrNot'];
		$desc = $details['desc'];
		
		if($catOrNot == 'Y'){
			$price = 0;
		}
		
		$sql = "INSERT INTO product (monthid, catid, name, price, date, catOrNot, description)
		VALUES ('". $monthid . "', '" . $catid . "', '" . $name . "', '" . $price . "', '" . $date . "','" . $catOrNot . "','" . $desc . "')";

		if($this->debug){print($sql. '<br/>');}	
		
		$result = $this->conn->query($sql); 
		
		if ($result === TRUE) {
			/*if($catid == 0){
				$last_id = $this->conn->insert_id;
				$sql = "UPDATE product SET catid = " . $last_id . " WHERE id = " . $last_id;
				if($this->debug){print($sql. '<br/>');}		
				$result = $this->conn->query($sql);
				if ($result === TRUE) {
					return $result;
				} else {
					print( "Error: " . $sql . "<br>" . $conn->error);
					return false;
				}
			}
			else{
				return $result;
			}*/
			
			//Update LastUpdate date on month table
			$sql = "UPDATE month SET lastupdate = now() WHERE id = " . $monthid;
			if($this->debug){print($sql. '<br/>');}		
			$result = $this->conn->query($sql);
			if ($result === TRUE) {
				return $result;
			} else {
				print( "Error: " . $sql . "<br>" . $conn->error);
				return false;
			}				
		} else {
			print( "Error: " . $sql . "<br>" . $conn->error);
			return false;
		}
	}
	
	
	function updateProduct($details, $proId){
		
		$name = $details['name'];
		$price = $details['price'];
		$date = $details['date'];
		$catOrNot = $details['catOrNot'];
		$desc = $details['desc'];
		$makeCat = $details['makeCat'];
		if($catOrNot == 'Y'){
			$price = 0;
		}
		
		$sql = "UPDATE product SET name = '" . $name . "', price = '" . $price . "' , date = '" . $date . "' , description = '" . $desc . "' , catOrNot = '" . $catOrNot . "' WHERE id = " . $proId;
		
		if($catOrNot == 'Y' && $makeCat == true){
			$details['catid'] = $proId;
			$details['catOrNot'] = 'N';
			$this->insertProduct($details);
		}

		if($this->debug){print($sql. '<br/>');}	
		
		$result = $this->conn->query($sql); 
		if ($result === TRUE) {
			return $result;
		} else {
			print( "Error: " . $sql . "<br>" . $this->conn->error);
			return false;
		}
	}
	
	function deleteProduct($id){
		$sql = "DELETE FROM product WHERE id=" . $id;

		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		
		if ($result === TRUE) {
			return $result;
		} else {
			print( "Error: " . $sql . "<br>" . $conn->error);
			return false;
		}
	}
	
	
	
	
	//Function for fetching data from table
	function getData($requestParm){
		
		$sql = "SELECT node.idNode, (node.iRight - node.iLeft) DIV 2 AS child_no, name.nodeName AS name
				FROM node_tree AS node,
						node_tree AS parent,
						node_tree_names AS name
				WHERE node.iLeft BETWEEN parent.iLeft AND parent.iRight";
		
		//adding Node id and language to query string
		$sql .= " AND parent.idNode = " . $requestParm['node_id'] ."  AND name.idNode = node.idNode AND name.language = '" . $requestParm['language'] . "'";
		
		//adding Search keyword to query string
		if(isset($requestParm['search_keyword'])){
			$sql .= " AND name.nodeName LIKE '%" . $requestParm['search_keyword'] . "%'";
		}
		
		//adding pagination to query string
		$pagenum = $requestParm['page_num'];
		if ($pagenum < 1) { 
			$pagenum = 1; 
		} 		
		$page_rows = $requestParm['page_size']; 
		$counting = ($page_rows * $pagenum) - ($page_rows) + 1;

		//This sets the range to display in our query 
		$max = 'limit ' .($pagenum - 1) * $page_rows .',' .$page_rows;
	
		$sql .= " ORDER BY node.idNode " . $max;
		
		if($this->debug){print($sql. '<br/>');}		
		$result = $this->conn->query($sql);
		if ($result->num_rows > 0) {
			
			// output data of each row
			$newArray = array();
			$i = 1;
			while($row = $result->fetch_assoc()) {
				$newArray[$i]["node_id"] = $row["idNode"];
				$newArray[$i]["name"] = $row["name"];
				$newArray[$i]["children_count"] = $row["child_no"];
				$i++;
			}
			return $newArray;
		} 
		else {
			return false;
		}
	}
	
	//Function for check mandatory fields
	function mandatoryCheck($param){
		if(!empty($param) && $param != " "){
			return true;
		}
		return false;
	}
	
	//Function for node Id Validation
	function nodeIdValidation($id){
		if($id > 0){
			$sql = "SELECT node.idNode FROM node_tree AS node, node_tree_names AS name WHERE node.idNode = " . $id  ." AND name.idNode = node.idNode";
			$result = $this->conn->query($sql);

			if ($result->num_rows > 0) {
				return true;
			} 
		}
		return false;
	}
}
$classVar = new dbConnection();


//Close DB Connection
//$classVar->CloseCon($classVar->conn);

?>
</body>
</html>