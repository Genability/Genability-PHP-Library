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
var selectedTimezone = '-0700';
var tariffInputsArrayOffset = 0;
var metadataInputsHTML = '';
var metadataInputs2HTML = '';
$(function (){
	// save initial inputs
	if (metadataInputsHTML == "") {
		metadataInputsHTML = $("#metadataInputs").html();
	}
	if (metadataInputs2HTML == "") {
		metadataInputs2HTML = $("#metadataInputs2").html();
	}

	// clear hidden inputs
	$("#generatedInputs").html("");
	$("#metadataInputs2").html("");

	// get selected timezone
	if ($("input[name=timezone]").is('*')) {
		selectedTimezone = $("input[name=timezone]").val();
	}

	$("input[name=fromDateTime]").datepicker({dateFormat: "yy-mm-dd'T00:00:00.0-0700'"});
	$("input[name=toDateTime]").datepicker({dateFormat: "yy-mm-dd'T00:00:00.0-0700'"});

	$("select[name=timezone]").change(function() {
		$("input[name=fromDateTime]").val($("input[name=fromDateTime]").val().substring(0,21) + $("select[name=timezone]").val());
		$("input[name=toDateTime]").val($("input[name=toDateTime]").val().substring(0,21) + $("select[name=timezone]").val());
		selectedTimezone = $("select[name=timezone]").val();
	});
	
	$("a[href=#fillAll]").click(function(e) {
		e.preventDefault();
		$(".tariffValue:visible").val($("input[name=fillTheRest]").val());
		$("#easyHourInputs input:visible").val($("input[name=fillTheRest]").val());
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

	$("#step1").click(function(e) {
		e.preventDefault();
		window.location = "calculate.php";
	});

	$("#metadata").click(function(e) {
		e.preventDefault();
		// clear current inputs
		$("#generatedInputs").html("");
		$("input[type=text]").val('');

		if (metadataInputsHTML != "") {
			$("#metadataInputs").html(metadataInputsHTML);
		}

		$("#metadataInputs").show();
		$("#generatedInputs").hide();
		$("#metadataInputs2").hide();
		$("#easyHourInputs").hide();
		
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
	console.log(selectedTimezone);
	// if currJ exists, get it and then add j to offset
	if ($("#currj").is('*')) {
		tariffInputsArrayOffset = $("#currj").val();
	} else {
		tariffInputsArrayOffset = 0;
	}

	// clear current inputs
	$("#generatedInputs").html("");
	$("#metadataInputs").html("");
	$("#metadataInputs2").html("");
	$("input[type=text]").val('');

	if (metadataInputs2HTML != "") {
		$("#metadataInputs2").html(metadataInputs2HTML);
	}

	$("#metadataInputs2").show();
	$("#generatedInputs").show();
	$("#easyHourInputs").hide();
	$("#metadataInputs").hide();

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
			var nextMonth = nextMonthjs.getFullYear() + "-" + padDate(nextMonthjs.getMonth()+1) + "-" + padDate(nextMonthjs.getDate()) + "T00:00:00.0" + selectedTimezone;
			var lastMonth;
			if (endDatejs > nextMonthjs) {
			while (endDatejs > nextMonthjs) {
				if (i==0) {
					appendTariffInput(i, startDate, nextMonth, "Consumption for " + monthStrings[startDatejs.getMonth()] + " " + startDatejs.getFullYear());
				} else {
					appendTariffInput(i, lastMonth, "Consumption for " + nextMonth, monthStrings[lastMonthjs.getMonth()] + " " + lastMonthjs.getFullYear());
				}
				lastMonth = nextMonth;
				lastMonthjs = new Date(nextMonthjs);
				nextMonthjs.setMonth(nextMonthjs.getMonth()+1);
				nextMonth = nextMonthjs.getFullYear() + "-" + padDate(nextMonthjs.getMonth()+1) + "-" + padDate(nextMonthjs.getDate()) + "T00:00:00.0" + selectedTimezone;
				i++;
			}
			appendTariffInput(i, lastMonth, endDate, "Consumption for " + monthStrings[lastMonthjs.getMonth()] + " " + lastMonthjs.getFullYear());
			} else {
				appendTariffInput(i, startDate, endDate, "Consumption for " + monthStrings[startDatejs.getMonth()] + " " + startDatejs.getFullYear());
			}
			break;
		case "days":
			var nextDayjs = new Date(startDatejs);
			nextDayjs.setDate(nextDayjs.getDate()+1);
			var nextDay = nextDayjs.getFullYear() + '-' + padDate(nextDayjs.getMonth()+1) + '-' + padDate(nextDayjs.getDate()) + "T00:00:00.0" + selectedTimezone;
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
				nextDay = nextDayjs.getFullYear() + '-' + padDate(nextDayjs.getMonth()+1) + '-' + padDate(nextDayjs.getDate()) + "T00:00:00.0" + selectedTimezone;
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
			var nextDay = nextDayjs.getFullYear() + '-' + padDate(nextDayjs.getMonth()+1) + '-' + padDate(nextDayjs.getDate()) + "T00:00:00.0" + selectedTimezone;
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
				nextDay = nextDayjs.getFullYear() + '-' + padDate(nextDayjs.getMonth()+1) + '-' + padDate(nextDayjs.getDate()) + "T00:00:00.0" + selectedTimezone;
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
			appendTariffInput(0, startDate, endDate, "Consumption for Entire Timespan");
	}
}

function appendTariffInput(i, fromDateTime, toDateTime, label) {
	if (tariffInputsArrayOffset) {
		i = i + parseInt(tariffInputsArrayOffset);
	}
	$("<input/>").attr("name", "tariffInputs[" + i + "][key]").attr("type", "hidden").attr("value", "consumption").appendTo("#generatedInputs");
	$("<input/>").attr("name", "tariffInputs[" + i + "][fromDateTime]").attr("type", "hidden").attr("value", fromDateTime).appendTo("#generatedInputs");
	$("<input/>").attr("name", "tariffInputs[" + i + "][toDateTime]").attr("type", "hidden").attr("value", toDateTime).appendTo("#generatedInputs");
	$("<label/>").attr("for", "tariffInputs[" + i + "][dataValue]").html(label).appendTo("#generatedInputs");
	$("<input/>").attr("name", "tariffInputs[" + i + "][dataValue]").attr("type", "text").attr("class", "tariffValue").appendTo("#generatedInputs");
	$("<input/>").attr("name", "tariffInputs[" + i + "][unit]").attr("type", "hidden").attr("value", "kwh").appendTo("#generatedInputs");
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
