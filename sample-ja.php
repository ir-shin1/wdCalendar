<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1">
    <title>	カレンダー </title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link href="css/dailog.css" rel="stylesheet" type="text/css" />
    <link href="css/calendar.css" rel="stylesheet" type="text/css" />
    <link href="css/dp.css" rel="stylesheet" type="text/css" />
    <link href="css/alert.css" rel="stylesheet" type="text/css" />
    <link href="css/main.css" rel="stylesheet" type="text/css" />


    <script src="src/jquery.js" type="text/javascript"></script>

    <script src="src/Plugins/Common.js" type="text/javascript"></script>
    <script src="src/Plugins/datepicker_lang_JA.js" type="text/javascript"></script>
    <script src="src/Plugins/jquery.datepicker.js" type="text/javascript"></script>

    <script src="src/Plugins/jquery.alert.js" type="text/javascript"></script>
    <script src="src/Plugins/jquery.ifrmdailog.js" defer="defer" type="text/javascript"></script>
    <script src="src/Plugins/wdCalendar_lang_JA.js" type="text/javascript"></script>
    <script src="src/Plugins/jquery.calendar.js" type="text/javascript"></script>

    <script type="text/javascript">
        $(document).ready(function() {
           var view="month";          // カレンダーの種類（初期表示）

            var DATA_FEED_URL = "php/datafeed.php";
            var op = {
                view: view,                          // カレンダーの種類（初期表示）
                theme:3,                             // 色
                showday: new Date(),                 // 初期表示日
                DeleteCmdhandler:Delete,             // 削除処理関数名
                ViewCmdhandler:View,                 // 表示処理関数名
                onWeekOrMonthToDay:wtd,              // ???週日月切り替え時使用???
                onBeforeRequestData: cal_beforerequest,  // Ajaxイベント送信中処理
                onAfterRequestData: cal_afterrequest,    // Ajaxイベント完了後処理
                onRequestDataError: cal_onerror,         // Ajaxイベント送信前処理
                autoload:true,                       // 予定初期表示
                url: DATA_FEED_URL + "?method=list",     // 表示処理の処理URL
                quickAddUrl: DATA_FEED_URL + "?method=add",         // 追加の処理URL
                quickUpdateUrl: DATA_FEED_URL + "?method=update",   // 更新の処理URL
                quickDeleteUrl: DATA_FEED_URL + "?method=remove"    // 削除の処理URL
            };
            var $dv = $("#calhead");
            var _MH = document.documentElement.clientHeight;
            var dvH = $dv.height() + 2;
            op.height = _MH - dvH;
            op.eventItems =[];

            var p = $("#gridcontainer").bcalendar(op).BcalGetOp();
            if (p && p.datestrshow) {
                $("#txtdatetimeshow").text(p.datestrshow);
            }
            // ツールバー未選択時
            $("#caltoolbar").noSelect();
            // カレンダー入力時
            $("#hdtxtshow").datepicker({ picker: "#txtdatetimeshow", showtarget: $("#txtdatetimeshow"),
            onReturn:function(r){
                            var p = $("#gridcontainer").gotoDate(r).BcalGetOp();
                            if (p && p.datestrshow) {
                                $("#txtdatetimeshow").text(p.datestrshow);
                            }
                     }
            });
            // Ajaxイベント送信中処理
            function cal_beforerequest(type)
            {
                var t="更新中....";
                switch(type)
                {
                    case 1:
                        t="更新中....";
                        break;
                    case 2:
                    case 3:
                    case 4:
                        t="リクエスト処理中...";
                        break;
                }
                $("#errorpannel").hide();
                $("#loadingpannel").html(t).show();
            }
            // Ajaxイベント完了後処理
            function cal_afterrequest(type)
            {
                switch(type)
                {
                    case 1:
                        $("#loadingpannel").hide();
                        break;
                    case 2:
                    case 3:
                    case 4:
                        $("#loadingpannel").html("予定の更新を完了");
                        window.setTimeout(function(){ $("#loadingpannel").hide();},2000);
                    break;
                }

            }
            // Ajaxイベント送信前処理
            function cal_onerror(type,data)
            {
                $("#errorpannel").show();
            }
            // 予定表示
            function View(data)
            {
                var str = "";
                $.each(data, function(i, item){
                    str += "[" + i + "]: " + item + "\n";
                });
                alert(str);
            }
            // 予定削除
            function Delete(data,callback)
            {

                $.alerts.okButton="削除";
                $.alerts.cancelButton="中止";
                hiConfirm("この予定を削除?", 'Confirm',function(r){ r && callback(0);});
            }
            // ???週日月切り替え時使用???
            function wtd(p)
            {
               if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $("#showdaybtn").addClass("fcurrent");
            }
            // 日表示
            $("#showdaybtn").click(function(e) {
                //document.location.href="#day";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("day").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }
            });
            // 週表示
            $("#showweekbtn").click(function(e) {
                //document.location.href="#week";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("week").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }

            });
            // 月表示
            $("#showmonthbtn").click(function(e) {
                //document.location.href="#month";
                $("#caltoolbar div.fcurrent").each(function() {
                    $(this).removeClass("fcurrent");
                })
                $(this).addClass("fcurrent");
                var p = $("#gridcontainer").swtichView("month").BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }
            });
            // 更新
            $("#showreflashbtn").click(function(e){
                $("#gridcontainer").reload();
            });

            // 今日へ
            $("#showtodaybtn").click(function(e) {
                var p = $("#gridcontainer").gotoDate().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }


            });
            // 前へ
            $("#sfprevbtn").click(function(e) {
                var p = $("#gridcontainer").previousRange().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }

            });
            // 次へ
            $("#sfnextbtn").click(function(e) {
                var p = $("#gridcontainer").nextRange().BcalGetOp();
                if (p && p.datestrshow) {
                    $("#txtdatetimeshow").text(p.datestrshow);
                }
            });

        });
    </script>
