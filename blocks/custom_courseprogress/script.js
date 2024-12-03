var inactiveusers = parseInt($('#inactiveuser').val());
var activeusers = parseInt($('#activeuser').val());
var totalUsers = activeusers + inactiveusers;
var activeuserpercentage = (parseFloat((activeusers / totalUsers) * 100)).toFixed(2);
var inactiveuserpercentage = (parseFloat((inactiveusers / totalUsers) * 100)).toFixed(2);
var donutEl = document.getElementById("donut").getContext("2d");
var myDonutChart = new Chart(donutEl, {
  type: "doughnut",
  data: {
    datasets: [
      {
        data: [activeuserpercentage, inactiveuserpercentage],
        backgroundColor: ["#A154A1", "#6A68C5"],
        hoverBackgroundColor: ["#D74B8E", "#6A68C5"],
        labels: ["Red", "Green", "Yellow"],
      },
    ],
  },
  options: {
    tooltips: {
      callbacks: {
        label: function (tooltipItem, data) {
          // Calculate the total of all segments to get the percentage
          var total = data.datasets[0].data.reduce(function (accumulator, currentValue) {
            return accumulator + currentValue;
          }, 0);

          // Get the current data value and calculate the percentage
          var currentValue = data.datasets[0].data[tooltipItem.index];
          var percentage = ((currentValue / total) * 100).toFixed(2) + '%';

          return data.labels[tooltipItem.index] + ': ' + percentage;
        },
      },
    },
    cutoutPercentage: 50,
    animation: {
      animateScale: true,
      animateRotate: true,
    },
    responsive: true,
    maintainAspectRatio: true,
    legend: {
      display: true,
      position: "right",
    },
  },
});
function userdownload(state) {
  var from = new Date($("#dateFrom").val());
  var to = new Date($("#dateTo").val());
  var url =
    M.cfg.wwwroot +
    "/blocks/custom_courseprogress/download.php?userstate="+state+"&from=" +
    $("#dateFrom").val() +
    "&to=" +
    $("#dateTo").val();
  var check = true;
  if (to == "Invalid Date" || from == "Invalid Date") {
    url =
      M.cfg.wwwroot +
      "/blocks/custom_courseprogress/download.php?userstate="+state+"&lastday=30";
    check = false;
  }
  if (from > to && check) {
    alert("From Date must be greater than To Date");
  } else {
    location.replace(url);
  }
}

function updateChartData(newData) {
  myDonutChart.data.datasets[0].data = newData;
  myDonutChart.update();
}

function filter_loginuser() {
  var from = new Date($("#dateFrom").val());
  var to = new Date($("#dateTo").val());
  var getdetail = true;

  if (to == "Invalid Date" || from == "Invalid Date") {
    getdetail = false;
    alert("Select a from and to date");
  } else if (from > to) {
    getdetail = false;
    alert("From Date must be greater than To Date");
  } else {
    $.ajax({
      url: M.cfg.wwwroot + "/blocks/custom_courseprogress/ajax.php",
      dataType: "json",
      data: { fromdate: $("#dateFrom").val(), todate: $("#dateTo").val() },
      success: function (returnData) {
        $('#loginActivity').hide();
        var totaluser = returnData.activeusers + returnData.inactiveusers;
        $('#activeUserCount').html('(' + returnData.activeusers + '/ ' + totaluser + ')');
        $('#inactiveUserCount').html('(' + returnData.inactiveusers + '/ ' + totaluser + ')');
        var totalUsers = returnData.activeusers + returnData.inactiveusers;
        var activeuserpercentage = (parseFloat((returnData.activeusers / totalUsers) * 100)).toFixed(2);
        var inactiveuserpercentage = (parseFloat((returnData.inactiveusers / totalUsers) * 100)).toFixed(2);
        updateChartData([
          `${activeuserpercentage}`,
          `${inactiveuserpercentage}`,
        ]);
      },
    });
  }
}

