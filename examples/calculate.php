<?
/** include the Genability PHP Library */
require_once('../genability.php');

// set your app id and app key
$gen = new genability(array(
  'app_id'  => 'your-app-id-here',    // Your Unique Genability Application ID <https://developer.genability.com/admin/applications>
  'app_key' => 'your-app-key-here',   // Your Unique Genability Application Key <https://developer.genability.com/admin/applications>
  'debug'   => false,                // Debug mode echos API Url & POST data if set to true (Optional)
));

// check to see if this account has accounts
$accounts = $gen->getAccounts();
$accounts = json_decode($accounts);

// set the default timezone for php date methods <http://www.php.net/manual/en/function.date-default-timezone-set.php>
switch ($_POST['timezone']) {
	case "-0400":
		date_default_timezone_set('America/New_York');
		break;
	case "-0500":
		date_default_timezone_set('America/Chicago');
		break;
	case "-0600":
		date_default_timezone_set('America/Boise');
		break;
	default:
		date_default_timezone_set('America/Los_Angeles');
}

// default fromDateTime and toDateTime set to the first of this month and next month
$fromDateTime = '2011-'.date('m').'-01T00:00:00.0-0700';
$toDateTime = '2011-'.date('m', mktime(0, 0, 0, date("m")+1, date("d"), date("Y"))).'-01T00:00:00.0-0700';

if ($_POST['tariffId']) {
	$TARIFF_ID = $_POST['tariffId'];
}

if ($_POST['accountId']) {
	$ACCOUNT_ID = $_POST['accountId'];
}

if ($_POST['fromDateTime']) {
	$FROM_DATE_TIME = $_POST['fromDateTime'];
}

if ($_POST['toDateTime']) {
	$TO_DATE_TIME = $_POST['toDateTime'];
}

if ($_POST['territoryId']) {
	$TERRITORY_ID = $_POST['territoryId'];
}

if ($_POST['detailLevel']) {
	$DETAIL_LEVEL = $_POST['detailLevel'];
}

// if tariffInputs are sent through POST, make the calculate call
// if the tariff parameters are sent, get the metadata parameters
// otherwise, get the tariff parameters
if ($_POST['tariffInputs']) {
	$output = $gen->getCalculation(array(
	  'masterTariffId'=> $TARIFF_ID,               // Unique Genability ID (primary key) for this tariff
	  'fromDateTime'  => $FROM_DATE_TIME,          // Starting date and time for this Calculate request. (Required)
	  'toDateTime'    => $TO_DATE_TIME,            // End date and time for this Calculate request. (Required)
	  'territoryId'   => $_POST['territoryId'],    // The territory ID of where the usage consumption occurred. (Optional)
	  'detailLevel'   => $_POST['detailLevel'],    // (Optional)
	  'tariffInputs'  => $_POST['tariffInputs'],   // The input values to use when running the calculation. (Array)
	));
} elseif ($_POST['fromDateTime'] && $_POST['toDateTime']) {
	$output = $gen->getCalculateInputs($_POST);
} elseif ($_POST['tariffId']) {
	// make the getTariff call
	$output = $gen->getTariff(array(
		'masterTariffId'=> $TARIFF_ID,	// Unique Genability ID (primary key) for this tariff
		'populateRates' => true,	// Populates the rate details for this Tariff (Boolean). The PHP Library defaults to false if not set
		'populateProperties' => true,	// Populates the rate properties needed to get the calculator inputs (Boolean). populateRates is needed for populateProperties
		'accountId'=> $ACCOUNT_ID
	));
}

$c = json_decode($output, true);
?>

<!DOCTYPE html>
<html>
<head>
	<title>Genability API PHP Library :: Examples :: calculate.php</title>
	<link rel="stylesheet" href="../static/genability_php_library.css">
	<link rel="stylesheet" href="../static/cupertino/jquery-ui-1.8.15.custom.css">
	<script src="../static/jquery-1.6.2.min.js" type="text/javascript"></script>
	<script src="../static/jquery-ui-1.8.15.custom.min.js" type="text/javascript"></script>
	<script src="../static/genability_php_library.js" type="text/javascript"></script>
