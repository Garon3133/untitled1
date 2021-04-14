<?php
class ControllerPrilTas extends Controller {
	public function index() {
		
		require_once 'function.php';

		
		$this->load->model('pril/allow');
		
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
										$CustomersIds .= $DecodedCustomersId[$ik]; // userside
									}
								}
							}
						}
					}
				}
			}
			print_r($CustomersIds);
	}
}
