<?php
// Array with names
$a[] = "Januvary";
$a[] = "Februvary";
$a[] = "March";
$a[] = "April";
$a[] = "May";
$a[] = "June";
$a[] = "July";
$a[] = "August";
$a[] = "September";
$a[] = "October";
$a[] = "November";
$a[] = "December";

$b[] = "2018";
$b[] = "2019";

// get the q parameter from URL
$m = '';
$y = '';
if(isset($_REQUEST["m"])){
	$m = $_REQUEST["m"];
}
else{
	$y = $_REQUEST["y"];
}

$hint = "";

// lookup all hints from array if $m is different from "" 
if ($m !== "") {
    $m = strtolower($m);
    $len=strlen($m);
    foreach($a as $name) {
        if (stristr($m, substr($name, 0, $len))) {
            if ($hint === "") {
                $hint = $name;
            } else {
                $hint .= ", $name";
            }
        }
    }
}

if ($y !== "") {
    $y = strtolower($y);
    $len=strlen($y);
    foreach($b as $name) {
        if (stristr($y, substr($name, 0, $len))) {
            if ($hint === "") {
                $hint = $name;
            } else {
                $hint .= ", $name";
            }
        }
    }
}

// Output "no suggestion" if no hint was found or output correct values 
echo $hint === "" ? "no suggestion" : $hint;
?>