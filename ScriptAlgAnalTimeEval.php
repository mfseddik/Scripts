<?php
/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 0.2b
 */

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
$module_name = array();
//Initialize module names
for($i=0;$i<100;$i++){
  array_push($module_name, " ");
}
$module_name[4] = "Insertionsort";
$module_name[3] = "Bubblesort";
$module_name[6] = "Selectionsort";
$module_name[96] = "Mergesort";
$module_name[69] = "Quicksort";
$module_name[70] = "Heapsort";
$module_name[99] = "Radixsort";

// if (isset($argv[3])){
//  $student_id = $argv[3];
//  $query = "SELECT * 
//                 FROM  `opendsa_userbutton` 
//                 WHERE user_id = $student_id
//                 AND book_id =5 
// 				        AND module_id =$module_id
//                 ORDER BY user_id, action_time, id";
// }
//else{
$query = "SELECT * 
                FROM  `opendsa_userbutton` 
                WHERE user_id IN 
               (Select user_id from `opendsa_userbook` where grade = 1 AND book_id = $book_id) 
                AND user_id NOT IN (Select user_id from `opendsa_userbook` where grade = 0)
                AND book_id = $book_id
                AND module_id =$module_id
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
  if(match_analysis($data[$i]['description'])){
	  if($data[$i]['user_id']==$data[$i+1]['user_id']){
	    array_push($result, $data[$i]);
      array_push($result, $data[$i+1]);
    }
  }
}

//distinct_ip_match_analysis($result);
calculate_baseline($result, $module_id, $book_id);
//print_r($result);
 
//////////////////////////////////////////////////////////////////////////////////////////////
function distinct_ip_match_analysis($result){
  $user_ip = array();
  $ip_record = array();
  for($i=0; $i<count($result); $i++){
	if(!search_assoc($user_ip, $result[$i]["ip_address"])){
      $ip_record["user_id"] = $result[$i]["user_id"];
	  $ip_record["ip_address"] = $result[$i]["ip_address"];
	  array_push($user_ip, $ip_record);
    }	
  }
  print_r($user_ip);
}

function calculate_baseline($result, $module_id, $book_id){
  //Calculating the baseline data points
  global $module_name;
  $maxDiff = 0;
  $seconds = array();
  for($i = 0;$i < count($result); $i += 2){
    if(match_analysis($result[$i]['description'])){
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
  }
  print_r($seconds);
  //Fill in csv file
  $file = fopen("$module_name[$module_id] In Book $book_id Analysis.csv",'w');
  //Adding headers
  fputcsv($file, array("User_ID","Difference In Seconds" ,"Module Name","Book_ID"));
  foreach($seconds as $line){
    fputcsv($file, $line);
  }

  echo 'Data Written to file Successfuly';
}


function match_analysis($str){
  if (strpos($str, "Show") !== false && strpos($str, "Analysis")!== false && 
              strpos($str, "Discussion")!== false){
    return true;
  }
  else{
    return false;
  }
}
function search_assoc($arr, $target){
  for($i=0;$i<count($arr);$i++){
    foreach($arr[$i] as $key=>$value){
	  if($value==$target)
	    return true;
	}
  }
  return false;
}
