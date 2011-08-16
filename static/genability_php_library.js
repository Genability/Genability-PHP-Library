$(function (){
	$("#toggleResponse").click(function(e) {
		e.preventDefault();
		$("#json_resp").toggle();
	});

	$("a[href=#toggleTariffList]").click(function(e) {
		e.preventDefault();
		$("#tariff_list").toggle();
	});
});

function setTariff(input) {
	document.getElementById('tariffId').value = input;
	document.getElementById('tariff_list').style.display = "none";
	document.getElementById('tariffForm').submit();
}


/** calculate.php **/
var dayStrings = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
var monthStrings = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var key = "consumption";
var unit = "kwh";
$(function (){
	$( "input[name=fromDateTime]" ).datepicker({dateFormat: "yy-mm-dd'T00:00:00.0-0700'"});
	$( "input[name=toDateTime]" ).datepicker({dateFormat: "yy-mm-dd'T00:00:00.0-0700'"});

	$(".quantity_key").change(function() {
		key = this.value;
		if (key == "demand") {
			unit = "kw";
		}
		$('input[name$="[key]"]').val(key);
		$('input[name$="[unit]"]').val(unit);
	});

	$("select[name=month]").change(function() {
		$("#generatedInputs").html("");
		for (var i = 0; i < daysInMonth(this.value-1,$("input[name=year]").val()); i++) {
			var newDate = new Date($("input[name=year]").val(), $("select[name=month]").val()-1, i+1);
			var tomorrow = new Date($("input[name=year]").val(), $("select[name=month]").val()-1, i+2);
			if ((i+2) > daysInMonth(this.value-1,$("input[name=year]").val())) {
				if ($("select[name=month]").val() == 12) {
					var nextyear = parseInt($("input[name=year]").val());
					var tomorrow = new Date(nextyear, $("select[name=month]").val(), 1);
				} else {
					var tomorrow = new Date($("input[name=year]").val(), $("select[name=month]").val(), 1);
				}
			}
			$("<input/>").attr("name", "tariffInputs[" + i + "][key]").attr("type", "hidden").attr("value", "consumption").appendTo("#generatedInputs");
			$("<input/>").attr("name", "tariffInputs[" + i + "][fromDateTime]").attr("type", "hidden").attr("value", $("input[name=year]").val() + "-" + $("select[name=month]").val() + "-" + padDate(newDate.getDate()) + "T00:00:00.0-0700").appendTo("#generatedInputs");
			$("<input/>").attr("name", "tariffInputs[" + i + "][toDateTime]").attr("type", "hidden").attr("value", tomorrow.getFullYear() + "-" + padDate(tomorrow.getMonth()+1) + "-" + padDate(tomorrow.getDate()) + "T00:00:00.0-0700").appendTo("#generatedInputs");
			$("<label/>").attr("for", "tariffInputs[" + i + "][value]").html(dayStrings[newDate.getDay()] + ", " + $("select[name=month]").val() + "/" + newDate.getDate()).appendTo("#generatedInputs");
			$("<input/>").attr("name", "tariffInputs[" + i + "][value]").attr("type", "text").attr("class", "tariffValue").appendTo("#generatedInputs");
			$("<input/>").attr("name", "tariffInputs[" + i + "][unit]").attr("type", "hidden").attr("value", "kwh").appendTo("#generatedInputs");
			$("<br/>").appendTo("#generatedInputs");
		}
	});
	
	$("a[href=#fillAll]").click(function(e) {
		e.preventDefault();
		$(".tariffValue").val($("input[name=fillTheRest]").val());
		$("#easyHourInputs input").val($("input[name=fillTheRest]").val());
	});

	$("a[href=#fillHours]").click(function(e) {
		e.preventDefault();
		for ($i=0; $i<25; $i++) {
			$("label:contains('"+$i+":00:00.0')").next(".tariffValue").val($('input[name="hour['+$i+']"]').val());
		}
	});

	$("#vdcheck").click(function() {
		$("#vardump").toggle();
	});

	$("#metadata").click(function(e) {
		e.preventDefault();
		$("#generatedInputs").html("");
		$("#metadataInputs").html("");
		$("#easyHourInputs").hide();
		$("#tariffInputs").submit();
	});

	$("#one").click(function(e) {
		e.preventDefault();
		generateTariffInputs($("input[name=fromDateTime]").val(), $("input[name=toDateTime]").val());
	});

	$("#months").click(function(e) {
		e.preventDefault();
		generateTariffInputs($("input[name=fromDateTime]").val(), $("input[name=toDateTime]").val(), "months");
	});

	$("#days").click(function(e) {
		e.preventDefault();
		generateTariffInputs($("input[name=fromDateTime]").val(), $("input[name=toDateTime]").val(), "days");
	});

	$("#hours").click(function(e) {
		e.preventDefault();
		generateTariffInputs($("input[name=fromDateTime]").val(), $("input[name=toDateTime]").val(), "hours");
	});
});

