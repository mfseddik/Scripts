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

$book_id = $argv[1];
$startDate  = $argv[2];
$endDate = $argv[3];

// //Get the Total_Attempts, Total_Incorrect, and Total_Hints for each KA in By Exercise

// $query = "SELECT exercise_id, name, user_id, correct, count_hints, count_attempts 
          // FROM `opendsa_userexerciselog` A INNER JOIN `opendsa_exercise` B ON A.exercise_id = B.id
           // where user_id IN 
              // (Select user_id from `opendsa_userbook` where grade = 1 AND book_id = $book_id) AND user_id NOT IN (Select user_id from `opendsa_userbook` where grade = 0) 
           // AND exercise_id IN 
              // (Select C.id from `opendsa_exercise` C INNER JOIN `opendsa_bookmoduleexercise`D ON C.id = D.exercise_id where ex_type = 'ka' AND book_id = $book_id) 
          // AND time_done BETWEEN '$startDate' AND  '$endDate' 
          // ORDER BY exercise_id, user_id, time_done";

// $data = array();
// if(@mysql_connect($db_server, $db_username, $db_pass)){
	  // if(@mysql_select_db($db_name)){
	    // if($query_run = mysql_query($query)){
		  // while($query_result = mysql_fetch_assoc($query_run)){
		    // array_push($data, $query_result); 
		  // } 
		// }
	// }
// }

// $count_hints = 0;
// $count_correct = 0;
// $count_attempts = 0;
// $result = array();

// for($i = 0; $i < count($data)-1; $i++){
  // $exercise_id = $data[$i]["exercise_id"];
  // $hints = $data[$i]["count_hints"];
  // $attempts = $data[$i]["count_attempts"];
  // $correct = $data[$i]["correct"];
  // $exercise_name = $data[$i]["name"];
  // $user_id = $data[$i]["user_id"];

  // $next_exercise_id = $data[$i + 1]["exercise_id"];
  // $next_hints = $data[$i + 1]["count_hints"];
  // $next_attempts = $data[$i + 1]["count_attempts"];
  // $next_user_id = $data[$i + 1]["user_id"];

  // if($exercise_id == $next_exercise_id){
    // if($hints == 1 && $attempts == 0){
      // $count_hints ++;
    // }
    // else if ($hints == 0 && $attempts == 1){
      // $count_attempts++;
    // }
    // if(($next_hints > $hints) && ($user_id == $next_user_id)){
      // $count_hints++;
    // }
    // if(($next_attempts > $attempts) && ($user_id == $next_user_id)){
      // $count_attempts++;
    // }
    // if($correct == 1){
      // $count_correct ++;
    // }
  // }
  // else{
    // if($correct == 1){
      // $count_correct ++; 
    // }
	// if($hints == 1 && $attempts == 0){
      // $count_hints ++;
    // }
    // else if ($hints == 0 && $attempts == 1){
      // $count_attempts++;
    // }
    // array_push($result, 
      // array("exercise_id"=>$exercise_id, "exercise_name"=>$exercise_name, "count_attempts"=>$count_attempts, "count_hints"=>$count_hints, "count_incorrect"=>$count_attempts-$count_correct));
    // $count_hints = 0;
    // $count_attempts = 0;
    // $count_correct = 0;
  // }
// }

// //Add the last exercise
// if($data[$i]["correct"] == 1){
  // $count_correct ++; 
// }
// if($data[$i]["count_hints"] == 1 && $data[$i]["count_attempts"] == 0){
  // $count_hints ++;
// }
// else if ($data[$i]["count_hints"] == 1 && $data[$i]["count_attempts"] == 1){
  // $count_attempts++;
// }
// array_push($result, array("exercise_id"=>$exercise_id, "exercise_name"=>$exercise_name,"count_attempts"=>$count_attempts, "count_hints"=>$count_hints, "count_incorrect"=>$count_attempts-$count_correct));
// print_r($result);

// //Fill in csv file
// $file = fopen("Attempts_Hints_EX_$book_id.csv",'w');

// //Adding headers
// fputcsv($file, array("Exercise_ID","Exercise_Name", "Total_Attempts","Total_Hints", "Total_Incorrect"));
// foreach($result as $line){
  // fputcsv($file, $line);
// }


//Get the Total_Attempts, Total_Incorrect, and Total_Hints for a single KA By User

$ex_id = $argv[4];


$query = "SELECT id, user_id, correct, count_hints, count_attempts 
          FROM `opendsa_userexerciselog` where user_id IN 
          (Select user_id from `opendsa_userbook` where grade = 1 AND book_id = $book_id) 
          AND user_id NOT IN (Select user_id from `opendsa_userbook` where grade = 0)
          AND exercise_id IN 
          ($ex_id) 
          AND time_done BETWEEN '$startDate' AND '$endDate'
          ORDER BY user_id, time_done, id";

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
$count_hints = 0;
$count_correct = 0;
$count_attempts = 0;
$total = 0;
$test = 0;
$result = array();

