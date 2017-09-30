<?php
function mattermostBotPost( $st, $sub, $op ) {
  $url = "";  // http://<mattermost のIncoming Webhooks URL
  $username = "闇の番人";

  if ( $url == "" ) {
    return;
  }

  $day = date("Y年m月d日", js2PhpTime( $st ));
  if ( $op == "add" ) {
    $content = '{ "username" : $username , "text" : "' . $day . 'にスケジュールを追加 : ' . $sub . '"}';
  } else if ( $op == "update" ) {
    $content = '{ "username" : $username , "text" : "' . $day . 'のスケジュールを更新 : ' . $sub . '"}';
  } else {
    $content = '{ "username" : $username , "text" : "' . $day . 'のスケジュールを削除 : ' . $sub . '"}';
  }

  $header = array(
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: ' . strlen($content),
    'Connection: Close'
  );
  $options = array('http' =>
    array(
      'protocol_version' => '1.1',
      'method' => 'POST',
      'header'  => implode('\r\n', $header),
      'content' => $content
    )
  );
 
  file_get_contents($url, false, stream_context_create($options));
}
?>
