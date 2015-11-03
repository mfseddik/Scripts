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

//Get all open dialog box events forall users for all pe exercises in a book

$query = 

  "SELECT * from opendsa_userbutton A where 
  A.user_id IN 
 (Select user_id from `opendsa_userbook` where grade = 1 AND book_id = $book_id) 
  AND 
  user_id NOT IN (Select user_id from `opendsa_userbook` where grade = 0) 
  AND book_id = $book_id AND A.exercise_id IN
 (SELECT B.id from opendsa_exercise B 
  inner join opendsa_bookmoduleexercise C 
  ON B.id = C.exercise_id where B.ex_type = 'pe' 
  AND C.book_id = $book_id) AND A.name = 'jsav-exercise-model-open' 
  AND A.action_time BETWEEN '$startDate' AND '$endDate' 
  ORDER BY A.user_id, A.exercise_id, A.action_time, A.id";


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

//print_r($data);

//Now we get the proficiecny Date of each user on each exercise and store it
$prof_time_data = array();

for($i=0;$i<count($data);$i++){
  $user_id = $data[$i]['user_id'];
  $ex_id = $data[$i]['exercise_id'];
  if(find($prof_time_data, $user_id, $ex_id) == -1){
    $query = 
             "SELECT MIN(time_done) AS prof_time from opendsa_userexerciselog where
              user_id = $user_id AND exercise_id = $ex_id AND 
              time_done BETWEEN '$startDate' AND '$endDate' 
              AND earned_proficiency = 1";


  $time = array();
  if(@mysql_connect($db_server, $db_username, $db_pass)){
    if(@mysql_select_db($db_name)){
      if($query_run = mysql_query($query)){
        while($query_result = mysql_fetch_assoc($query_run)){
          array_push($prof_time_data, 
            array($user_id, $ex_id, $query_result['prof_time'])); 
        } 
      }
    }
  }
 }
}

//print_r($prof_time_data);

//Now see how many times the dialog was opened before or after proficiency

$before = 0;
$after = 0;
$result = array();
$prev_user_id = $data[0]["user_id"];
$prev_ex_id = $data[0]["exercise_id"];
for($i=0;$i<count($data);$i++){
  $user_id = $data[$i]['user_id'];
  $ex_id = $data[$i]['exercise_id'];
  $action_time = $data[$i]["action_time"];
  $prof_time = find($prof_time_data, $user_id, $ex_id);
  if($user_id != $prev_user_id || $ex_id != $prev_ex_id){
    array_push($result, 
      array("user_id"=>$prev_user_id, "Exercise_Id"=>$prev_ex_id, "Before"=>$before, "After"=>$after));
    $prev_user_id = $user_id;
    $prev_ex_id = $ex_id;
    $before = 0;
    $after = 0;
  }
  if($prof_time != -1){
    if($action_time <= $prof_time){
      $before++;
    }
    else{
      $after++;
    }
  }
  //Adding last thing
  if($i == count($data)-1){
    array_push($result, 
      array("user_id"=>$user_id, "Exercise_Id"=>$ex_id, "Before"=>$before, "After"=>$after));
  }
}

print_r($result);

//Fill in csv file
$file = fopen("PE Analysis Book $book_id.csv",'w');

//Adding header
fputcsv($file, 
array("User_ID", "Exercise_Id","Open Before Proficiency", "Open After Proficiency"));
foreach($result as $line){
  fputcsv($file, $line);
}
echo "Data Written Successfuly to File\n";


////////////////////////////////////////////////////////////////////
function find($arr, $target1, $target2){
  for($i=0;$i<count($arr);$i++){
    if($arr[$i][0]==$target1 && $arr[$i][1] == $target2)
      return $arr[$i][2];
  }
  return -1;
}

?>