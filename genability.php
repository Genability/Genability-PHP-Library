<?php
/**
 * Genability's PHP Library
 * <http://developer.genability.com/>
 * For assistance, e-mail us at <devnet@genability.com>
 * or follow us on Twitter @genabilityapi
 *
 * @author Matthew Fong <mfong@genability.com> @matthewfong
 * @version 0.86
 *
 * Last Updated: October 26, 2011
 */

/**
 * genability
 * Wrapper class to store all the associated variables and
 * make all the calls to the Genability API
 */
class genability {

	/** @var String $GENABILITY_API_URL The base url for the Genability API */
	private $GENABILITY_API_URL = "http://api.genability.com/rest/public/";
	/** @var String $GENABILITY_API_URL_ALTRA The base url for the alpha Genability API */
	private $GENABILITY_API_URL_BETA = "http://api.genability.com/rest/beta/";


	// you can retrieve your APP_ID and APP_KEY from <https://developer.genability.com/admin/applications>
	/** @var String $APP_ID Your Application ID */
	private $APP_ID;
	/** @var String $APP_KEY Your Application Key */
	private $APP_KEY;
	/** @var String $APP_PARAMS This is just your Application ID and Key as paramters for the api url */
	private $API_PARAMS;

	/**
	 * Creates a new genability object
	 *
	 *
	 */
	function __construct($config) {
		$this->params = array();

		// default configuration options
		$this->config = array_merge(
			array(
				'app_id'	=> '',
				'app_key'	=> '',
				'debug'         => '',
			),
			$config
		);

		$this->APP_ID = $this->config['app_id'];
		$this->APP_KEY = $this->config['app_key'];
		$this->API_PARAMS = "?appId=" . $this->APP_ID . "&appKey=" . $this->APP_KEY;
	}