</head>
<body>
<div id="genabilityExample">
	<div id="powered_by_genability">
		<h1 id="genability"><a href="http://genability.com/" target="_blank">Powered by Genability</a></h1>
		<p><a href="https://developer.genability.com/documentation/api-reference/pricing/calculate" target="_blank">Calculate Documentation</a></p>
	</div>
	<h3 class="nav">Genability API PHP Library :: Examples :: <a href="tariff.php">Tariff</a> | <a href="tariffs.php">Tariffs</a> | <a href="price.php">Price</a> | <a href="calculate.php">Calculate</a></h3>
	<h2>Calculate Example</h2>
	<form id="tariffInputs" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<div class="inputBlock">
			<label for="tariffId">Master Tariff Id</label>
			<? if ($TARIFF_ID) {
			echo $TARIFF_ID; ?>
			<input type="hidden" name="tariffId" value="<?=$TARIFF_ID?>"/>
			<? } else { ?>
			<input type="text" name="tariffId" id ="tariffId"/>
			<? } ?>
		</div>
<?if (sizeof($accounts->results)>0 && (!$_POST || $_POST['accountId'] != '')) {?>
		<div class="inputBlock">
			<label for="accountId">Account Id(Optional)</label>
			<? if ($ACCOUNT_ID) {
			echo $ACCOUNT_ID; ?>
			<input type="hidden" name="accountId" value="<?=$ACCOUNT_ID;?>"/>
			<? } else { ?>
			<select id="accountId" name="accountId">
				<option value="">--</option>
			<?for ($i=0; $i<sizeof($accounts->results); $i++) {?>
				<option value="<?=$accounts->results[$i]->accountId?>"<?if ($_POST['accountId'] == $accounts->results[$i]->accountId) echo ' selected="selected"';?>><?if ($accounts->results[$i]->accountName) echo $accounts->results[$i]->accountName . ' (' . $accounts->results[$i]->accountId . ')'; else echo $accounts->results[$i]->accountId;?></option>
			<?}?>
			</select>
			<?}?>
		</div>
<?} else {?>
		<input type="hidden" name="accountId" value=""/>
<?}?>
		<? if ($TERRITORY_ID) { ?>
		<div class="inputBlock">
			<label for="territoryId">Territory Id</label>
			<?=$TERRITORY_ID?>
			<input type="hidden" name="territoryId" value="<?=$TERRITORY_ID?>"/>
		</div>
		<? }
		if ($FROM_DATE_TIME) { ?>
		<div class="inputBlock">
			<label for="fromDateTime">From Date</label>
			<?=$FROM_DATE_TIME?>
			<input type="hidden" name="fromDateTime" value="<?=$FROM_DATE_TIME?>"/>
		</div>
		<? }
		if ($TO_DATE_TIME) { ?>
		<div class="inputBlock">
			<label for="toDateTime">To Date</label>
			<?=$TO_DATE_TIME?>
			<input type="hidden" name="toDateTime" value="<?=$TO_DATE_TIME?>"/>
		</div>
		<? }
		if ($DETAIL_LEVEL) { ?>
		<div class="inputBlock">
			<label for="detailLevel">Detail Level</label>
			<?=ucwords(strtolower(str_replace("_", " ", $_POST['detailLevel'])))?>
		</div>
		<? }
		if ($c['status'] == "success" && $c['type'] == "Tariff") { ?>
			<div class="inputBlock">
				<label for="timezone">Timezone</label>
				<select name="timezone">
					<option value="-0700">Pacific</option>
					<option value="-0600">Mountain</option>
					<option value="-0500">Central</option>
					<option value="-0400">Eastern</option>
				</select>
			</div>
			<div class="inputBlock">
				<label for="fromDateTime">From Date</label>
				<? if ($FROM_DATE_TIME) {
				echo $FROM_DATE_TIME; ?>
				<input type="hidden" name="fromDateTime" value="<?=$FROM_DATE_TIME?>"/>
				<? } else { ?>
				<input type="text" name="fromDateTime" value="<?=$fromDateTime?>"/>
				<? } ?>
			</div>
			<div class="inputBlock">
				<label for="toDateTime">To Date</label>
				<? if ($TO_DATE_TIME) {
				echo $TO_DATE_TIME; ?>
				<input type="hidden" name="toDateTime" value="<?=$TO_DATE_TIME?>"/>
				<? } else { ?>
				<input type="text" name="toDateTime" value="<?=$toDateTime?>"/>
				<? } ?>
			</div>
			<? if (sizeof($c['results'][0]['properties']) > 0) {
				for ($i=0; $i<sizeof($c['results'][0]['properties']); $i++) {
					if ($c['results'][0]['properties'][$i]['propertyTypes'] == "RATE_CRITERIA") {
						if ($c['results'][0]['properties'][$i]['keyName'] == "territory") { ?>
							<div class="inputBlock">
								<label for="territoryId">Territory</label>
								<select name="territoryId" id="territoryId">
								<? for ($j=0; $j<sizeof($c['results'][0]['properties'][$i]['choices']); $j++) { ?>
									<option value="<?=$c['results'][0]['properties'][$i]['choices'][$j]['value']?>"><?=$c['results'][0]['properties'][$i]['choices'][$j]['displayValue']?></option>
								<? } ?>
								</select>
							</div>
						<? } elseif ($c['results'][0]['properties'][$i]['dataType'] == "CHOICE") { ?>
							<div class="inputBlock">
								<label for="<?=$c['results'][0]['properties'][$i]['keyName']?>" title="<?=$c['results'][0]['properties'][$i]['description']?>"><?=$c['results'][0]['properties'][$i]['displayName']?></label>
								<select name="<?=$c['results'][0]['properties'][$i]['keyName']?>" id="<?=$c['results'][0]['properties'][$i]['keyName']?>">
								<? for ($j=0; $j<sizeof($c['results'][0]['properties'][$i]['choices']); $j++) { ?>
									<option value="<?=$c['results'][0]['properties'][$i]['choices'][$j]['value']?>"><?=$c['results'][0]['properties'][$i]['choices'][$j]['displayValue']?></option>
								<? } ?>
								</select>
							</div>
						<? } elseif ($c['results'][0]['properties'][$i]['dataType'] == "BOOLEAN") { ?>
							<div class="inputBlock">
								<label for="<?=$c['results'][0]['properties'][$i]['keyName']?>" title="<?=$c['results'][0]['properties'][$i]['description']?>"><?=$c['results'][0]['properties'][$i]['displayName']?></label>
								<select name="<?=$c['results'][0]['properties'][$i]['keyName']?>" id="<?=$c['results'][0]['properties'][$i]['keyName']?>">
									<option value="true">True</option>
									<option value="false">False</option>
								</select>
							</div>
						<? } else { ?>
							<div class="inputBlock">
								<label for="<?=$c['results'][0]['properties'][$i]['keyName']?>" title="<?=$c['results'][0]['properties'][$i]['description']?>"><?=$c['results'][0]['properties'][$i]['displayName']?></label>
								<input type="text" name="<?=$c['results'][0]['properties'][$i]['keyName']?>" id="<?=$c['results'][0]['properties'][$i]['keyName']?>"/>
							</div>
						<? }
					}
				}
			}
		} elseif ($c['status'] == "success" && $c['type'] == "PropertyData") { ?>
			<div class="inputBlock">
				<label for="detailLevel">Detail Level</label>
			<? if ($_POST['detailLevel']) {
				echo ucwords(strtolower(str_replace("_", " ", $_POST['detailLevel'])));
			} else { ?>
				<select name="detailLevel" id="detailLevel">
					<option value="ALL" <?if ($_POST['detailLevel'] == 'ALL') echo 'selected';?>>All</option>
					<option value="TOTAL" <?if ($_POST['detailLevel'] == 'TOTAL') echo 'selected';?>>Total</option>
					<option value="CHARGE_TYPE" <?if ($_POST['detailLevel'] == 'CHARGE_TYPE') echo 'selected';?>>Charge Type</option>
					<option value="RATE" <?if ($_POST['detailLevel'] == 'RATE') echo 'selected';?>>Rate</option>
				</select>
			<? } ?>
			</div>
			<div id="showInputs">
				<label>Show Inputs</label>
				<input type="button" id="metadata" value="Metadata/TOU Buckets"/>
				<input type="button" id="months" value="Months"/>
				<input type="button" id="days" value="Days"/>
				<input type="button" id="hours" value="Hours"/>
			</div>

			<div id="easyInput"<?if ($c["status"] == "success" && $c["type"] == "PropertyData") { ?>style="display: block;"<? } ?>>
				<label for="fillTheRest">Easy Input</label>
				<input type="text" name="fillTheRest" id="fillTheRest"/> <a href="#fillAll">fill all values</a>
			</div>

			<div id="easyHourInputs">
				<div class="hourCol">
				<label>Easy Hour Inputs</label><a href="#fillHours">fill hours</a>
				<div class="inputBlock">
				<label>00:00</label><input type="text" name="hour[0]"/>
				</div>
				<div class="inputBlock">
				<label>01:00</label><input type="text" name="hour[1]"/>
				</div>
				<div class="inputBlock">
				<label>02:00</label><input type="text" name="hour[2]"/>
				</div>
				<div class="inputBlock">
				<label>03:00</label><input type="text" name="hour[3]"/>
				</div>
				<div class="inputBlock">
				<label>04:00</label><input type="text" name="hour[4]"/>
				</div>
				<div class="inputBlock">
				<label>05:00</label><input type="text" name="hour[5]"/>
				</div>
				<div class="inputBlock">
				<label>06:00</label><input type="text" name="hour[6]"/>
				</div>
				<div class="inputBlock">
				<label>07:00</label><input type="text" name="hour[7]"/>
				</div>
				<div class="inputBlock">
				<label>08:00</label><input type="text" name="hour[8]"/>
				</div>
				<div class="inputBlock">
				<label>09:00</label><input type="text" name="hour[9]"/>
				</div>
				<div class="inputBlock">
				<label>10:00</label><input type="text" name="hour[10]"/>
				</div>
				<div class="inputBlock">
				<label>11:00</label><input type="text" name="hour[11]"/>
				</div>
				</div>
				<div class="hourCol right">
				<div class="inputBlock">
				<label>12:00</label><input type="text" name="hour[12]"/>
				</div>
				<div class="inputBlock">
				<label>13:00</label><input type="text" name="hour[13]"/>
				</div>
				<div class="inputBlock">
				<label>14:00</label><input type="text" name="hour[14]"/>
				</div>
				<div class="inputBlock">
				<label>15:00</label><input type="text" name="hour[15]"/>
				</div>
				<div class="inputBlock">
				<label>16:00</label><input type="text" name="hour[16]"/>
				</div>
				<div class="inputBlock">
				<label>17:00</label><input type="text" name="hour[17]"/>
				</div>
				<div class="inputBlock">
				<label>18:00</label><input type="text" name="hour[18]"/>
				</div>
				<div class="inputBlock">
				<label>19:00</label><input type="text" name="hour[19]"/>
				</div>
				<div class="inputBlock">
				<label>20:00</label><input type="text" name="hour[20]"/>
				</div>
				<div class="inputBlock">
				<label>21:00</label><input type="text" name="hour[21]"/>
				</div>
				<div class="inputBlock">
				<label>22:00</label><input type="text" name="hour[22]"/>
				</div>
				<div class="inputBlock">
				<label>23:00</label><input type="text" name="hour[23]"/>
				</div>
				</div>
			</div>
		<? } ?>
	<input type="submit" value="Proceed" id="proceed"/>

	<hr/>

	<a id="toggleResponse" href="#">view/hide response</a><div id="json_resp"><?=var_dump(json_decode($output, true));?></div>