for($i = 0; $i < count($data)-1; $i++){

  $user_id = $data[$i]["user_id"];
  $hints = $data[$i]["count_hints"];
  $attempts = $data[$i]["count_attempts"];
  $correct = $data[$i]["correct"];
  
  $next_user_id = $data[$i + 1]["user_id"];
  $next_hints = $data[$i + 1]["count_hints"];
  $next_attempts = $data[$i + 1]["count_attempts"];
  

  if($user_id == $next_user_id){
    $total++;
    if($hints == 1 && $attempts == 0){
      $count_hints++;
    }
	if(($next_hints > $hints) && ($attempts == $next_attempts)){ //0 1
	                                                         //1 0
      $count_hints++;
    }
	if ($hints == 0 && $attempts == 1){
      $count_attempts++;
      //echo "At".$data[$i]["id"].".. 0  1 -> $count_attempts\n";	  
    }
	if(($next_attempts > $attempts) && ($hints == $next_hints)){
      $count_attempts++;
	  //echo "At".$data[$i]["id"].".. next -> $count_attempts ---> $attempts AND $next_attempts\n";
    }
    if($correct == 1){
      $count_correct++;
    }
  }
  else{
	if($hints == 1 && $attempts == 0){
      $count_hints++;
	  
    }
    if ($hints == 0 && $attempts == 1){
      $count_attempts++;
	  
    }
	if($correct == 1){
      $count_correct++;
    }
	$total++;
    array_push($result, 
      array("user_id"=>$user_id, "count_attempts"=>$total-$count_hints, "count_hints"=>$count_hints, "count_incorrect"=>$count_attempts-$count_correct));   
	
    $count_hints = 0;
    $count_attempts = 0;
    $count_correct = 0;
	$total=0;
  }
}

//Add the last User
if($data[$i]["correct"] == 1){
  $count_correct++; 
}
if($data[$i]["count_hints"] == 1 && $data[$i]["count_attempts"] == 0){
  $count_hints++;
}
if ($data[$i]["count_hints"] == 0 && $data[$i]["count_attempts"] == 1){
  $count_attempts++;
}
$total++;
array_push($result, array("user_id"=>$user_id,"count_attempts"=>$total-$count_hints, "count_hints"=>$count_hints, "count_incorrect"=>$count_attempts-$count_correct));
print_r($result);
//Fill in csv file
$file = fopen("Attempts_Hints_Users Exercise $ex_id Book $book_id.csv",'w');

//Adding header
fputcsv($file, 
array("User_ID", "Total_Attempts","Total_Hints", "Total_Incorrect"));
foreach($result as $line){
  fputcsv($file, $line);
}





// //Get the Total_Attempts, Total_Incorrect, and Total_Hints for each KA in CS3114F14 By User

// $query = "SELECT user_id, exercise_id, correct, count_hints, count_attempts 
          // FROM `opendsa_userexerciselog` where user_id IN 
          // (Select user_id from `opendsa_userbook` where grade = 1 AND book_id = 5) 
          // AND exercise_id IN 
          // (Select C.id from `opendsa_exercise` C INNER JOIN `opendsa_bookmoduleexercise` D ON C.id = D.exercise_id where ex_type = 'ka' AND book_id = 5) 
          // AND time_done BETWEEN '2014-08-26' AND  '2014-12-20' 
          // ORDER BY user_id, exercise_id, time_done";

// $data = array();
// if(@mysql_connect($db_server, $db_username, $db_pass)){
    // if(@mysql_select_db($db_name)){
      // if($query_run = mysql_query($query)){
      // while($query_result = mysql_fetch_assoc($query_run)){
        // array_push($data, $query_result); 
      // } 
    // }
  // }
// }

// $count_hints = 0;
// $count_correct = 0;
// $count_attempts = 0;
// $result = array();

// for($i = 0; $i < count($data)-1; $i++){
  // $user_id = $data[$i]["user_id"];
  // $hints = $data[$i]["count_hints"];
  // $attempts = $data[$i]["count_attempts"];
  // $correct = $data[$i]["correct"];
  // $exercise_id = $data[$i]["exercise_id"];

  // $next_user_id = $data[$i + 1]["user_id"];
  // $next_hints = $data[$i + 1]["count_hints"];
  // $next_attempts = $data[$i + 1]["count_attempts"];
  // $next_exercise_id = $data[$i + 1]["exercise_id"];

  // if($user_id == $next_user_id){
    // if($hints == 1 && $attempts == 0){
      // $count_hints ++;
    // }
    // else if ($hints == 0 && $attempts == 1){
      // $count_attempts++;
    // }
    // if(($next_hints > $hints) && ($exercise_id == $next_exercise_id)){
      // $count_hints++;
    // }
    // if(($next_attempts > $attempts) && ($exercise_id == $next_exercise_id)) {
      // $count_attempts++;
    // }
    // if($correct == 1){
      // $count_correct ++;
    // }
  // }
  // else{
    // if($correct == 1){
      // $count_correct ++; 
    // }
    // array_push($result, 
      // array("user_id"=>$user_id, "count_attempts"=>$count_attempts, "count_hints"=>$count_hints, "count_incorrect"=>$count_attempts-$count_correct));
    // $count_hints = 0;
    // $count_attempts = 0;
    // $count_correct = 0;
  // }