</head>
<body>
    <div>

      <div id="calhead" style="padding-left:1px;padding-right:1px;">
            <!-- ▼ヘッダ -->
            <div class="cHead"><div class="ftitle">カレンダー</div>
            <div id="loadingpannel" class="ptogtitle loadicon" style="display: none;">更新中....</div>
             <div id="errorpannel" class="ptogtitle loaderror" style="display: none;">データの読み込みに失敗しました。「更新」ボタンを押してもう一度トライしてみてください。</div>
            </div>
            <!-- ▲ヘッダ -->
            <!-- ▼ツールバー -->
            <div id="caltoolbar" class="ctoolbar">
            <div class="btnseparator"></div>
             <div id="showtodaybtn" class="fbutton">
                <div><span title='今日' class="showtoday">
                今日</span></div>
            </div>
              <div class="btnseparator"></div>

            <div id="showdaybtn" class="fbutton">
                <div><span title='日' class="showdayview">日</span></div>
            </div>
              <div  id="showweekbtn" class="fbutton">
                <div><span title='週' class="showweekview">週</span></div>
            </div>
              <!-- デフォルトは月 変更は view="month"; と合わせて -->
              <div  id="showmonthbtn" class="fbutton fcurrent">
                <div><span title='月' class="showmonthview">月</span></div>

            </div>
            <div class="btnseparator"></div>
              <div  id="showreflashbtn" class="fbutton">
                <div><span title='更新' class="showdayflash">更新</span></div>
                </div>
             <div class="btnseparator"></div>
            <div id="sfprevbtn" title="前"  class="fbutton">
              <span class="fprev"></span>

            </div>
            <div id="sfnextbtn" title="次" class="fbutton">
                <span class="fnext"></span>
            </div>
            <div class="fshowdatep fbutton">
                    <div>
                        <input type="hidden" name="txtshow" id="hdtxtshow" />
                        <span id="txtdatetimeshow">表示期間を選択 »</span>

                    </div>
            </div>

            <div class="clear"></div>
            </div>
            <!-- ▲ツールバー -->
      </div>
      <div style="padding:1px;">

        <div class="t1 chromeColor">
            &nbsp;</div>
        <div class="t2 chromeColor">
            &nbsp;</div>
        <div id="dvCalMain" class="calmain printborder">
            <div id="gridcontainer" style="overflow-y: visible;">
            </div>
        </div>
        <div class="t2 chromeColor">

            &nbsp;</div>
        <div class="t1 chromeColor">
            &nbsp;
        </div>
        </div>

  </div>

</body>
</html>
