<?php

//
// Database `odsa_S14`
//

// `odsa_S14`.`opendsa_userbutton`
$db_server = 'localhost';
$db_username = 'mfseddik';
$db_pass = 'MFS_a1s2d3f4g5h6';
$db_name = 'odsa_S14';
$module_id = $argv[1];
$book_id = $argv[2];
$start_date = $argv[3];
$end_date = $argv[4];

$query = "SELECT * 
                FROM  `opendsa_userbutton` 
                WHERE user_id IN 
               (Select user_id from `opendsa_userbook` where grade = 1 AND book_id = $book_id) 
                AND user_id NOT IN (Select user_id from `opendsa_userbook` where grade = 0)
                AND book_id = $book_id
                AND module_id = $module_id
                AND action_time BETWEEN '$start_date' AND '$end_date'
                ORDER BY user_id, action_time, id";
//}
$data = array();
if(@mysql_connect($db_server, $db_username, $db_pass)){
	  if(@mysql_select_db($db_name)){
	    if($query_run = mysql_query($query)){
		  while($query_result = mysql_fetch_assoc($query_run)){
			array_push($data, $query_result); 
		  } 
		}
	}
}
$result = array();
for($i = 0;$i < count($data)-1; $i++){
  if($data[$i]["name"] == 'document-ready' || $data[$i]["name"] == 'window-unload'){
	  if($data[$i]['user_id']==$data[$i+1]['user_id']){
	    array_push($result, $data[$i]);
    }
  }
}

print_r($result);


//calculate_time($result, $module_id, $book_id);

 
//////////////////////////////////////////////////////////////////////////////////////////////

function calculate_time($result, $module_id, $book_id){
  //Calculating the baseline data points
  global $module_name;
  $maxDiff = 0;
  $seconds = array();
  for($i = 0;$i < count($result); $i += 2){
 
    $dateDiff = strtotime($result[$i+1]['action_time']) - strtotime($result[$i]['action_time']);
	  //echo $dateDiff."     ";
	  if($dateDiff > $maxDiff && $dateDiff <= 600){
	    $maxDiff = $dateDiff;
	  }
	  
    if($i < count($result)-2){
      if($result[$i+2]['user_id'] != $result[$i]['user_id']){
        //Add maxDiff to the list
        array_push($seconds, array($result[$i]['user_id'],$maxDiff, $module_name[$module_id], $book_id));
        $maxDiff = 0;
      }
    }
    else{
      //Adding the last one
      array_push($seconds, array($result[$i]['user_id'],$maxDiff, $module_name[$module_id], $book_id));
    
	   }
    
  }
  print_r($seconds);
  //Fill in csv file
  $file = fopen("$module_name[$module_id] In Book $book_id Analysis.csv",'w');
  //Adding headers
  fputcsv($file, array("User_ID","Difference In Seconds" ,"Module Name","Book_ID"));
  foreach($seconds as $line){
    fputcsv($file, $line);
  }

  echo 'Data Written to file Successfuly\n';
}
