<?php
class ControllerPrilNotificationStartForAllCompany extends Controller{
	public function index() {
		require_once 'function.php';
		//$Start = '1';
		foreach (object_to_array(retrieveJsonPostData()) as $pril_key => $pril_value) {
			switch ($pril_key) {
				case 'apiKey':
					$apiKey = $pril_value;
					break;
				case 'userSide':
					$userSide = $pril_value;
					break;
				case 'companyId':
					$companyId = $pril_value;
					break;
				case 'Start':
					$Start = $pril_value;
					break;
			}
		}
		$this->load->model('pril/allow');
		if($Start == '0'){
			$Column = "customer_id";
			$server = $this->model_pril_allow->getCompanyInfo($Column);
			$CustomersIds = "";
			foreach($server as $element1){
				foreach($element1 as $customerId){
					$CustomersIds .= "◄".$customerId."►";
					$Column = "custom_field";
					$server2 = $this->model_pril_allow->getCompanyCustomField($customerId);
					foreach($server2 as $element){
						foreach($element as $customerInfo){
							$DecodedCustomersId = json_decode($customerInfo, true);
							for($ik = 0; $ik < 15; $ik++){
								if($ik == 1){
									if($DecodedCustomersId[$ik] != ''){
										$CustomersIds .= $DecodedCustomersId[$ik]."►"; // apikey
									}else{
										$CustomersIds .= "►";
									}
								}else if($ik == 2){
									if($DecodedCustomersId[$ik] != ''){
										$CustomersIds .= $DecodedCustomersId[$ik]."►"; // userside
									}else{
										$CustomersIds .= "►";
									}
								}
							}
						}
					}
				}
			}
			print_r($CustomersIds);
		}
		/*$apiKey = '1597535';
		$userSide = "https://user.intronex.ru";
		$companyId = '3';*/
		if($Start == '1' && $apiKey != '' && $userSide != '' && $companyId != ''){
			$companyData = array(
				'key'         => $apiKey,
				'userside'    => $userSide,
				'customerId'  => $companyId
			);
			$this->load->controller('pril/NotificationTask', $companyData);
			$this->load->controller('pril/NotificationComm', $companyData);
		}else{
			print_r(" Pusto da ");
		}
	}
}
?>