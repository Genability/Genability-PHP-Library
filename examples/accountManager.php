<?
/** include the Genability PHP Library */
require_once('../genability.php');

// set your app id and app key
$gen = new genability(array(
  'app_id'  => '49c67ab6',    // Your Unique Genability Application ID <https://developer.genability.com/admin/applications> 0e37899c
  'app_key' => '11726b385d8cb61afbd749d681c5774e',   // Your Unique Genability Application Key <https://developer.genability.com/admin/applications>
  'debug'   => true,                // Debug mode echos API Url & POST data if set to true (Optional)
));

if ($_POST) {
	//$addAccount - $gen->addAccount($_POST);
	print_r($_POST);
	switch ($_POST["action"]) {
		case "addAccount":
			$gen->addAccount($_POST);
			break;
		case "updateAccount":
			$gen->updateAccount($_POST);
			break;
		case "deleteAccount":
			$gen->deleteAcount($_POST["accountId"]);
			break;
		case "addProfile":
			$gen->addProfile($_POST);
			break;
		case "getProfile":
			$gen->getProfile($_POST);
			break;
		case "uploadReadings":
			$gen->uploadReadings($_POST);
			break;
	}
}

if ($_GET["accountId"]) {
	$account = $gen->getAccount($_GET["accountId"]);
	$account = json_decode($account, true);
	
	$profiles = $gen->getProfiles($_GET["accountId"]);
	$profiles = json_decode($profiles, true);
} elseif ($_GET["profileId"]) {
	$profile = $gen->getProfile($_GET);
print_r($profile);
	$profile = json_decode($profile, true);
} else {
	$accounts = $gen->getAccounts();
	$accounts = json_decode($accounts, true);

	$profiles = $gen->getProfiles();
	$profiles = json_decode($profiles, true);
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Genability API PHP Library :: Examples :: accountManager.php</title>
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
		<p><a href="https://developer.genability.com/documentation/api-reference/storage/account" target="_blank">Account Documentation</a></p>
	</div>
	<h3 class="nav">Genability API PHP Library :: Examples :: <a href="tariff.php">Tariff</a> | <a href="tariffs.php">Tariffs</a> | <a href="price.php">Price</a> | <a href="calculate.php">Calculate</a></h3>
	<h2>Account Manager</h2>

<?
if ($accounts["status"] == "success") { ?>
<form action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST">
<fieldset>
	<legend>Add A New Account</legend>
	<input type="hidden" name="action" value="addAccount"/>
<div class="inputBlock">
	<label for="providerOrgId">providerOrgId</label>
	<input name="providerOrgId" id="providerOrgId" value="ed0c24c4-045e-11e1-a79a-002354cc3b2e"/>
</div>
<div class="inputBlock">
	<label for="accountName">accountName</label>
	<input name="accountName" id="accountName"/>
</div>
<div class="inputBlock">
	<label for="customerOrgName">customerOrgName</label>
	<input name="customerOrgName" id="customerOrgName"/>
</div>

<div class="inputBlock">
	<label for="providerAccountId">providerAccountId</label>
	<input name="providerAccountId" id="providerAccountId"/>
</div>
<div class="inputBlock">
	<input type="submit" value="Add New Account"/>
</div>
</fieldset>
</form>
<?	foreach ($accounts["results"] as $account) {
		if ($account["accountId"]) {
			echo '<div class="account"><a href="accountManager.php?accountId=' . $account["accountId"] . '">';
			if ($account["accountName"]) echo $account["accountName"];
				else echo $account["accountId"];
			echo '</a>';
			if ($account["customerOrgName"]) echo 'customerOrgName: ' . $account["customerOrgName"];
				elseif ($account["customerOrgId"]) echo 'customerOrgId: ' .  $account["customerOrgId"];
			if ($account["providerAccountId"]) echo 'providerAccountId:' . $account["providerAccountId"];
echo '<form name="deleteAccount" action="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'" method="POST">
	<input type="hidden" name="action" value="deleteAccount"/>
	<input type="hidden" name="accountId" value="'.$account["accountId"].'">
	<input type="submit" value="Delete"/>
</form>';
			echo '<br/>';
			if ($account["properties"]) {
				echo '<strong>Account Properties:</strong><p class="accountProperties">';
				foreach ($account["properties"] as $property) {
					echo $property["keyName"] . ': ' . $property["dataValue"] . '<br/>';
				}
				echo '</p>';
			}
			echo '</div>';
		}
	}
} elseif ($account["status"] == "success") {
	$account = $account["results"][0];
	if ($account["accountId"]) {?>
<form action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST">
<fieldset>
	<legend>Account Id: <?=$account["accountId"]?></legend>
	<input type="hidden" name="action" value="updateAccount"/>
	<input type="hidden" name="accountId" value="<?=$account["accountId"]?>"/>
<div class="inputBlock">
	<label for="accountName">accountName</label>
	<input name="accountName" id="accountName" value="<?=$account["accountName"]?>"/>
</div>
<div class="inputBlock">
	<label for="customerOrgName">customerOrgName</label>
	<input name="customerOrgName" id="customerOrgName" value="<?=$account["customerOrgName"]?>"/>
</div>
<div class="inputBlock">
	<label for="customerOrgId">customerOrgId</label>
	<input name="customerOrgId" id="customerOrgId" value="<?=$account["customerOrgId"]?>"/>
</div>
<div class="inputBlock">
	<label for="providerAccountId">providerAccountId</label>
	<input name="providerAccountId" id="providerAccountId" value="<?=$account["providerAccountId"]?>"/>
</div>
<div class="inputBlock">
	<input type="submit" value="Update"/>
</div>
</fieldset>
</form>

<form action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST">
	<input type="hidden" name="action" value="deleteAccount"/>
	<input type="hidden" name="accountId" value="<?=$account["accountId"]?>">
	<input type="submit" value="Delete"/>
</form>
			<?echo '<br/>';
			if ($account["properties"]) {
				echo '<strong>Account Properties:</strong><p class="accountProperties">';
				foreach ($account["properties"] as $property) {
					echo $property["keyName"] . ': ' . $property["dataValue"] . '<br/>';
				}
				echo '</p>';
			}
			echo '</div>';?>
<form action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST">
<fieldset>
	<legend>Add Usage Profile</legend>
	<input type="hidden" name="action" value="addProfile"/>
	<input type="hidden" name="accountId" value="<?=$account["accountId"]?>"/>
<div class="inputBlock">
	<label for="profileName">profileName</label>
	<input name="profileName" id="profileName"/>
</div>
<div class="inputBlock">
	<label for="description">description</label>
	<textarea name="description" id="description"></textarea>
</div>
<div class="inputBlock">
	<input type="submit" value="Add"/>
</div>
</fieldset>
</form>
	<? }
} elseif ($profile["status"] == "success") { ?>
<form action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST">
<fieldset>
	<legend>Get Usage Profile</legend>
	<input type="hidden" name="action" value="getProfile"/>
	<input type="hidden" name="profileId" value="<?=$account["profileId"]?>"/>
<div class="inputBlock">
	<label for="populateUsageData">populateUsageData</label>
	<input type="checkbox" name="populateUsageData" id="populateUsageData"/>
</div>
<div class="inputBlock">
	<label for="fromDateTime">fromDateTime</label>
	<input name="fromDateTime" id="fromDateTime"/>
</div>
<div class="inputBlock">
	<label for="toDateTime">fromDateTime</label>
	<input name="toDateTime" id="toDateTime"/>
</div>
<div class="inputBlock">
	<label for="groupBy">groupBy</label>
	<select name="groupBy" id="groupBy">
		<option value=""></option>
		<option value="YEAR">YEAR</option>
		<option value="MONTH">MONTH</option>
		<option value="DAY">DAY</option>
		<option value="HOUR">HOUR</option>
		<option value="QTRHOUR">QTRHOUR</option>
	</select>
</div>
<div class="inputBlock">
	<label for="groupBy">clipBy</label>
	<select name="clipBy" id="clipBy">
		<option value=""></option>
		<option value="OUTER">OUTER</option>
		<option value="INNER">INNER</option>
	</select>
</div>
<div class="inputBlock">
	<input type="submit" value="Get"/>
</div>
</fieldset>
</form>
<?	foreach ($profile["results"] as $profile) {
		echo $profile["profileId"] . '<br/>';
		echo $profile["profileName"] . '<br/>';
		echo $profile["accountId"] . '<br/>';
		echo $profile["description"] . '<br/>';
		echo $profile["sourceId"] . '<br/>';
		if (sizeof($profile["readingDataSummaries"]) > 0) {
		foreach ($profile["readingDataSummaries"] as $rd) {
			echo $rd["fromDateTime"] . ' - ' . $rd["toDateTime"] . ': ' . $rd["numberOfReadings"] . ' ' . $rd["quantityUnit"] . ' readings<br/>';
		}
		}
	} ?>

<form action="<?=$_SERVER['PHP_SELF']?>?<?=$_SERVER['QUERY_STRING']?>" method="POST" enctype="multipart/form-data">
<fieldset>
	<legend>Upload Reading Data</legend>
	<input type="hidden" name="action" value="uploadReadings"/>
	<input type="hidden" name="profileId" value="<?=$profile["profileId"]?>"/>
	<input type="hidden" name="fileFormat" value="espi"/>
<div class="inputBlock">
	<label for="fileData">file</label>
	<input type="file" name="fileData" id="fileData"/>
</div>
<div class="inputBlock">
	<input type="submit" value="Upload"/>
</div>
</fieldset>
</form>

<? } else { ?>
Please connect your appId to Genability's Explorer.
<?}?>

<? if ($profiles["status"] == "success") {
	echo 'Profiles:<br/>';
		foreach ($profiles["results"] as $profile) {
			if ($profile["profileName"]) {
				echo '<a href="accountManager.php?profileId='.$profile["profileId"].'">' . $profile["profileName"] . '</a> (<a href="accountManager.php?profileId='.$profile["profileId"].'">' . $profile["profileId"] . '</a>)<br/>';
			} else {
				echo '<a href="accountManager.php?profileId='.$profile["profileId"].'">' . $profile["profileId"] . '</a><br/>';
			}
		}
	}?>

</div>
</body>
</html>