function filtercohorts(){
  let val = $('#select1').val();
  $.ajax({
      type: "GET",
      url: M.cfg.wwwroot + "/blocks/custom_courseprogress/ajax.php", // PHP script that provides data
      dataType: "json",
      data: {courseid : val},
      success: function (returndata) {
        const data_key = Object.keys(returndata.response)
        const value_key = Object.values(returndata.response)
        // jazib 
        updateline(data_key,value_key, returndata.coursename)
      }
  });
}

function filteractivity(){
  let val = $('#selectactivity1').val();
  $.ajax({
      type: "GET",
      url: M.cfg.wwwroot + "/blocks/custom_courseprogress/ajax.php", // PHP script that provides data
      dataType: "json",
      data: {courseid : val, graph : 'bar'},
      success: function (returndata) {
        const data_key = Object.keys(returndata.response)
        const value_key = Object.values(returndata.response)
        // jazib 
        updatelinebar(data_key,value_key, returndata.coursename)
      }
  });
}

function course_access_report_download() {
  var courseid = $('#select1').val();
  url = M.cfg.wwwroot + "/blocks/custom_courseprogress/download.php?courseid="+courseid;
  location.replace(url);
}

function course_activity_report_download() {
  var courseid = $('#select1').val();
  url = M.cfg.wwwroot + "/blocks/custom_courseprogress/download.php?courseid="+courseid+"&graph=bar";
  location.replace(url);
}
var config = {
  type: "line",
  data: {
      labels: ["Cohort-A", "Cohort-B", "Cohort-C", "Cohort-D", "Cohort-E", "Cohort-F", "Cohort-G"],
      datasets: [{
          label: "APAC PME",
          backgroundColor: "#904E8F",
          borderColor: "#904E8F",
          fill: false,
          lineTension: 0.3,
          data: [50, 300, 100, 450, 150, 200, 300],
      }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: false,
        suggestedMin: 1,
        ticks: {
          precision: 0,
        },
      }
    }
  }
};

var configbar = {
  type: "bar",
  data: {
      labels: ["Cohort-A", "Cohort-B", "Cohort-C", "Cohort-D", "Cohort-E", "Cohort-F", "Cohort-G"],
      datasets: [{
          label: "APAC PME",
          backgroundColor: "#904E8F",
          borderColor: "#904E8F",
          fill: false,
          lineTension: 0.3,
          data: [50, 300, 100, 450, 150, 200, 300],
      }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true,
        suggestedMin: 1,
        ticks: {
          precision: 0,
        },
      }
    }
  }
};

window.onload = function () {
  var ctx = document.getElementById("canvas").getContext("2d");
  var ctxbar = document.getElementById("bargraph").getContext("2d");
  let val = $('#select1').val();
  $.ajax({
    type: "GET",
    url: M.cfg.wwwroot + "/blocks/custom_courseprogress/ajax.php", // PHP script that provides data
    dataType: "json",
    data: {courseid : val},
    success: function (returndata) {
      const data_label = Object.keys(returndata.response)
      const value_set = Object.values(returndata.response)
      // jazib 
      config.data.labels = data_label
      config.data.datasets[0].data = value_set
      config.data.datasets[0].label = returndata.coursename
      window.myLine = new Chart(ctx, config);
    }
  });


  $.ajax({
    type: "GET",
    url: M.cfg.wwwroot + "/blocks/custom_courseprogress/ajax.php", // PHP script that provides data
    dataType: "json",
    data: {courseid : val, graph : 'bar'},
    success: function (returndatabar) {
      const data_label_bar = Object.keys(returndatabar.response)
      const value_set_bar = Object.values(returndatabar.response)
      // jazib 
      configbar.data.labels = data_label_bar
      configbar.data.datasets[0].data = value_set_bar
      configbar.data.datasets[0].label = returndatabar.coursename
      window.myLinebar = new Chart(ctxbar, configbar);
    }
  });

};

// jazib
function updateline(data_key,value_key,label){
  config.data.labels = data_key
  config.data.datasets[0].data = value_key
  config.data.datasets[0].label = label
  myLine.update()
}

function updatelinebar(data_key,value_key,label){
  configbar.data.labels = data_key
  configbar.data.datasets[0].data = value_key
  configbar.data.datasets[0].label = label
  myLinebar.update()
}