function generateTariffInputs(startDate, endDate, breakdown) {
	// clear current inputs
	$("#generatedInputs").html("");
	$("#metadataInputs").html("");
	$("#easyHourInputs").hide();

	// show easy input
	$("#easyInput").show();

	// convert the dates to something javascript will like
	var startDatejs = new Date(startDate.substr(5,2) +"/" + startDate.substr(8,2) + "/" + startDate.substr(0,4));
	var endDatejs = new Date(endDate.substr(5,2) +"/" + endDate.substr(8,2) + "/" + endDate.substr(0,4));

	// i always start at the beginning
	var i=0;

	switch (breakdown) {
		case "months":
			var nextMonthjs = new Date(startDatejs);
			nextMonthjs.setMonth(startDatejs.getMonth()+1);
			console.log("nextMonthjs: " + nextMonthjs);
			var nextMonth = nextMonthjs.getFullYear() + "-" + padDate(nextMonthjs.getMonth()+1) + "-" + padDate(nextMonthjs.getDate()) + "T00:00:00.0-0700";
			console.log("nextMonth: " + nextMonth);
			var lastMonth;
			if (endDatejs > nextMonthjs) {
			while (endDatejs > nextMonthjs) {
				if (i==0) {
					appendTariffInput(i, startDate, nextMonth, monthStrings[startDatejs.getMonth()] + " " + startDatejs.getFullYear());
				} else {
					console.log("lastMonth: " + lastMonth + " lastMonthjs: " + lastMonthjs);
					appendTariffInput(i, lastMonth, nextMonth, monthStrings[lastMonthjs.getMonth()] + " " + lastMonthjs.getFullYear());
				}
				lastMonth = nextMonth;
				lastMonthjs = new Date(nextMonthjs);
				console.log("lastMonth: " + lastMonth + " lastMonthjs: " + lastMonthjs);
				nextMonthjs.setMonth(nextMonthjs.getMonth()+1);
				nextMonth = nextMonthjs.getFullYear() + "-" + padDate(nextMonthjs.getMonth()+1) + "-" + padDate(nextMonthjs.getDate()) + "T00:00:00.0-0700";
				console.log("nextMonth: " + nextMonth + " nextMonthjs: " + nextMonthjs);
				i++;
			}
			appendTariffInput(i, lastMonth, endDate, monthStrings[lastMonthjs.getMonth()] + " " + lastMonthjs.getFullYear());
			} else {
				appendTariffInput(i, startDate, endDate, monthStrings[startDatejs.getMonth()] + " " + startDatejs.getFullYear());
			}
			break;
		case "days":
			var nextDayjs = new Date(startDatejs);
			nextDayjs.setDate(nextDayjs.getDate()+1);
			var nextDay = nextDayjs.getFullYear() + '-' + padDate(nextDayjs.getMonth()+1) + '-' + padDate(nextDayjs.getDate()) + "T00:00:00.0-0700";
			var lastDay;
			if (endDatejs > nextDayjs) {
			while (endDatejs > nextDayjs) {
				if (i==0) {
					appendTariffInput(i, startDate, nextDay, dayStrings[startDatejs.getDay()] + ", " + monthStrings[startDatejs.getMonth()] + " " + startDatejs.getDate() + " " + startDatejs.getFullYear());
				} else {
					appendTariffInput(i, lastDay, nextDay, dayStrings[lastDayjs.getDay()] + ", " + monthStrings[lastDayjs.getMonth()] + " " + lastDayjs.getDate() + " " + lastDayjs.getFullYear());
				}
				lastDay = nextDay;
				lastDayjs = new Date(nextDayjs);
				nextDayjs.setDate(nextDayjs.getDate()+1);
				nextDay = nextDayjs.getFullYear() + '-' + padDate(nextDayjs.getMonth()+1) + '-' + padDate(nextDayjs.getDate()) + "T00:00:00.0-0700";
				i++;
			}
			appendTariffInput(i, lastDay, endDate, dayStrings[lastDayjs.getDay()] + ", " + monthStrings[lastDayjs.getMonth()] + " " + lastDayjs.getDate() + " " + lastDayjs.getFullYear());
			} else {
				appendTariffInput(i, startDate, endDate, dayStrings[startDatejs.getDay()] + ", " + monthStrings[startDatejs.getMonth()] + " " + startDatejs.getDate() + " " + startDatejs.getFullYear());
			}
			break;
		case "hours":
			// show easy hour inputs
			$("#easyHourInputs").show();

			// hours gets a special counter
			var h = 0;
			var nextDayjs = new Date(startDatejs);
			nextDayjs.setDate(nextDayjs.getDate()+1);
			var nextDay = nextDayjs.getFullYear() + '-' + padDate(nextDayjs.getMonth()+1) + '-' + padDate(nextDayjs.getDate()) + "T00:00:00.0-0700";
			var lastDay;
			//if (endDatejs > nextDayjs) {
			while (endDatejs >= nextDayjs) {
				if (i==0) {
					for ($j=startDate.substr(11,2); $j<=23; $j++) {
						lastDay = startDate.substr(0,11) + padDate($j) + startDate.substr(13);
						lastDay2 = startDate.substr(0,11) + padDate(parseInt($j)+1) + startDate.substr(13);
						nextDay = nextDay.substr(0,11) + "00" + nextDay.substr(13);
						if ($j == 23) {
							appendTariffInput(h, lastDay, nextDay, lastDay);
						} else {
							appendTariffInput(h, lastDay, lastDay2, lastDay);
						}
						h++;
					}
				} else {
					for ($j=0; $j<=23; $j++) {
						lastDay = lastDay.substr(0,11) + padDate($j) + lastDay.substr(13);
						lastDay2 = lastDay.substr(0,11) + padDate($j+1) + lastDay.substr(13);
						nextDay = nextDay.substr(0,11) + "00" + nextDay.substr(13);
						if ($j == 23) {
							appendTariffInput(h, lastDay, nextDay, lastDay);
						} else {
							appendTariffInput(h, lastDay, lastDay2, lastDay);
						}
						h++;
					}
				}
				nextDayjs.setDate(nextDayjs.getDate()+1);
				lastDay = nextDay;
				nextDay = nextDayjs.getFullYear() + '-' + padDate(nextDayjs.getMonth()+1) + '-' + padDate(nextDayjs.getDate()) + "T00:00:00.0-0700";
				i++;
			}
			for ($j=0; $j<=endDate.substr(11,2); $j++) {
				lastDay = lastDay.substr(0,11) + padDate($j) + lastDay.substr(13);
				lastDay2 = lastDay.substr(0,11) + padDate($j+1) + lastDay.substr(13);
				nextDay = endDate.substr(0,11) + "00" + endDate.substr(13);
				if ($j == 24) {
					appendTariffInput(h, lastDay, nextDay, lastDay);
				} else {
					appendTariffInput(h, lastDay, lastDay2, lastDay);
				}
				h++;
			}
			/*} else {
				appendTariffInput(i, startDate, endDate, "Fix Me");
			}*/
			break;
		default:
			appendTariffInput(0, startDate, endDate, "Value for Entire Timespan");
	}
}

