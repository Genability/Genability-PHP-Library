<?
// set the default timezone for php date methods <http://www.php.net/manual/en/function.date-default-timezone-set.php>
date_default_timezone_set('America/Los_Angeles');

// default fromDateTime and toDateTime set to today and 2 days from now
$mili_sec = substr(date('u'),0,1);
$fromDateTime = date('Y-m-d')."T".date('H:i:s').".".$mili_sec.date('O');
$toDateTime = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")+2, date("Y")))."T".date('H:i:s').".".$mili_sec.date('O');

// set the price paramters to whatever is sent via POST
// othwerwise, set params to default
if ($_POST['tariff']) {
	$TARIFF_ID = $_POST['tariff'];
} else {
	$TARIFF_ID = '82010';
}

if ($_POST['fromDateTime']) {
	$FROM_DATE_TIME = $_POST['fromDateTime'];
} else {
	$FROM_DATE_TIME = $fromDateTime;
}

if ($_POST['toDateTime']) {
	$TO_DATE_TIME = $_POST['toDateTime'];
} else {
	$TO_DATE_TIME = $toDateTime;
}

if ($_POST['territoryId']) {
	$TERRITORY_ID = $_POST['territoryId'];
}

if ($_POST['consumptionAmount']) {
	$CONSUMPTION_AMOUNT = $_POST['consumptionAmount'];
} else {
	$CONSUMPTION_AMOUNT = '500';
}

if ($_POST['demandAmount']) {
	$DEMAND_AMOUNT = $_POST['demandAmount'];
} else {
	$DEMAND_AMOUNT = '500';
}

if ($_POST['accountId']) {
	$ACCOUNT_ID = $_POST['accountId'];
}

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

// make the getPrice call
$output = $gen->getPrice(array(
  'masterTariffId'    => $TARIFF_ID,           // Unique Genability ID (primary key) for this tariff
  'fromDateTime'      => $FROM_DATE_TIME,      // Date and time of the requested start of the Price. In ISO 8601 format. (Required)
  'toDateTime'        => $TO_DATE_TIME,        // Date and time of the requested start of the Price. In ISO 8601 format. (Optional)
  'territoryId'       => $TERRITORY_ID,        // When specified, rate changes returned will be for the specified Territory. (Optional)
  'consumptionAmount' => $CONSUMPTION_AMOUNT,  // A monthly consumption in kWh. (Optional)
  'demandAmount'      => $DEMAND_AMOUNT,       // A monthly demand in kW. (Optional)
  'accountId'         => $ACCOUNT_ID
));

?>


<!DOCTYPE html>
<html>
<head>
	<title>Genability API PHP Library :: Examples :: price.php</title>
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
		<p><a href="https://developer.genability.com/documentation/api-reference/public/price" target="_blank">Get Price Documentation</a></p>
	</div>
	<h3 class="nav">Genability API PHP Library :: Examples :: <a href="tariff.php">Tariff</a> | <a href="tariffs.php">Tariffs</a> | <a href="price.php">Price</a> | <a href="calculate.php">Calculate</a> | <a href="accountManager.php">Accounts</a></h3>
	<h2>Get Price Example</h2>
	<form id="genabilityExammple" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<div class="inputBlock">
			<label for="tariff">Master Tariff Id</label>
			<input type="text" id="tariffId" name="tariff" value="<?=$TARIFF_ID?>"/>
			<a href="#toggleTariffList">tariff cheat sheet</a>
		</div>
		<div id="tariff_list">
			<a class="close" href="#toggleTariffList">x close</a>
			<div class="lse">
				<p class="lse_name">Residential </p>
				<p class="lse_tariff"><a href="#" onClick="setTariff('684'); return false;">684</a> - Residence Service (PECO Energy- PA)</p>
				<p class="lse_tariff"><a href="#" onClick="setTariff('706'); return false;">706</a> - Residential Time-of-Day Service - Underground
				</br>(Xcel Energy - MN)</p>
				<p class="lse_tariff"><a href="#" onClick="setTariff('408'); return false;">408</a> - Residential Time-of-Use with Demand Charge Combined Advantage 7 PM - Noon</br>(Arizona Public Service - AZ)</p>				
			</div>
			<div class="lse second">
				<p class="lse_name">General</p>
				<p class="lse_tariff"><a href="#" onClick="setTariff('82010'); return false;">82010</a> - Small General Service Time-of-Use
				</br>(Pacific Gas & Electric - CA) </p>
				<p class="lse_tariff"><a href="#" onClick="setTariff('83002'); return false;">83002</a> - Large General Service Secondary Voltage </br>(Dominion Virginia - VA)</p>			
			</div>
		</div>
		<div class="inputBlock">
			<label for="fromDateTime">From</label>
			<input type="text" name="fromDateTime" value="<?=$FROM_DATE_TIME?>"/>
		</div>
		<div class="inputBlock">
			<label for="toDateTime">To</label>
			<input type="text" name="toDateTime" value="<?=$TO_DATE_TIME?>"/>
		</div>
		<div class="inputBlock">
			<label for="consumptionAmount">Consumption Amount</label>
			<input type="text" name="consumptionAmount" value="<?=$CONSUMPTION_AMOUNT?>"/>
		</div>
		<div class="inputBlock">
			<label for="demandAmount">Demand Amount</label>
			<input type="text" name="demandAmount" value="<?=$DEMAND_AMOUNT?>"/>
		</div>