<?if ($c["status"] == "success" && $c["type"] == "PropertyData") { ?>
<table id="metadataInputs" class="pretty_blue_table">
	<tr>
		<th>key</th>
		<th>fromDateTime</th>
		<th>toDateTime</th>
		<th>value</th>
		<th>unit</th>
	</tr>
<? for ($i = 0; $i < sizeof($c["results"]) ; $i++) {  ?>
	<tr>
		<td><?=$c["results"][$i]["keyName"]?><input type="hidden" name="tariffInputs[<?=$i?>][keyName]" value="<?=$c[results][$i][keyName]?>"/></td>
		<td><?=date("n/j/y g:i a", strtotime($c["results"][$i]["fromDateTime"]))?><input type="hidden" name="tariffInputs[<?=$i?>][fromDateTime]" value="<?=$c[results][$i][fromDateTime]?>"/></td>
		<td><?=date("n/j/y g:i a", strtotime($c["results"][$i]["toDateTime"]))?><input type="hidden" name="tariffInputs[<?=$i?>][toDateTime]" value="<?=$c[results][$i][toDateTime]?>"/></td>
		<td><? if ($c['results'][$i]['dataValue']) { ?>
			<?=$c['results'][$i]['dataValue']?>
			<input type="hidden" name="tariffInputs[<?=$i?>][dataValue]" value="<?=$c['results'][$i]['dataValue']?>"/>
		<? } elseif ($c["results"][$i]["keyName"] != 'consumption' && $c["results"][$i]["keyName"] != 'demand') {
			$gpk = $gen->getPropertyKey(array('keyName'=> $c["results"][$i]["keyName"])); $gpk= json_decode($gpk, true);
			if ($gpk["results"][0]["dataType"] == "CHOICE" || $gpk["results"][0]["dataType"] == "BOOLEAN") { ?>
			<select name="tariffInputs[<?=$i?>][dataValue]">
				<? for ($j = 0; $j < sizeof($gpk["results"][0]["choices"]); $j++) { ?>
				<option value="<?=$gpk["results"][0]["choices"][$j]["value"]?>"><?=$gpk["results"][0]["choices"][$j]["displayValue"]?></option>
				<? } ?>
			</select>
			<? } else { ?>
			<input type="text" name="tariffInputs[<?=$i?>][dataValue]" class="tariffValue" placeholder="Enter <?=ucwords(strtolower($gpk['results'][0]['dataType']))?>"/>
			<? } ?>
		<? } else { ?>
		<input type="text" name="tariffInputs[<?=$i?>][dataValue]" class="tariffValue" placeholder="Enter <?=ucwords(strtolower($c[results][$i][dataType]))?>"/>
		<? }?></td>
		<td><?=$c["results"][$i]["unit"]?><input type="hidden" name="tariffInputs[<?=$i?>][unit]" value="<?=$c[results][$i][unit]?>"/></td>
	</tr>
<?	}
if ($ACCOUNT_ID) { ?>
	<tr>
		<td>accountId<input type="hidden" name="tariffInputs[<?=$i?>][keyName]" value="accountId"/></td>
		<td></td>
		<td></td>
		<td><input type="hidden" name="tariffInputs[<?=$i?>][dataValue]" value="<?=$ACCOUNT_ID?>"/><?=$ACCOUNT_ID?></td>
		<td></td>
	</tr>
<? }
?>
</table>
<? } ?>

