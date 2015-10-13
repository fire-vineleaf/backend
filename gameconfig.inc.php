<?php

require_once("buildings.inc.php");
$config["isTest"] = true;
$config["initial"]["numPeople"] = 3;
/*
// building type 0 level 1
$config["buildings"][0][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 0 level 2
$config["buildings"][0][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 0 level 3
$config["buildings"][0][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);
// building type 0 level 3
$config["buildings"][0][5] = array(
	"b1" => 49
	,"b2" => 45
	,"b3" => 33
	,"p1" => 100
	,"p2" => 100
	,"duration" => 760
	);
// building type 0 level 3
$config["buildings"][0][6] = array(
	"b1" => 56
	,"b2" => 53
	,"b3" => 39
	,"p1" => 100
	,"p2" => 100
	,"duration" => 890
	);
// building type 0 level 3
$config["buildings"][0][7] = array(
	"b1" => 66
	,"b2" => 63
	,"b3" => 46
	,"p1" => 100
	,"p2" => 100
	,"duration" => 1050
	);
// building type 0 level 3
$config["buildings"][0][8] = array(
	"b1" => 77
	,"b2" => 75
	,"b3" => 54
	,"p1" => 100
	,"p2" => 100
	,"duration" => 1239
	);
// building type 0 level 3
$config["buildings"][0][9] = array(
	"b1" => 90
	,"b2" => 90
	,"b3" => 66
	,"p1" => 100
	,"p2" => 100
	,"duration" => 1474
	);
// building type 0 level 3
$config["buildings"][0][10] = array(
	"b1" => 106
	,"b2" => 108
	,"b3" => 78
	,"p1" => 100
	,"p2" => 100
	,"duration" => 1754
	);
// building type 0 level 3
$config["buildings"][0][11] = array(
	"b1" => 126
	,"b2" => 130
	,"b3" => 95
	,"p1" => 100
	,"p2" => 100
	,"duration" => 2105
	);
// building type 0 level 3
$config["buildings"][0][12] = array(
	"b1" => 150
	,"b2" => 157
	,"b3" => 114
	,"p1" => 100
	,"p2" => 100
	,"duration" => 2523
	);
// building type 0 level 3
$config["buildings"][0][13] = array(
	"b1" => 179
	,"b2" => 191
	,"b3" => 139
	,"p1" => 100
	,"p2" => 100
	,"duration" => 3057
	);
// building type 0 level 3
$config["buildings"][0][14] = array(
	"b1" => 215
	,"b2" => 232
	,"b3" => 169
	,"p1" => 100
	,"p2" => 100
	,"duration" => 3699
	);
// building type 0 level 3
$config["buildings"][0][15] = array(
	"b1" => 259
	,"b2" => 384
	,"b3" => 209
	,"p1" => 100
	,"p2" => 100
	,"duration" => 4512
	);
// building type 0 level 3
$config["buildings"][0][16] = array(
	"b1" => 312
	,"b2" => 349
	,"b3" => 256
	,"p1" => 100
	,"p2" => 100
	,"duration" => 5505
	);
// building type 0 level 3
$config["buildings"][0][17] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);
// building type 0 level 3
$config["buildings"][0][18] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);
// building type 0 level 3
$config["buildings"][0][19] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);
// building type 0 level 3
$config["buildings"][0][20] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);

	
// building type 1 level 1
$config["buildings"][1][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 1 level 2
$config["buildings"][1][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 1 level 3
$config["buildings"][1][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
// building type 2 level 1
$config["buildings"][2][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 2 level 2
$config["buildings"][2][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 2 level 3
$config["buildings"][2][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);
	
	
// building type 3 level 1
$config["buildings"][3][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 3 level 2
$config["buildings"][3][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 3 level 3
$config["buildings"][3][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);
	
// building type 4 level 1
$config["buildings"][4][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 4 level 2
$config["buildings"][4][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 4 level 3
$config["buildings"][4][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
	
// building type 5 level 1
$config["buildings"][5][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 5 level 2
$config["buildings"][5][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 5 level 3
$config["buildings"][5][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
	
// building type 6 level 1
$config["buildings"][6][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 6 level 2
$config["buildings"][6][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 6 level 3
$config["buildings"][6][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
// building type 7 level 1
$config["buildings"][7][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 7 level 2
$config["buildings"][7][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 7 level 3
$config["buildings"][7][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);
	
	
// building type 8 level 1
$config["buildings"][8][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 8 level 2
$config["buildings"][8][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 8 level 3
$config["buildings"][8][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
	
	
// building type 9 level 1
$config["buildings"][9][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 9 level 2
$config["buildings"][9][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 9 level 3
$config["buildings"][9][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
	
	
	
// building type 10 level 1
$config["buildings"][10][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 10 level 2
$config["buildings"][10][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 10 level 3
$config["buildings"][10][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
	
// building type 11 level 1
$config["buildings"][11][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 11 level 2
$config["buildings"][11][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 11 level 3
$config["buildings"][11][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
	
// building type 12 level 1
$config["buildings"][12][2] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 10
	);
// building type 12 level 2
$config["buildings"][12][3] = array(
	"b1" => 100
	,"b2" => 100
	,"b3" => 100
	,"p1" => 100
	,"p2" => 100
	,"duration" => 20
	);
// building type 12 level 3
$config["buildings"][12][4] = array(
	"b1" => 300
	,"b2" => 300
	,"b3" => 300
	,"p1" => 100
	,"p2" => 100
	,"duration" => 30
	);	
	
	*/
	
	
?>