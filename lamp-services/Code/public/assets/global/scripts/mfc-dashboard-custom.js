$(document).ready(function() {
  // Custom Date Formats
  $("#customDatePickerZone .input-daterange").datepicker({
    format: "dd/mm/yyyy",
    endDate: "today",
    todayHighlight: true
  });

  $.ajaxSetup({
    headers: { "X-CSRF-Token": $('input[name="_token"]').val() },
    dataType: "JSON"
  });

  var load_url = window.location.pathname == "/mfc" ? "/mfc/" : "/";
  

  $("#loader").hide();

  $("#customDateWidthSubmit").click(function() {
    var toDate = $("#toDate").val();
    var fromDate = $("#fromDate").val();
    if (
      toDate == undefined ||
      toDate == "" ||
      (fromDate == undefined || fromDate == "")
    ) {
      alert("Please Select Valid To & From Dates");
      $("#fromDate, #toDate").val("");
    } else {
      toDateCheck = new Date(toDate);
      fromDateCheck = new Date(fromDate);
      if (fromDateCheck > toDateCheck) {
        alert("Please Select Proper Date Range");
        $("#fromDate, #toDate").val("");
      } else {
        loadDashboardData("custom", toDate, fromDate);
      }
    }
  });

  $("#dashboard_filter_dates").change(function() {
    // If the thing(date) is not Custom,
    // then the predefined Dates loads
    var filterData = $(this).val();
    if (filterData != "custom") {
      $("#customDatesView").addClass("customDateArea");
      $("#fromDate, #toDate").val("");
      loadDashboardData(filterData, 0, 0);
    } else {
      $("#customDatesView").removeClass("customDateArea");
    }
  });

  function loadDashboardData(filterData, toDate, fromDate) {
    $('[class="loader"]').show();
    $('[class="data_value"]').text(0);

    var inputData = {
      filter_date: filterData,
      fromDate: fromDate,
      toDate: toDate
    };
    var custom_load_url = "/mfc";
    var response = $.post(custom_load_url, inputData);
    response.done(function(data) {
      var mainGridData = {};
      if (data.cncData != undefined) {
        mainGridData = data.cncData;
      } else if (data.order_details != undefined) {
        mainGridData = data.order_details;
      }
      $.each(mainGridData, function(key, value) {
        var test = key.toLowerCase();
        var temp = test.replace(/[^A-Z0-9]/gi, "_");
        if (temp == "dashboard") {
          $.each(value, function(key2, dashboard) {
            $.each(dashboard, function(key3, dashboardData) {
              var key3 = dashboardData.key;
              var val3 = dashboardData.val;
              var per3 = dashboardData.per;
              if (key3 != null) {
                var test3 = key3.toString().toLowerCase();
                var temp3 = test3.replace(/[^A-Z0-9]/gi, "_");
                $("#" + temp3).text(val3);
                $("#data_per_" + temp3).text(per3);
              }
            });
          });
        }
      });
    });

    $('[class="loader"]').hide();
  }

  function getInputDates() {
    return {
      filter_date: $("#dashboard_filter_dates").val(),
      toDate: $("#toDate").val(),
      fromDate: $("#fromDate").val()
    };
  }
  // Those dotted blue circles, which disturbs the UI Cards will be closed/hidden here
  $('[class="loader"]').hide();
});