<?if ($c["status"] == "success" && $c["type"] == "PropertyData") {
$nonConsumption = false;
$j=0;
	for ($i = 0; $i < sizeof($c["results"]) ; $i++) {
		if ($c['results'][$i]['keyName'] != "consumption") {
			if ($nonConsumption == false) { ?>
<table id="metadataInputs2" class="pretty_blue_table">
	<tr>
		<th>key</th>
		<th>fromDateTime</th>
		<th>toDateTime</th>
		<th>value</th>
		<th>unit</th>
	</tr>
<? } $nonConsumption = true; ?>
	
	<tr>
		<td><?=$c["results"][$i]["keyName"]?><input type="hidden" name="tariffInputs[<?=$j?>][keyName]" value="<?=$c[results][$i][keyName]?>"/></td>
		<td><?=date("n/j/y g:i a", strtotime($c["results"][$i]["fromDateTime"]))?><input type="hidden" name="tariffInputs[<?=$j?>][fromDateTime]" value="<?=$c[results][$i][fromDateTime]?>"/></td>
		<td><?=date("n/j/y g:i a", strtotime($c["results"][$i]["toDateTime"]))?><input type="hidden" name="tariffInputs[<?=$j?>][toDateTime]" value="<?=$c[results][$i][toDateTime]?>"/></td>
		<td><? if ($c['results'][$i]['dataValue']) { ?>
			<?=$c['results'][$i]['dataValue']?>
			<input type="hidden" name="tariffInputs[<?=$i?>][dataValue]" value="<?=$c['results'][$i]['dataValue']?>"/>
		<? } else if ($c["results"][$i]["keyName"] != 'consumption' && $c["results"][$i]["keyName"] != 'demand') {
			$gpk = $gen->getPropertyKey(array('keyName'=> $c["results"][$i]["keyName"])); $gpk= json_decode($gpk, true);
			if ($gpk["results"][0]["dataType"] == "CHOICE" || $gpk["results"][0]["dataType"] == "BOOLEAN") { ?>
			<select name="tariffInputs[<?=$i?>][dataValue]" class="tariffValue">
				<? for ($j = 0; $j < sizeof($gpk["results"][0]["choices"]); $j++) { ?>
				<option value="<?=$gpk["results"][0]["choices"][$j]["value"]?>"><?=$gpk["results"][0]["choices"][$j]["displayValue"]?></option>
				<? } ?>
			</select>
			<? } else { ?>
			<input type="text" name="tariffInputs[<?=$i?>][dataValue]" class="tariffValue" placeholder="Enter <?=ucwords(strtolower($gpk['results'][0]['dataType']))?>"/>
			<? } ?>
		<? } else { ?>
		<input type="text" name="tariffInputs[<?=$i?>][dataValue]" class="tariffValue" placeholder="Enter <?=ucwords(strtolower($c[results][$i][dataType]))?>"/>
		<? }?></td>
		<td><?=$c["results"][$i]["unit"]?><input type="hidden" name="tariffInputs[<?=$j?>][unit]" value="<?=$c[results][$i][unit]?>"/></td>
	</tr>
<?	$j++;	}
	}
if ($nonConsumption == true) { echo '<input type="hidden" id="currj" value="'.$j.'"/></table>'; }
} ?>

