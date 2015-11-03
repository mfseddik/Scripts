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
                AND (name ='document-ready' OR name = 'window-unload')
                ORDER BY user_id, action_time, id";
//}
$result = array();
if(@mysql_connect($db_server, $db_username, $db_pass)){
	  if(@mysql_select_db($db_name)){
	    if($query_run = mysql_query($query)){
		  while($query_result = mysql_fetch_assoc($query_run)){
			array_push($result, $query_result); 
		  } 
		}
	}
}

//print_r($result);


//Finding module sessions for each user and then pass this to calculate_time to get the max
$sessions = array();
$session_start = false;
$current_user = null;
$module_load = null;
for($i = 0;$i < count($result); $i++){
  if($result[$i]['name'] == 'document-ready' && $session_start == false){
    //Start of a session
    $current_user = $result[$i]["user_id"];
    $module_load = $result[$i];
    $session_start = true;
  }
  else if ($result[$i]['name'] == 'document-ready' && $session_start == true){
    //subsequent document-readys without matching window-unload
    if($result[$i]["user_id"] != $current_user){
      $current_user = $result[$i]["user_id"];
      $module_load = $result[$i];
    }
  }
  else if ($result[$i]['name'] == 'window-unload' && $session_start == true){
    //window-unload matching a document ready
    if($result[$i]['user_id'] == $current_user){
      array_push($sessions, $module_load);  
      array_push($sessions, $result[$i]);  
    }
    $session_start = false; 
  }
}

print_r($sessions);

calculate_time($sessions, $module_id, $book_id);

 
//////////////////////////////////////////////////////////////////////////////////////////////

function calculate_time($result, $module_id, $book_id){
  //Calculating the baseline data points
  $maxDiff = 0;
  $seconds = array();
  for($i = 0;$i < count($result); $i += 2){
 
    $dateDiff = strtotime($result[$i+1]['action_time']) - strtotime($result[$i]['action_time']);
	  //echo $dateDiff."     ";
	  if($dateDiff > $maxDiff && $dateDiff <= 7200){
	    $maxDiff = $dateDiff;
	  }
	  
    if($i < count($result)-2){
      if($result[$i+2]['user_id'] != $result[$i]['user_id']){
        //Add maxDiff to the list
        array_push($seconds, array($result[$i]['user_id'],$maxDiff, $module_id, $book_id));
        $maxDiff = 0;
      }
    }
    else{
      //Adding the last one
      array_push($seconds, array($result[$i]['user_id'],$maxDiff, $module_id, $book_id));
    
	   }
    
  }
  print_r($seconds);
  
  //Fill in csv file
  // $file = fopen("Module $module_id In Book $book_id Analysis.csv",'w');
  // //Adding headers
  // fputcsv($file, array("User_ID","Difference In Seconds" ,"Module Name","Book_ID"));
  // foreach($seconds as $line){
  //   fputcsv($file, $line);
  // }

  // echo "Data Written to file Successfuly\n";
}