	/**
	 * getTariff
	 *
	 * Get One Tariff
	 * This allows you to retrieve one Tariff
	 * <https://developer.genability.com/documentation/api-reference/public/tariff>
	 */
	function getTariff($params) {
		$url = $this->GENABILITY_API_URL . "tariffs/" . $params['masterTariffId'] . $this->API_PARAMS;
		if ($params['territoryId']) {
			$url .= "&territoryId=" . $params['territoryId'];
		}
		if ($params['populateRates'] == true) {
			$url .= "&populateRates=true";
		} else {
			$url .= "&populateRates=false";
		}
		if ($params['populateProperties'] == false) {
			$url .= "&populateProperties=false";
		} else {
			$url .= "&populateProperties=true";
		}
		if ($params['accountId']) {
			$url .= "&accountId=" . $params['accountId'];
		}

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * getTariffs
	 *
	 * Get a List of Tariffs
	 * This allows you to search for a set of Tariffs and get them back as a list of Tariff objects in the
	 * standard response format.
	 * <https://developer.genability.com/documentation/api-reference/public/tariff#getTariffs>
	 */
	function getTariffs($params) {
		$url = $this->GENABILITY_API_URL . "tariffs/" . $this->API_PARAMS;

		foreach ($params as $key => $value) {
			if ($value != null || $value != '') {
				$url .= "&" . $key . "=";
				if (is_array($value)) {
					for ($i=0; $i < sizeof($value); $i++) {
						if ($i > 0) {
							$url .= ',';
						}
						$url .= $value[$i];
					}
				} else {
					$url .= rawurlencode($value);
				}
			}
		}

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }	

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * getPrice
	 *
	 * Get the Price of a Tariff
	 * This returns the price of the specified tariff for the passed in date and time, and also the changes in
	 * price for this tariff for the next week.
	 * <https://developer.genability.com/documentation/api-reference/pricing/price>
	 */
	function getPrice($params) {
		$url = $this->GENABILITY_API_URL . "prices/" . $params['masterTariffId'] . $this->API_PARAMS
			. "&fromDateTime=" . rawurlencode($params['fromDateTime']);
		
		// optional parameters
		if ($params['toDateTime']) {
			$url .= "&toDateTime=" . $params['toDateTime'];
		}
		if ($params['territoryId']) {
			$url .= "&territoryId=" . $params['territoryId'];
		}
		if ($params['consumption']) {
			$url .= "&consumption=" . $params['consumption'];
		}
		if ($params['demand']) {
			$url .= "&demand=" . $params['demand'];
		}
		if ($params['accountId']) {
			$url .= "&accountId=" . $params['accountId'];
		}

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * getCalculateInputs
	 *
	 * Get Calculate Input meta-data
	 * Calling the URL as a HTTP GET will return the "inputs" required to accurately calculate the cost for a given
	 * period. It essentially gives you the meta-data for what to pass into the calculate method, and as such
	 * provides a template for the payload of the HTTP POST.
	 * <https://developer.genability.com/documentation/api-reference/pricing/calculate>
	 */
	function getCalculateInputs($params) {
		$url = $this->GENABILITY_API_URL_BETA . "calculate/" . $params['tariffId'] . $this->API_PARAMS;

		foreach ($params as $k => $v) {
			if ($v)
				$url .= "&" . rawurlencode($k) . "=" . rawurlencode($v);
		}

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * getCalculation
	 *
	 * Run new Calculation
	 * To run a new calculation, you will POST a payload containing the calculation criteria and inputs, and a
	 * Calculated Cost will be returned. The table below documents the various properties of the payload, and
	 * immediately after the table is an example of the payload itself.
	 * <https://developer.genability.com/documentation/api-reference/pricing/calculate>
	 */
	function getCalculation($params) {
		$url = $this->GENABILITY_API_URL_BETA . "calculate/" . $params['masterTariffId'];

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$data['appId'] = $this->APP_ID;
		$data['appKey'] = $this->APP_KEY;
		$data['fromDateTime'] = $params['fromDateTime'];
		$data['toDateTime'] = $params['toDateTime'];
		if ($params['territoryId']) {
			$data['territoryId'] = $params['territoryId'];
		}
		if ($params['billingPeriod'] == true) {
			$data['billingPeriod'] = true;
		}
		$data['detailLevel'] = $params['detailLevel'];
		$data['tariffInputs'] = $params['tariffInputs'];
		if ($params['accountId']) {
			$data['tariffInputs'][0]['keyName'] = 'accountId';
			$data['tariffInputs'][0]['dataValue'] = $params['accountId'];
		}
		$data = json_encode($data);

		if ($this->config['debug']) { echo $data; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$arr = array();
		array_push($arr, 'Content-Type: application/json; charset=utf-8');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * getPropertyKey
	 *
	 * Get One Property
	 * This allows you to retrieve one Property using its keyname. This is particularly useful when using the
	 * Calculator as it may require you to specify certain applicability values prior to making the calculation.
	 * <https://developer.genability.com/documentation/api-reference/public/property>
	 */
	function getPropertyKey($params) {
		$url = $this->GENABILITY_API_URL . "properties/" . $params['keyName'] . $this->API_PARAMS;

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}




	/**
	 * Accounts
	 */

	/**
	 * getAccount
	 */
	function getAccount($accountId, $providerAccountId=NULL) {
		$url = $this->GENABILITY_API_URL_BETA . "accounts/" . $accountId . $this->API_PARAMS;

		if ($providerAccountId) {
			$url .= "&providerAccountId=" . $providerAccountId;
		}

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * getAccounts
	 */
	function getAccounts($params=NULL) {
		$url = $this->GENABILITY_API_URL_BETA . "accounts/" . $this->API_PARAMS;

		// optional parameters
		if ($params['search']) {
			$url .= "&search=" . $params['toDateTime'];
		}

		if ($params['providerAccountId']) {
			$url .= "&providerAccountId=" . $params['providerAccountId'];
		}

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * deleteAccount
	 */
	function deleteAcount($accountId) {
		$url = $this->GENABILITY_API_URL_BETA . "accounts/" . $accountId . $this->API_PARAMS;

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * addAccount
	 */
	function addAccount($params) {
		$url = $this->GENABILITY_API_URL_BETA . "accounts/" . $this->API_PARAMS;

		foreach($params as $key=>$value) {
			if ($key != "providerOrgId" && $key != "accountName" && $key != "customerOrgName" && $key != "providerAccountId") unset($params[$key]);
			elseif ($value == "" || $value == NULL) unset($params[$key]);
		}
		$params = json_encode($params);

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * updateAccount
	 */
	function updateAccount($params) {
		$url = $this->GENABILITY_API_URL_BETA . "accounts/" . $this->API_PARAMS;

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }
		if ($this->config['debug']) { echo '<strong class="debugFunction">Params:</strong>'; print_r($params); }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * addAccountProperty
	 */
	function addAccountProperty($accountId, $params) {
		$url = $this->GENABILITY_API_URL_BETA . "accounts/" . $accountId . "/properties" . $this->API_PARAMS;

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * updateAccountProperty
	 */
	function updateAccountProperty($accountId, $params) {
		$url = $this->GENABILITY_API_URL_BETA . "accounts/" . $accountId . "/properties" . $this->API_PARAMS;

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * deleteAccountProperty
	 */
	function deleteAcountProperty($accountId, $keyname) {
		$url = $this->GENABILITY_API_URL_BETA . "accounts/" . $accountId . "/properties/" . $keyname . $this->API_PARAMS;

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	/**
	 * Usage Profile
	 */

	function addProfile($params) {
		$url = $this->GENABILITY_API_URL_BETA . "usage/profiles" . $this->API_PARAMS;

		foreach($params as $key=>$value) {
			if ($key != "accountId" && $key != "profileName" && $key != "description") unset($params[$key]);
			elseif ($value == "" || $value == NULL) unset($params[$key]);
		}
		$params = json_encode($params);

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	function getProfiles($accountId = NULL) {
		$url = $this->GENABILITY_API_URL_BETA . "usage/profiles" . $this->API_PARAMS;

		// optional parameter
		if ($accountId) {
			$url .= "&appId=" . $accountId;
		}

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch , CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

	function getProfile($params) {
		$url = $this->GENABILITY_API_URL_BETA . "usage/profiles/" . $params['profileId'] . $this->API_PARAMS;
		if ($params['fromDateTime']) {
			$url .= "&fromDateTime=" . $params['fromDateTime'];
		}
		if ($params['toDateTime']) {
			$url .= "&toDateTime=" . $params['toDateTime'];
		}
		if ($params['populateUsageData'] == false) {
			$url .= "&populateUsageData=false";
		} else {
			$url .= "&populateUsageData=true";
		}
		if ($params['groupBy']) {
			$url .= "&groupBy=" . $params['groupBy'];
		}
		if ($params['clipBy']) {
			$url .= "&clipBy=" . $params['clipBy'];
		}

		if ($this->config['debug']) { echo '<strong class="debugFunction">' . __FUNCTION__ . '</strong>' . $url; }

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		return $result;
	}

}
?>
