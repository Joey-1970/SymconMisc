<?
    // Klassendefinition
    class IPS2GPIO_SDS011 extends IPSModule 
    {
	public function Destroy() 
	{
		//Never delete this line!
		parent::Destroy();
		$this->SetTimerInterval("Messzyklus", 0);
	}
	    
	// Überschreibt die interne IPS_Create($id) Funktion
        public function Create() 
        {
            	// Diese Zeile nicht löschen.
            	parent::Create();
            	$this->RegisterPropertyBoolean("Open", false);
		$this->RegisterPropertyInteger("Pin_RxD", -1);
		$this->SetBuffer("PreviousPin_RxD", -1);
		$this->RegisterPropertyInteger("Pin_TxD", -1);
		$this->SetBuffer("PreviousPin_TxD", -1);

            	$this->ConnectParent("{ED89906D-5B78-4D47-AB62-0BDCEB9AD330}");
		
		// Statusvariablen anlegen

        }
	
	public function GetConfigurationForm() 
	{ 
		$arrayStatus = array(); 
		$arrayStatus[] = array("code" => 101, "icon" => "inactive", "caption" => "Instanz wird erstellt"); 
		$arrayStatus[] = array("code" => 102, "icon" => "active", "caption" => "Instanz ist aktiv");
		$arrayStatus[] = array("code" => 104, "icon" => "inactive", "caption" => "Instanz ist inaktiv");
		$arrayStatus[] = array("code" => 200, "icon" => "error", "caption" => "Pin wird doppelt genutzt!");
		$arrayStatus[] = array("code" => 201, "icon" => "error", "caption" => "Pin ist an diesem Raspberry Pi Modell nicht vorhanden!"); 
		
		$arrayElements = array(); 
		$arrayElements[] = array("type" => "CheckBox", "name" => "Open", "caption" => "Aktiv"); 
		$arrayElements[] = array("type" => "Label", "label" => "Angabe der GPIO-Nummer (Broadcom-Number)"); 
  		
		$arrayOptions = array();
		$GPIO = array();
		$GPIO = unserialize($this->Get_GPIO());
		If ($this->ReadPropertyInteger("Pin_RxD") >= 0 ) {
			$GPIO[$this->ReadPropertyInteger("Pin_RxD")] = "GPIO".(sprintf("%'.02d", $this->ReadPropertyInteger("Pin_RxD")));
		}
		ksort($GPIO);
		foreach($GPIO AS $Value => $Label) {
			$arrayOptions[] = array("label" => $Label, "value" => $Value);
		}
		$arrayElements[] = array("type" => "Select", "name" => "Pin_RxD", "caption" => "GPIO-Nr. RxD", "options" => $arrayOptions );
		
		$arrayOptions = array();
		$GPIO = array();
		$GPIO = unserialize($this->Get_GPIO());
		If ($this->ReadPropertyInteger("Pin_TxD") >= 0 ) {
			$GPIO[$this->ReadPropertyInteger("Pin_TxD")] = "GPIO".(sprintf("%'.02d", $this->ReadPropertyInteger("Pin_TxD")));
		}
		ksort($GPIO);
		foreach($GPIO AS $Value => $Label) {
			$arrayOptions[] = array("label" => $Label, "value" => $Value);
		}
		$arrayElements[] = array("type" => "Select", "name" => "Pin_TxD", "caption" => "GPIO-Nr. TxD", "options" => $arrayOptions );
				
		
		$arrayActions = array();
		If ($this->ReadPropertyBoolean("Open") == true) {
					}
		else {
			$arrayActions[] = array("type" => "Label", "label" => "Diese Funktionen stehen erst nach Eingabe und Übernahme der erforderlichen Daten zur Verfügung!");
		}
		
 		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements, "actions" => $arrayActions)); 		 
 	}      
	    
        // Überschreibt die intere IPS_ApplyChanges($id) Funktion
        public function ApplyChanges() 
        {
	        // Diese Zeile nicht löschen
	      	parent::ApplyChanges();
		If ( ( intval($this->GetBuffer("PreviousPin_RxD")) <> $this->ReadPropertyInteger("Pin_RxD") ) OR ( intval($this->GetBuffer("PreviousPin_TxD")) <> $this->ReadPropertyInteger("Pin_TxD") ) ) {
			$this->SendDebug("ApplyChanges", "Pin-Wechsel RxD - Vorheriger Pin: ".$this->GetBuffer("PreviousPin_RxD")." Jetziger Pin: ".$this->ReadPropertyInteger("Pin_RxD"), 0);
			$this->SendDebug("ApplyChanges", "Pin-Wechsel TxD - Vorheriger Pin: ".$this->GetBuffer("PreviousPin_TxD")." Jetziger Pin: ".$this->ReadPropertyInteger("Pin_TxD"), 0);
		}
		
        	If ((IPS_GetKernelRunlevel() == 10103) AND ($this->HasActiveParent() == true)) {
			// den Handle für dieses Gerät ermitteln
			If (($this->ReadPropertyInteger("Pin_RxD") >= 0) AND ($this->ReadPropertyInteger("Pin_TxD") >= 0) AND ($this->ReadPropertyBoolean("Open") == true) ) {
				$Result = $this->SendDataToParent(json_encode(Array("DataID"=> "{A0DAAF26-4A2D-4350-963E-CC02E74BD414}", "Function" => "open_bb_serial_sds011", "Baud" => 9600, "Pin_RxD" => $this->ReadPropertyInteger("Pin_RxD"), "PreviousPin_RTxD" => $this->GetBuffer("PreviousPin_RxD"), "Pin_TxD" => $this->ReadPropertyInteger("Pin_TxD"), "PreviousPin_TxD" => $this->GetBuffer("PreviousPin_TxD"), "InstanceID" => $this->InstanceID )));
				$this->SetBuffer("PreviousPin_RxD", $this->ReadPropertyInteger("Pin_RxD"));
				$this->SetBuffer("PreviousPin_TxD", $this->ReadPropertyInteger("Pin_TxD"));
				$this->SetStatus(102);
			}
			else {
				$this->SetStatus(104);
			}
		}
        }
	public function RequestAction($Ident, $Value) 
	{
  		switch($Ident) {
	       
	        default:
	            throw new Exception("Invalid Ident");
	    	}
	}
	
	public function ReceiveData($JSONString) 
	{
		$data = json_decode($JSONString);
	 	switch ($data->Function) {
			 case "set_serial_SDS011_data":
				$ByteMessage = utf8_decode($data->Value);
				$this->SendDebug("ReceiveData", "Ankommende Daten: ".$ByteMessage, 0);
				
				break;
			 case "get_serial":
			   	$this->ApplyChanges();
				break;
			 case "status":
			   	If (($data->Pin == $this->ReadPropertyInteger("Pin_RxD")) OR ($data->Pin == $this->ReadPropertyInteger("Pin_TxD"))) {
			   		$this->SetStatus($data->Status);
			   	}
			   	break;
	 	}
 	}
	// Beginn der Funktionen
	public function Send(String $Message)
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("Send", "Ausfuehrung", 0);
			$Message = utf8_encode(chr(2).$Message.chr(3));
			//$Message = utf8_encode($Message);
			$this->SendDataToParent(json_encode(Array("DataID"=> "{A0DAAF26-4A2D-4350-963E-CC02E74BD414}", "Function" => "write_bb_bytes_serial", "Baud" => 9600, "Pin_TxD" => $this->ReadPropertyInteger("Pin_TxD"), "Command" => $Message)));
		}
	}
	
	public function GetStatus()
	{
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SendDebug("GetStatus", "Ausfuehrung", 0);
			$this->Send('Q$S');
		}
	}				
	    
	private function RegisterProfileInteger($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
	{
	        if (!IPS_VariableProfileExists($Name))
	        {
	            IPS_CreateVariableProfile($Name, 1);
	        }
	        else
	        {
	            $profile = IPS_GetVariableProfile($Name);
	            if ($profile['ProfileType'] != 1)
	                throw new Exception("Variable profile type does not match for profile " . $Name);
	        }
	        IPS_SetVariableProfileIcon($Name, $Icon);
	        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
	        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);        
	}    
	    
	private function Get_GPIO()
	{
		If ($this->HasActiveParent() == true) {
			$GPIO = $this->SendDataToParent(json_encode(Array("DataID"=> "{A0DAAF26-4A2D-4350-963E-CC02E74BD414}", "Function" => "get_GPIO")));
		}
		else {
			$AllGPIO = array();
			$AllGPIO[-1] = "undefiniert";
			for ($i = 2; $i <= 27; $i++) {
				$AllGPIO[$i] = "GPIO".(sprintf("%'.02d", $i));
			}
			$GPIO = serialize($AllGPIO);
		}
	return $GPIO;
	}
	    
	private function HasActiveParent()
    	{
		$Instance = @IPS_GetInstance($this->InstanceID);
		if ($Instance['ConnectionID'] > 0)
		{
			$Parent = IPS_GetInstance($Instance['ConnectionID']);
			if ($Parent['InstanceStatus'] == 102)
			return true;
		}
        return false;
    	}  
	
}
?>
