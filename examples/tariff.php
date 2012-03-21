<?
// set the default timezone for php date methods <http://www.php.net/manual/en/function.date-default-timezone-set.php>
date_default_timezone_set('America/Los_Angeles');

// set the TARIFF_ID to whatever is sent via POST
// othwerwise, default to example tariff
if ($_POST['tariff']) {
	$TARIFF_ID = $_POST['tariff'];
} else {
	$TARIFF_ID = '82010';
}

// if a Territory Id is passed through, set it
if ($_POST['territoryId']) {
	$TERRITORY_ID = $_POST['territoryId'];
}

// if an Account Id is passed through, set it
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

// make the getTariff call
$output = $gen->getTariff(array(
  'masterTariffId'=> $TARIFF_ID,	// Unique Genability ID (primary key) for this tariff
  'populateRates' => true,		// Populates the rate details for this Tariff (Boolean). The PHP Library defaults to false if not set
  'territoryId'   => $TERRITORY_ID,     // When specified, rate changes returned will be for the specified Territory. (Optional)
  'accountId'     => $ACCOUNT_ID,
));

// check to see if this account has accounts
$accounts = $gen->getAccounts();
$accounts = json_decode($accounts);

// helper method to display text easier (lowercase and without _s)
function formatText($input) {
	return strtolower(str_replace("_", " ", $input));
}
?>


<!DOCTYPE html>
<html>
<head>
	<title>Genability API PHP Library :: Examples :: tariff.php</title>
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
		<p><a href="https://developer.genability.com/documentation/api-reference/public/tariff" target="_blank">Get Tariff Documentation</a></p>
	</div>
	<h3 class="nav">Genability API PHP Library :: Examples :: <a href="tariff.php">Tariff</a> | <a href="tariffs.php">Tariffs</a> | <a href="price.php">Price</a> | <a href="calculate.php">Calculate</a> | <a href="accountManager.php">Accounts</a></h3>
	<h2>Get Tariff Example</h2>
	<form id="tariffForm" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<div class="inputBlock">
			<label for="tariffId">Master Tariff Id</label>
			<input type="text" id="tariffId" name="tariff" value="<?=$TARIFF_ID?>"/>
			<a href="#toggleTariffList">tariff cheat sheet</a>
		</div>
		<div id="tariff_list">
			<a class="close" href="#toggleTariffList">x close</a>
			<div class="lse">
				<p class="lse_name">Residential </p>
				<p class="lse_tariff"><a href="#" onClick="setTariff('706'); return false;">684</a> - Residence Service (PECO Energy- PA)</p>
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
			<label for="territoryId">Territory Id(Optional)</label>
			<input id="territoryId" type="text" name="territoryId" value="<?=$TERRITORY_ID?>"/>
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
		<button type="submit">Get Tariff!</button>
	</form>

	<p><a id="toggleResponse" href="#">view/hide response</a></p>
	<div id="json_resp"><?=var_dump(json_decode($output, true));?></div>

