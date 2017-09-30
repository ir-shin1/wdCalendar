<?php
include_once("dbconfig.php");
include_once("functions.php");
include_once("mattermost_bot.php");

function addCalendar($st, $et, $sub, $ade){
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "insert into `jqcalendar` (`subject`, `starttime`, `endtime`, `isalldayevent`) values ('"
      .$db->escapeString($sub)."', '"
      .php2MySqlTime(js2PhpTime($st))."', '"
      .php2MySqlTime(js2PhpTime($et))."', '"
      .$db->escapeString($ade)."' )";
    // echo($sql);
    if($db->query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = $db->getMessage();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'add success';
      $ret['Data'] = $db->insertId();
      mattermostBotPost($st, $sub, "add");
    }
    $db->disConnection();
  }catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}


function addDetailedCalendar($st, $et, $sub, $ade, $dscr, $loc, $color, $tz){
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "insert into `jqcalendar` (`subject`, `starttime`, `endtime`, `isalldayevent`, `description`, `location`, `color`) values ('"
      .$db->escapeString($sub)."', '"
      .php2MySqlTime(js2PhpTime($st))."', '"
      .php2MySqlTime(js2PhpTime($et))."', '"
      .$db->escapeString($ade)."', '"
      .$db->escapeString($dscr)."', '"
      .$db->escapeString($loc)."', '"
      .$db->escapeString($color)."' )";
    // echo($sql);
    if($db->query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = $db->getMessage();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'add success';
      $ret['Data'] = $db->insertId();
      mattermostBotPost($st, $sub, "add");
    }
    $db->disConnection();
  }catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}

function listCalendarByRange($sd, $ed){
  $ret = array();
  $ret['events'] = array();
  $ret["issort"] =true;
  $ret["start"] = php2JsTime($sd);
  $ret["end"] = php2JsTime($ed);
  $ret['error'] = null;
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "select * from `jqcalendar` where `starttime` between '"
      .php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."' ORDER BY starttime ASC";
    $handle = $db->query($sql);
    // echo($sql);
    while ($row = $db->fetchObject($handle)) {
      //$ret['events'][] = $row;
      //$attends = $row->AttendeeNames;
      //if($row->OtherAttendee){
      //  $attends .= $row->OtherAttendee;
      //}
      //echo $row->StartTime;
      $ret['events'][] = array(
        $row->Id,
        $row->Subject,
        php2JsTime(mySql2PhpTime($row->StartTime)),
        php2JsTime(mySql2PhpTime($row->EndTime)),
        $row->IsAllDayEvent,
        ($row->IsAllDayEvent!=1 && date("Y-m-d",mySql2PhpTime($row->EndTime))>date("Y-m-d",mySql2PhpTime($row->StartTime))?1:0), //more than one day event
        //$row->InstanceType,
        0,//Recurring event,
        $row->Color,
        ($row->Holiday==1)?0:1,//editable
        $row->Location, 
        '',//$attends
        $row->Holiday
      );
    }
  }catch(Exception $e){
     $ret['error'] = $e->getMessage();
  }
  return $ret;
}

function listCalendar($day, $type){
  $phpTime = js2PhpTime($day);
  //echo $phpTime . "+" . $type;
  switch($type){
    case "month":
      $st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
      $et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));
      break;
    case "week":
      //suppose first day of a week is monday 
      $monday  =  date("d", $phpTime) - date('N', $phpTime) + 1;
      //echo date('N', $phpTime);
      $st = mktime(0,0,0,date("m", $phpTime), $monday, date("Y", $phpTime));
      $et = mktime(0,0,-1,date("m", $phpTime), $monday+7, date("Y", $phpTime));
      break;
    case "day":
      $st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
      $et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime)+1, date("Y", $phpTime));
      break;
  }
  //echo $st . "--" . $et;
  return listCalendarByRange($st, $et);
}

function updateCalendar($id, $st, $et){
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();

    $sql = "select * from `jqcalendar` where `id`=" . $id;
    $handle = $db->query($sql);
    // echo($sql);
    while ($row = $db->fetchObject($handle)) {
      $sub=$row->Subject;
    }

    $sql = "update `jqcalendar` set"
      . " `starttime`='" . php2MySqlTime(js2PhpTime($st)) . "', "
      . " `endtime`='" . php2MySqlTime(js2PhpTime($et)) . "' "
      . "where `id`=" . $id;
    // echo($sql);
    if($db->query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = $db->getMessage();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'Succefully';
      mattermostBotPost($st, $sub, "update");
    }
  }catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}

function updateDetailedCalendar($id, $st, $et, $sub, $ade, $dscr, $loc, $color, $tz){
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "update `jqcalendar` set"
      . " `starttime`='" . php2MySqlTime(js2PhpTime($st)) . "', "
      . " `endtime`='" . php2MySqlTime(js2PhpTime($et)) . "', "
      . " `subject`='" . $db->escapeString($sub) . "', "
      . " `isalldayevent`='" . $db->escapeString($ade) . "', "
      . " `description`='" . $db->escapeString($dscr) . "', "
      . " `location`='" . $db->escapeString($loc) . "', "
      . " `color`='" . $db->escapeString($color) . "' "
      . "where `id`=" . $id;
    // echo($sql);
    if($db->query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = $db->getMessage();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'Succefully';
      mattermostBotPost($st, $sub, "update");
    }
  }catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}

function removeCalendar($id){
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();

    $sql = "select * from `jqcalendar` where `id`=" . $id;
    $handle = $db->query($sql);
    // echo($sql);
    while ($row = $db->fetchObject($handle)) {
      $st=php2JsTime(mySql2PhpTime($row->StartTime));
      $sub=$row->Subject;
    }

    $sql = "delete from `jqcalendar` where `id`=" . $id;
    // echo($sql);
    if($db->query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = $db->getMessage();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'Succefully';
      mattermostBotPost($st, $sub, "remove");
    }
  }catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
}




header('Content-type:text/javascript;charset=UTF-8');
$method = $_GET["method"];
switch ($method) {
    case "add":
        $ret = addCalendar($_POST["CalendarStartTime"], $_POST["CalendarEndTime"], $_POST["CalendarTitle"], $_POST["IsAllDayEvent"]);
        break;
    case "list":
        $ret = listCalendar($_POST["showdate"], $_POST["viewtype"]);
        break;
    case "update":
        $ret = updateCalendar($_POST["calendarId"], $_POST["CalendarStartTime"], $_POST["CalendarEndTime"]);
        break; 
    case "remove":
        $ret = removeCalendar( $_POST["calendarId"]);
        break;
    case "adddetails":
        $st = $_POST["stpartdate"] . " " . $_POST["stparttime"];
        $et = $_POST["etpartdate"] . " " . $_POST["etparttime"];
        if(isset($_GET["id"])){
            $ret = updateDetailedCalendar($_GET["id"], $st, $et, 
                $_POST["Subject"], isset($_POST["IsAllDayEvent"])?1:0, $_POST["Description"], 
                $_POST["Location"], $_POST["colorvalue"], $_POST["timezone"]);
        }else{
            $ret = addDetailedCalendar($st, $et,                    
                $_POST["Subject"], isset($_POST["IsAllDayEvent"])?1:0, $_POST["Description"], 
                $_POST["Location"], $_POST["colorvalue"], $_POST["timezone"]);
        }        
        break; 


}
echo json_encode($ret); 



?>