<div id="generatedInputs"></div>
</form>

<? if ($c["status"] == "success" && $c["type"] == "CalculatedCost") {
	for ($i=0; $i<sizeof($c["results"]); $i++) { ?>
	<div class="tariff_info">
		<p><label>Master Tariff Id</label><?=$c["results"][$i]["masterTariffId"]?></p>
		<p><label>Tariff Name</label><?=$c["results"][$i]["tariffName"]?></p>
		<p><label>Timespan</label>From <?=date("D M j, Y g:i a", strtotime($c["results"][$i]["fromDateTime"]))?> to <?=date("D M j, Y g:i a", strtotime($c["results"][$i]["toDateTime"]))?></p>
		<p><label>Total Cost</label><strong><?=$c["results"][$i]["totalCost"]?></strong></p>
	</ul>
	<? if ($c["results"][$i]["items"] != NULL) { ?>
	<table class="cost_breakdown pretty_blue_table">
		<thead>
			<tr>
				<th>Rate Id</th>
				<th>Rate Band Id</th>
				<th>Rate Group Name</th>
				<th>Rate Name</th>
				<th>From Date</th>
				<th>To Date</th>
				<th>Rate Type</th>
				<th>Quantity Key</th>
				<th>Quantity</th>
				<th>Cost</th>
			</t>
		</thead>
		<tbody><?foreach ($c["results"][$i]["items"] as $ci) {?>
			<tr>
				<td><?=$ci["tariffRateId"]?></td>
				<td><?=$ci["tariffRateBandId"]?></td>
				<td><?=$ci["rateGroupName"]?></td>
				<td><?=$ci["rateName"]?></td>
				<td><?=date("n/j/y g:i a", strtotime($ci["fromDateTime"]))?></td>
				<td><?=date("n/j/y g:i a", strtotime($ci["toDateTime"]))?></td>
				<td><?=strtolower(str_replace("_", " ", $ci["rateType"]));?></td>
				<td><?=$ci["quantityKey"]?></td>
				<td><?=$ci["itemQuantity"]?></td>
				<td><?=$ci["cost"]?></td>
			</tr>
		<?}?></tbody>
	</table>
	<? } }
} else if ($c["type"] == "Error" && $c["results"][0]["objectName"] == "request") { ?>
	<p class="error">Please enter a valid app id and app key, you can grab a pair here: <a href="https://developer.genability.com/admin/applications">https://developer.genability.com/admin/applications</a>
<? } else if ($c["type"] == "Error") {
	echo '<p class="error">Error occured in calculate() method :<br/><br/>';
	foreach ($c["results"] as $errors) {
		echo $errors['objectName'] . ' ' . $errors['code'] . '  ' . $errors['message'] . '<br/>';
	}
	echo '</p>';
} ?>
</div>
</body>
</html>
