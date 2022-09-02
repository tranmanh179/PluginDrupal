var $ = jQuery;
var countingTime = {};
var taskCoutingTime = [];

// countingTime: object chứa thời gian làm của các node
// taskCoutingTime: mảng chứa id các node đang chạy giờ

countingTime= drupalSettings.clockkanban.countingTime;
taskCoutingTime = drupalSettings.clockkanban.taskCoutingTime;

$('.card-title').each(function( index ) {
  var task = $(this).closest( "article" );
  var idNode = String(task.data('id'));
  $(this).append(' <i onclick="startCountTime(this);" class="bi bi-alarm startClock" aria-hidden="true"></i> <span class="showClock" id="showClock-'+idNode+'"></span></span>');
});

function startCountTime(clock) {
  var task = $(clock).closest("article");
  var idNode = String(task.data('id'));

  console.log('Kiểm tra task ' + idNode);

  if(taskCoutingTime.includes(String(idNode)) == false) {
    $.ajax({
      method: "POST",
      url: "/clock-kanban-save-start-counting-time",
      data: {idNode: idNode}
    })
      .done(function (msg) {
        console.log('bắt đầu tính giờ task ' + idNode);

        if(countingTime[idNode] == undefined){
          countingTime[idNode] = 1;
          task.find(".showClock").html('00:00:01');
        }
        console.log(taskCoutingTime);
        taskCoutingTime = [];
        taskCoutingTime.push(String(idNode));
        console.log(taskCoutingTime);
        startLoad();
      });
  }else{
    var index = taskCoutingTime.indexOf(idNode);
    if (index > -1) {
      $.ajax({
        method: "POST",
        url: "/clock-kanban-save-pause-counting-time",
        data: {idNode: idNode}
      })
        .done(function (msg) {
          console.log('task '+idNode+' đã tạm dừng tính giờ');

          taskCoutingTime.splice(index, 1);

          startLoad();
        });
    }
  }
};


function caculateTime()
{
  var i;
  var textShowClock
  var n= Object.keys(countingTime).length;
  if(n > 0){
    for (i in countingTime) {
      if(taskCoutingTime.includes(i)){
        countingTime[i]++;
        textShowClock = convertTime(countingTime[i]);
        $('#showClock-'+i).html(textShowClock);
      }
    }
  }

}

function convertTime(timeCouting)
{
  var second = timeCouting % 60;
  var minute = (timeCouting - second)/60;
  var hour = 0;
  var day = 0;
  var textTime = '';

  if(minute >= 60) {
    var minuteNow = minute;
    minute = minuteNow % 60;
    hour = (minuteNow-minute)/60;
  }

  if(hour >= 24) {
    var hourNow = hour;
    hour = hourNow % 24;
    day = (hourNow-hour)/24;
  }

  var textHour;
  if(hour<10){
    textHour= '0'+hour;
  }else{
    textHour= hour;
  }

  var textMinute;
  if(minute<10){
    textMinute= '0'+minute;
  }else{
    textMinute= minute;
  }

  var textSecond;
  if(second<10){
    textSecond= '0'+second;
  }else{
    textSecond= second;
  }

  textTime = textHour +':'+ textMinute + ':' + textSecond;
  if(day>0) {
    textTime = day + 'd ' + textTime;
  }

  return textTime;
}

function startLoad()
{
  var i;
  var textShowClock
  var n= Object.keys(countingTime).length;
  if(n > 0){
    for (i in countingTime) {
      textShowClock = convertTime(countingTime[i]);
      $('#showClock-'+i).html(textShowClock);

      if(taskCoutingTime.includes(i)){
        $('#showClock-'+i).css('color','green');
      }else{
        $('#showClock-'+i).css('color','red');
      }
    }
  }
}

startLoad();
setInterval(caculateTime, 1000);