function appendTariffInput(i, fromDateTime, toDateTime, label) {
	$("<input/>").attr("name", "tariffInputs[" + i + "][key]").attr("type", "hidden").attr("value", key).appendTo("#generatedInputs");
	$("<input/>").attr("name", "tariffInputs[" + i + "][fromDateTime]").attr("type", "hidden").attr("value", fromDateTime).appendTo("#generatedInputs");
	$("<input/>").attr("name", "tariffInputs[" + i + "][toDateTime]").attr("type", "hidden").attr("value", toDateTime).appendTo("#generatedInputs");
	$("<label/>").attr("for", "tariffInputs[" + i + "][value]").html(label).appendTo("#generatedInputs");
	$("<input/>").attr("name", "tariffInputs[" + i + "][value]").attr("type", "text").attr("class", "tariffValue").appendTo("#generatedInputs");
	$("<input/>").attr("name", "tariffInputs[" + i + "][unit]").attr("type", "hidden").attr("value", unit).appendTo("#generatedInputs");
	$("<br/>").appendTo("#generatedInputs");
}

function padDate(date) {
	if (date.toString().length < 2) {
		return "0" + date;
	} else {
		return date;
	}
}

function daysInMonth(iMonth, iYear) {
	return 32 - new Date(iYear, iMonth, 32).getDate();
}
