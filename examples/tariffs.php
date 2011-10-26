<?
/** include the Genability PHP Library */
require_once('../genability.php');

// set your app id and app key
$gen = new genability(array(
  'app_id'  => 'your-app-id-here',    // Your Unique Genability Application ID <https://developer.genability.com/admin/applications>
  'app_key' => 'your-app-key-here',   // Your Unique Genability Application Key <https://developer.genability.com/admin/applications>
  'debug'   => false,                // Debug mode echos API Url & POST data if set to true (Optional)
));

if ($_POST) {
	// make the getTariffs call
	$output = $gen->getTariffs(array(
		'search'	=>	$_POST['search'],
		'zipCode'	=>	$_POST['zipCode'],
		'tariffTypes'	=>	$_POST['tariffTypes'],
		'sortOn'	=>	$_POST['sortOn']
	));
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Genability API PHP Library :: Examples :: tariffs.php</title>
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
		<p><a href="https://developer.genability.com/documentation/api-reference/public/tariff#getTariffs" target="_blank">Get Tariffs Documentation</a></p>
	</div>
	<h3 class="nav">Genability API PHP Library :: Examples :: <a href="tariff.php">Tariff</a> | <a href="tariffs.php">Tariffs</a> | <a href="price.php">Price</a> | <a href="calculate.php">Calculate</a></h3>
	<h2>Get Tariffs Example</h2>
	<form id="tariffForm" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
		<div class="inputBlock">
			<label for="search">Search</label>
			<input type="text" id="search" name="search" value="<?=$_POST['search']?>"/>
		</div>
		<div class="inputBlock">
			<label for="zipCode">Zip Code</label>
			<input type="text" id="zipCode" name="zipCode" value="<?=$_POST['zipCode']?>"/>
		</div>
		<div class="inputBlock">
			<label>Tariff Types</label>
			<input type="checkbox" name="tariffTypes[]" value="DEFAULT" id="default"<?if ($_POST) foreach($_POST['tariffTypes'] as $option) { if ($option == 'DEFAULT') echo ' checked'; }?>><label for="default" class="cb">Default</label>
			<input type="checkbox" name="tariffTypes[]" value="ALTERNATIVE" id="alternative"<?if ($_POST) foreach($_POST['tariffTypes'] as $option) { if ($option == 'ALTERNATIVE') echo ' checked'; }?>><label for="alternative" class="cb">Alternative</label>
			<input type="checkbox" name="tariffTypes[]" value="OPTIONAL_EXTRA" id="optional_extra"<?if ($_POST) foreach($_POST['tariffTypes'] as $option) { if ($option == 'OPTIONAL_EXTRA') echo ' checked'; }?>><label for="optional_extra" class="cb">Optional Extra</label>
			<input type="checkbox" name="tariffTypes[]" value="RIDER" id="rider"<?if ($_POST) foreach($_POST['tariffTypes'] as $option) { if ($option == 'RIDER') echo ' checked'; }?>><label for="rider" class="cb">Rider</label>
		</div>
		<div class="inputBlock">
			<label>Sort On</label>
			<input type="checkbox" name="sortOn[]" value="masterTariffId" id="sort_masterTariffId"<?if ($_POST) foreach($_POST['sortOn'] as $option) { if ($option == 'masterTariffId') echo ' checked'; }?>><label for="sort_masterTariffId" class="cb">Master Tariff Id</label>
			<input type="checkbox" name="sortOn[]" value="lseId" id="sort_lseId"<?if ($_POST) foreach($_POST['sortOn'] as $option) { if ($option == 'lseId') echo ' checked'; }?>><label for="sort_lseId" class="cb">LSE Id</label>
			<input type="checkbox" name="sortOn[]" value="lseName" id="sort_lseName"<?if ($_POST) foreach($_POST['sortOn'] as $option) { if ($option == 'lseName') echo ' checked'; }?>><label for="sort_lseName" class="cb">LSE Name</label>
			<input type="checkbox" name="sortOn[]" value="tariffCode" id="sort_tariffCode"<?if ($_POST) foreach($_POST['sortOn'] as $option) { if ($option == 'tariffCode') echo ' checked'; }?>><label for="sort_tariffCode" class="cb">Tariff Code</label>
			<input type="checkbox" name="sortOn[]" value="tariffName" id="sort_tariffName"<?if ($_POST) foreach($_POST['sortOn'] as $option) { if ($option == 'tariffName') echo ' checked'; }?>><label for="sort_tariffName" class="cb">Tariff Name</label>
			<input type="checkbox" name="sortOn[]" value="tariffType" id="sort_tariffType"<?if ($_POST) foreach($_POST['sortOn'] as $option) { if ($option == 'tariffType') echo ' checked'; }?>><label for="sort_tariffType" class="cb">Tariff Type</label>
		</div>
		<button type="submit">Get Tariffs!</button>
	</form>

<? if ($_POST) { ?>

	<p><a id="toggleResponse" href="#">view/hide response</a></p>
	<div id="json_resp"><?=var_dump(json_decode($output, true));?></div>

<?
	$tariffsArr = json_decode($output, true);

	if ($tariffsArr["status"] == "success" && sizeof($tariffsArr["results"]) > 0) { ?>
		<div class="tariff_info">
			<table class="tariff_rates">
				<thead>
					<tr>
						<th>Master Tariff Id</th>
						<th>Lse Id</th>
						<th>Lse Name</th>
						<th>Tariff Code</th>
						<th>Tariff Name</th>
						<th>Tariff Type</th>
						
					</tr>
				</thead>
				<tbody>
				<? foreach ($tariffsArr["results"] as $results) { ?>
					<tr>
						<td><?=$results["masterTariffId"]?></td>
						<td><?=$results["lseId"]?></td>
						<td><?=$results["lseName"]?></td>
						<td><?=$results["tariffCode"]?></td>
						<td><?=$results["tariffName"]?></td>
						<td><?=$results["tariffType"]?></td>
					</tr>
				<? } ?>
				</tbody>
			</table>
		</div>
	<? } elseif ($tariffsArr["status"] == "success") { ?>
		<p class="error">No Tariffs Found!</p>
	<? } elseif ($tariffsArr["type"] == "Error" && $tariffsArr["results"][0]["objectName"] == "requestSignature") { ?>
		<p class="error">Please enter a valid app id and app key, you can grab a pair here: <a href="https://developer.genability.com/admin/applications">https://developer.genability.com/admin/applications</a>
	<?} else {
	echo '<p class="error">Error occured in getTariff() method :<br/><br/>';
	foreach ($tariffsArr["results"] as $errors) {
		echo $errors['objectName'] . ' ' . $errors['code'] . '  ' . $errors['message'] . '<br/>';
	}
	echo '</p>';
}}?>
</div>
</body>
</html>