// }

// //Add the last exercise
// if($data[$i]["correct"] == 1){
  // $count_correct ++; 
// }
// array_push($result, array("user_id"=>$user_id,"count_attempts"=>$count_attempts, "count_hints"=>$count_hints, "count_incorrect"=>$count_attempts-$count_correct));
// print_r($result);

// //Fill in csv file
// $file = fopen("Attempts_Hints_USER.csv",'w');

// //Adding headers
// fputcsv($file, array("User_ID", "Total_Attempts","Total_Hints", "Total_Incorrect"));
// foreach($result as $line){
  // fputcsv($file, $line);
// }


//Get the number of students who's hint ratio exceeds a certian amount and incorrect ratio exceeds a certian amount for all exercises

// $query = "SELECT exercise_id, name, user_id, correct, count_hints, count_attempts 
          // FROM `opendsa_userexerciselog` A INNER JOIN `opendsa_exercise` B ON A.exercise_id = B.id
          // where user_id IN 
          // (Select user_id from `opendsa_userbook` where grade = 1 AND book_id = 5) 
          // AND exercise_id IN 
          // (Select C.id from `opendsa_exercise` C INNER JOIN `opendsa_bookmoduleexercise` D ON C.id = D.exercise_id where ex_type = 'ka' AND book_id = 5) 
          // AND time_done BETWEEN '2014-08-26' AND  '2014-12-20' 
          // ORDER BY exercise_id, user_id, time_done";

// $data = array();
// if(@mysql_connect($db_server, $db_username, $db_pass)){
	  // if(@mysql_select_db($db_name)){
	    // if($query_run = mysql_query($query)){
		  // while($query_result = mysql_fetch_assoc($query_run)){
		    // array_push($data, $query_result); 
		  // } 
		// }
	// }
// }

// $hint_threshold = $argv[1];
// $incorrect_threshold = $argv[2];
// $count_students = 0;

// $count_hints = 0;
// $count_correct = 0;
// $count_attempts = 0;

// $result = array();

// for($i = 0; $i < count($data)-1; $i++){
  // $exercise_id = $data[$i]["exercise_id"];
  // $hints = $data[$i]["count_hints"];
  // $attempts = $data[$i]["count_attempts"];
  // $correct = $data[$i]["correct"];
  // $exercise_name = $data[$i]["name"];
  // $user_id = $data[$i]["user_id"];

  // $next_exercise_id = $data[$i + 1]["exercise_id"];
  // $next_hints = $data[$i + 1]["count_hints"];
  // $next_attempts = $data[$i + 1]["count_attempts"];
  // $next_user_id = $data[$i + 1]["user_id"];

  // if($exercise_id == $next_exercise_id){
    // if($user_id == $next_user_id){
      // if($hints == 1 && $attempts == 0){
        // $count_hints ++;
      // }
      // else if ($hints == 0 && $attempts == 1){
        // $count_attempts++;
      // }
      // if($next_hints > $hints){
        // $count_hints++;
      // }
      // if($next_attempts > $attempts){
        // $count_attempts++;
      // }
      // if($correct == 1){
        // $count_correct ++;
      // }
	// }
	// else{
	  // if($correct == 1){
        // $count_correct ++; 
      // }
	  // $count_incorrect = $count_attempts - $count_correct;
	  // if($count_attempts != 0){
	    // $incorrect_ratio = (float)$count_incorrect / (float)$count_attempts;
		// $hint_ratio = (float)$count_hints / (float)$count_attempts;
	  // }
	  // if($hint_ratio >= $hint_threshold && $incorrect_ratio >= $incorrect_threshold){
	    // $count_students++;  
	  // }
	  // $count_correct = 0;
	  // $count_hints = 0;
	  // $count_attempts = 0;
	// }
  // }
  // else{
    // array_push($result, 
      // array("exercise_id"=>$exercise_id, "exercise_name"=>$exercise_name, "count_students"=>$count_students));
    // $count_hints = 0;
    // $count_attempts = 0;
    // $count_correct = 0;
	// $count_students = 0;
  // }
// }

// //Add the last exercise
// if($data[$i]["correct"] == 1){
  // $count_correct ++; 
// }
// array_push($result, array("exercise_id"=>$exercise_id, "exercise_name"=>$exercise_name,"count_students"=>$count_students));
// print_r($result);

// //Fill in csv file
// $file = fopen("Count_Students_Exceed_IncorrectHintRatio.csv",'w');

// //Adding headers
// fputcsv($file, array("Exercise_ID","Exercise_Name", "Count_Students"));
// foreach($result as $line){
  // fputcsv($file, $line);
// }

?>