<?
	$tariffsArr = json_decode($output, true);

	if ($tariffsArr["status"] == "success") {
		foreach ($tariffsArr["results"] as $results) { ?>
		<div class="tariff_info">
			<table class="tariff_rates">
				<thead>
					<tr>
						<th>Tariff Id</th>
						<th>Master Tariff Id</th>
						<th>Lse Id</th>
						<th>Lse Name</th>
						<th>Tariff Code</th>
						<th>Tariff Name</th>
						<th>Tariff Type</th>
						<th>Effective Date</th>
						<th>End Date</th>
						<th>Billing Period</th>
                        			<th>TimeZone</th>
						
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?=$results["tariffId"]?></td>
						<td><?=$results["tariffId"]?></td>
						<td><?=$results["lseId"]?></td>
						<td><?=$results["lseName"]?></td>
						<td><?=$results["tariffCode"]?></td>
						<td><?=$results["tariffName"]?></td>
						<td><?=$results["tariffType"]?></td>
						<td><?=date("D M j, Y g:i a", strtotime($results["effectiveDate"]))?> </td>
						<td><?if (is_null($results["endDate"])) {?> <p><em></em><p>
						<?} else {?>
						<?=date("D M j, Y g:i a", strtotime($results["endDate"]))?>
						<?}?>
						</td>
						<td><?=ucwords(strtolower(str_replace("_", " ", $results["billingPeriod"])))?></td>
						<td><?=$results["timeZone"]?></td>
					</tr>
				</tbody>
			</table>
			<table class="tariff_rates">
					<thead>
						<tr>
							<th>Rate Description</th>
							<th>Amount</th>
							<th>Id</th>
							<th>Band Id</th>
							<th>Unit</th>
							<th>Applies To</th>
							<th>Applicability Value</th>
							<th>Territory Id</th>
							<th>Credit?</th>
							<th>Charge Period</th>
							<th>Charge Type</th>
						</tr>
					</thead>
					<tbody>
					<?$prevRateName="";?>
					<?foreach ($results["rates"] as $rate) {?>
					<tr>
					<?if (($prevRateName=="") || ($prevRateName!=$rate["rateGroupName"])&& (count($rate["rateBands"])>0)){ ?>
					<td colspan="18" class="group_name"><?=$rate["rateGroupName"]?></td>
					<?$prevRateName=$rate["rateGroupName"];?>
					<?}else{?>
					
					<?}?>
					</tr>
					<?if (count($rate["rateBands"])== 1) {?>
					<tr>
					<td>
					<?=$rate["rateName"]?>
					<?if( $rate["applicabilityKey"] != NULL){ ?><br/>
					<span class="applicability">Applicability: <?=$rate["applicabilityKey"]?>-
					<?=$rate["rateBands"][0]["applicabilityValue"]?></span>
					<?}?>
					</td>
					
					<td>
					<?if($rate["rateBands"][0]["isCredit"]=="true"){?>
					<?if( $rate["chargeType"] != "CONSUMPTION_BASED"){ ?>
						<?if( $rate["rateBands"][0]["rateUnit"]=="PERCENTAGE"){?>
							<?=(($rate["rateBands"][0]["rateAmount"]*(-1))/100)?>
						<? }else{?>
							$<?=$rate["rateBands"][0]["rateAmount"]*(-1)?>
						<?}?>
					<?}elseif($rate["rateBands"][0]["rateUnit"]=="PERCENTAGE"){?>
							<?=$rate["rateBands"][0]["rateAmount"]*(-1)?>
					<?}else{?>
						$<?=($rate["rateBands"][0]["rateAmount"]*(-1))/100?>
					<?}?>
					<?}else{?>
					<?if( $rate["chargeType"] != "CONSUMPTION_BASED"){ ?>
						<?if( $rate["rateBands"][0]["rateUnit"]=="PERCENTAGE"){?>
							<?=($rate["rateBands"][0]["rateAmount"]/100)?>
						<? }else{?>
							$<?=$rate["rateBands"][0]["rateAmount"]?>
						<?}?>
					<?}elseif($rate["rateBands"][0]["rateUnit"]=="PERCENTAGE"){?>
						<?=$rate["rateBands"][0]["rateAmount"]?>
					<?}else{?>
						$<?=$rate["rateBands"][0]["rateAmount"]/100?>
					<?}?>
					<?}?>
					</td>
					<td><?=$rate["tariffRateId"]?></td>
					<td><?=$rate["rateBands"][0]["tariffRateBandId"]?></td>
					<td><?=formatText($rate["rateBands"][0]["rateUnit"])?></td>
					
					<td><?=$rate["applicabilityKey"]?></td>
					<td><?=$rate["rateBands"][0]["applicabilityValue"]?></td>
					<td><?=$rate["territoryId"]?></td>
					<td><?=$rate["rateBands"][0]["isCredit"]?></td>
					<td class="chargePeriod"><?=$rate["chargePeriod"]?></td>	
					<td><?=formatText($rate["chargeType"])?></td>
					</tr>
					<?} else {?>
					<?if ((count($rate["rateBands"])>0)){ ?>
					<tr>
					<td colspan="18" class="rateName"><?=$rate["rateName"]?></td>
					</tr>
					<?}?>
					<?$prevConsumptionUpperLimit="0";
                                          $prevDemandUpperLimit="0";
					?>
					<?foreach ($rate["rateBands"] as $rateBand) {?>
					<tr>
					<?if(($rateBand["hasConsumptionLimit"]=="true") && empty($rateBand["consumptionUpperLimit"])){?>
					<td>Above<?= $prevConsumptionUpperLimit?>kWh</td>
					<? $prevConsumptionUpperLimit=$rateBand["consumptionUpperLimit"] ?>
					<?}elseif($rateBand["hasConsumptionLimit"]=="true"){?>
					<td><?= $prevConsumptionUpperLimit?>-<?=$rateBand["consumptionUpperLimit"]?> kWh</td>
					<? $prevConsumptionUpperLimit=$rateBand["consumptionUpperLimit"] ?>
					<?}elseif(($rateBand["hasDemandLimit"]=="true")&&(empty($rateBand["demandUpperLimit"]))){?>
						<td>Above <?= $prevDemandUpperLimit?> kW</td>
						<? $prevDemandUpperLimit=$rateBand["demandUpperLimit"] ?>
					<?}elseif($rateBand["hasDemandLimit"]=="true"){?>
						<td><?=$prevDemandUpperLimit?>-<?=$rateBand["demandUpperLimit"]?> kW</td>
						<? $prevDemandUpperLimit=$rateBand["demandUpperLimit"] ?>
					<?}else{?>
						<td>&nbsp;</td>
					<?}?>			
					<td>
					<?if($rateBand["isCredit"]=="true"){?>
					<?if( $rate["chargeType"] != "CONSUMPTION_BASED"){ ?>
						$<?=($rateBand["rateAmount"]*(-1))?>
					<?}elseif($rateBand["rateUnit"]=="PERCENTAGE"){?>
						<?=$rateBand["rateAmount"]*(-1)?>
					<?}else{?>
						$<?=$rateBand["rateAmount"]*(-1)/100?>
					<?}?>					
					<?}else{?>						
					<?if( $rate["chargeType"] != "CONSUMPTION_BASED"){ ?>
						$<?=($rateBand["rateAmount"])?>
					<?}elseif($rateBand["rateUnit"]=="PERCENTAGE"){?>
						<?=$rateBand["rateAmount"]?>
					<?}else{?>
						$<?=$rateBand["rateAmount"]/100?>
					<?}?>
					<?}?>
					</td>
					<td><?=$rate["tariffRateId"]?></td>
					<td><?=$rateBand["tariffRateBandId"]?></td>
					<td><?=formatText($rate["rateBands"][0]["rateUnit"])?></td>
					<td><?=$rate["applicabilityKey"]?></td>
					<td><?=$rateBand["applicabilityValue"]?></td>
					<td><?=$rate["territoryId"]?></td>
					<td><?=$rateBand["isCredit"]?></td>
					<td class="chargePeriod"><?=$rate["chargePeriod"]?></td>	
					<td><?=formatText($rate["chargeType"])?></td>
					</tr>
					<?}?>	
					<?}?>	
					<?}?>	
					</tbody>		
				</table>
		</div>
		<?}
	} elseif ($tariffsArr["type"] == "Error" && $tariffsArr["results"][0]["objectName"] == "requestSignature") { ?>
		<p class="error">Please enter a valid app id and app key, you can grab a pair here: <a href="https://developer.genability.com/admin/applications">https://developer.genability.com/admin/applications</a>
	<?} else {
	echo '<p class="error">Error occured in getTariff() method :<br/><br/>';
	foreach ($tariffsArr["results"] as $errors) {
		echo $errors['objectName'] . ' ' . $errors['code'] . '  ' . $errors['message'] . '<br/>';
	}
	echo '</p>';
}?>
</div>
</body>
</html>
