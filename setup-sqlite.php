<?php

  $sql = "CREATE TABLE `jqcalendar` (" .
         " `Id`            INTEGER       PRIMARY KEY AUTOINCREMENT," .
         " `Subject`       varchar(1000) default NULL," .
         " `Location`      varchar(200)  default NULL," .
         " `Description`   varchar(255)  default NULL," .
         " `StartTime`     datetime      default NULL," .
         " `EndTime`       datetime      default NULL," .
         " `IsAllDayEvent` smallint(6)   NOT NULL," .
         " `Color`         varchar(200)  default NULL," .
         " `Holiday`       smallint(6)   default 0," .
         " `RecurringRule` varchar(500)  default NULL" .
         ");";

  $db = new SQLite3('/var/lib/db/wdCalendar.db');

  $db->query( $sql );

  $db->close();

?>
