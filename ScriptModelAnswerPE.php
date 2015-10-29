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

//Get those exercises in which the model answer box was opened
$query = "SELECT distinct B.id
          FROM  `opendsa_userbutton` A INNER JOIN `opendsa_exercise` B ON A.exercise_id = B.id   
          WHERE B.ex_type = 'pe' AND A.user_id NOT 
          IN ( 1, 2, 3, 5, 276, 8, 12 ) 
          AND A.book_id =5
          AND A.action_time
          BETWEEN  '2014-08-26' AND '2014-12-20'
          AND A.name = 'jsav-exercise-model-open'
          AND  '2014-12-20'group by B.id";


$exercises = array();
if(@mysql_connect($db_server, $db_username, $db_pass)){
	  if(@mysql_select_db($db_name)){
	    if($query_run = mysql_query($query)){
		  while($query_result = mysql_fetch_assoc($query_run)){
			array_push($exercises, $query_result); 
		  } 
		}
	}
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Loop through all the exercises to find whether the model answer box was opened before starting the exercise
// $final_result = array();
// $user_finished = array();
// for ($i = 0; $i < count($exercises) ; $i++) { 
//   $ex = $exercises[$i]["id"];
//   $query = "SELECT MIN(action_time), user_id, name
//             FROM  `opendsa_userbutton` 
//             WHERE user_id
//             IN ( 
// 				SELECT user_id
// 				FROM  `opendsa_userbook` 
// 				WHERE grade =1) 
//             AND book_id = 5
//             AND exercise_id = $ex
//             AND action_time BETWEEN  '2014-08-26' AND  '2014-12-20'
//             AND (name ='jsav-exercise-model-open' OR name ='jsav-exercise-gradeable-step') 
//             group by user_id, name 
//             ORDER BY user_id, MIN(action_time)";

//   $user_count = 0;
//   unset($user_finished);
//   $user_finished = array();
//   if(@mysql_connect($db_server, $db_username, $db_pass)){
//     if(@mysql_select_db($db_name)){
//       if($query_run = mysql_query($query)){
//         while($query_result = mysql_fetch_assoc($query_run)){
//           $user_id = $query_result["user_id"];
//           $name = $query_result["name"];
//           if(!in_array($user_id, $user_finished)){
//             array_push($user_finished, $user_id);
//             if ($name == "jsav-exercise-model-open"){
//               $user_count++;
//             }
//           }
//         } 
//       }
//     }
//   }
//   array_push($final_result, array($ex => $user_count));
// }
// print_r($final_result);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////
//Loop through all the exercises to find whether the model answer box was opened before or after proficiency but after an interaction

// $final_result_before = array();
// $final_result_after = array();
// for ($j = 0; $j < count($exercises) ; $j++) { 
//   $ex = $exercises[$j]["id"];
//   $data = array();
//   $query = "SELECT MIN(action_time) AS min_action_time, user_id, name
//             FROM  `opendsa_userbutton` 
//             WHERE user_id 
//             IN ( SELECT user_id
// 				    FROM  `opendsa_userbook` 
// 				    WHERE grade =1 ) 
//             AND book_id = 5
//             AND exercise_id = $ex
//             AND action_time BETWEEN  '2014-08-26' AND  '2014-12-20'
//             AND (name ='jsav-exercise-model-open' OR name ='jsav-exercise-gradeable-step') 
//             group by user_id, name 
//             ORDER BY user_id, MIN(action_time)";

//   $user_count_before = 0;
//   $user_count_after = 0;
//   if(@mysql_connect($db_server, $db_username, $db_pass)){
//     if(@mysql_select_db($db_name)){
//       if($query_run = mysql_query($query)){
//         while($query_result = mysql_fetch_assoc($query_run)){
// 		      array_push($data, $query_result);
//         } 
//       }
//       for($i = 0; $i < count($data)-1; $i++) {
//          if(($data[$i]["user_id"] == $data[$i+1]["user_id"]) && 
//           ($data[$i]["name"] == "jsav-exercise-gradeable-step" && $data[$i+1]["name"] 
//           == "jsav-exercise-model-open")){
//            //Now let's see whether the student opens the model-box before or after proficiency
//            $user_id = $data[$i]['user_id']; 
//            $query_prof = "SELECT `proficient_date` 
//                           FROM `opendsa_userexercise` 
//                           where user_id  = $user_id
//                           AND exercise_id = $ex";

//            if($query_run_prof = mysql_query($query_prof)){
//               $date = mysql_fetch_row($query_run_prof);
//               $date_model = strtotime($data[$i+1]["min_action_time"]);
//               $date_prof = strtotime($date[0]);

//               if($date_model > $date_prof){
//                $user_count_after++;

//               }
//               else{
//                $user_count_before++;
//               }
//            }
//         }
//       }
//     }
//   }
//   array_push($final_result_before, array($ex=>$user_count_before));
//   array_push($final_result_after, array($ex=>$user_count_after));
// }
// print_r($final_result_before);
// print_r($final_result_after);
//////////////////////////////////////////////////////////////////////////////////////////////

//Loop through all the exercises to see for each exercise the number of students who made any interaction with
//the exercise post-proficiency

//We can also find the number of students who got another proficiency

$user_another_interact = array();
$user_another_pro = array();
for ($j = 0; $j < count($exercises) ; $j++) { 
  $ex = $exercises[$j]["id"];
  $query = "SELECT count(DISTINCT A.user_id) AS user_count
            FROM  `opendsa_userbutton` A INNER JOIN `opendsa_userexercise` B 
            ON A.user_id = B.user_id AND A.exercise_id = B.exercise_id
            WHERE A.user_id 
            IN ( SELECT user_id
            FROM  `opendsa_userbook` 
            WHERE grade =1 AND book_id = 5) 
            AND A.exercise_id = $ex
            AND action_time BETWEEN  '2014-08-26' AND  '2014-12-20'
            AND action_time > B.proficient_date
            AND A.name ='jsav-exercise-gradeable-step'";

  $query2 = "SELECT count(DISTINCT user_id) AS user_count
             FROM `opendsa_userexerciselog`
             WHERE user_id 
             IN ( SELECT user_id
             FROM  `opendsa_userbook` 
             WHERE grade =1 AND book_id = 5) 
             AND exercise_id = $ex
             AND earned_proficiency > 1";

  $user_count_interact = 0;
  $user_count_pro = 0;
  if(@mysql_connect($db_server, $db_username, $db_pass)){
    if(@mysql_select_db($db_name)){
      if($query_run = mysql_query($query)){
        if($query_result = mysql_fetch_assoc($query_run)){
          array_push($user_another_interact, array($ex => $query_result["user_count"]));
        } 
      }
      //Second query here
      if($query_run2 = mysql_query($query2)){
        if($query_result2 = mysql_fetch_assoc($query_run2)){
          array_push($user_another_pro, array($ex => $query_result2["user_count"]));
        } 
      }
    
    }
  }
}
print_r($user_another_interact);
print_r($user_another_pro);

?>