<?if (sizeof($accounts->results)>0) {?>
		<div class="inputBlock">
			<label for="accountId">Account Id(Optional)</label>
			<select id="accountId" name="accountId">
				<option value="">--</option>
			<?for ($i=0; $i<sizeof($accounts->results); $i++) {?>
				<option value="<?=$accounts->results[$i]->accountId?>"<?if ($_POST['accountId'] == $accounts->results[$i]->accountId) echo ' selected="selected"';?>><?if ($accounts->results[$i]->accountName) echo $accounts->results[$i]->accountName . ' (' . $accounts->results[$i]->accountId . ')'; else echo $accounts->results[$i]->accountId;?></option>
			<?}?>
			</select>
		</div>
<?}?>
		<button type="submit">Get Prices!</button>
	</form>

	<a id="toggleResponse" href="#">view/hide response</a><div id="json_resp"><?=var_dump(json_decode($output, true));?></div>

<?	
$pricesArr = json_decode($output, true);

if ($pricesArr["status"] == "success") {
	foreach ($pricesArr["results"] as $results) { ?>
	<div class="tariff_info">
		<p><label>Master Tariff Id</label><?=$results["tariffId"]?></p>
		<p><label>Current Rate Amount</label><?=$results["rateAmount"]?></p>
		<p><label>Charge Type</label><?=ucwords(strtolower(str_replace("_", " ", $results["chargeType"])))?></p>
		<p><label>Relative Price Index</label><?=$results["relativePriceIndex"]?></p>
		<p><label>Price Changes</label>
		<?if (count($results["priceChanges"]) == 0) {?>
		<em>No Price Change Data</em></p>
		<?} else {?>
		</p><table class="pretty_blue_table">
			<thead>
				<tr>
					<th>Change Date/Time</th>
					<th class="rpi" title="Relative Price Index">RPI</th>
					<th>Change Name</th>
					<th>Rate Amount</th>
					<th>Accuracy</th>
				</t>
			</thead>
			<tbody><?foreach ($results["priceChanges"] as $priceChanges) {?>
				<tr>
					<td title="<?=$priceChanges["changeDateTime"]?>"><?=date("D M j, Y g:i a", strtotime($priceChanges["changeDateTime"]))?></td>
					<td><?=$priceChanges["relativePriceIndex"]?></td>
					<td class="change_name"><?=$priceChanges["changeName"]?></td>
					<td><?=$priceChanges["rateAmount"]?></td>
					<td><?=$priceChanges["accuracy"]?></td>
				</tr>
			<?}?></tbody>
		</table></p>
		<?}?>
	</div>
	<?}
} elseif ($pricesArr["type"] == "Error" && $pricesArr["results"][0]["objectName"] == "requestSignature") { ?>
		<p class="error">Please enter a valid app id and app key, you can grab a pair here: <a href="https://developer.genability.com/admin/applications">https://developer.genability.com/admin/applications</a>
	<?} else {
	echo '<p class="error">Error occured in getPrice() method :<br/><br/>';
	foreach ($pricesArr["results"] as $errors) {
		echo $errors['objectName'] . ' ' . $errors['code'] . '  ' . $errors['message'] . '<br/>';
	}
	echo '</p>';
}
?>
</div>
</body>
</html>
