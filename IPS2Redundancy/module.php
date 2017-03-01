<?
class IPS2Redundancy extends IPSModule
{
    	// ToDo:
	// - Variable Tagesgruppen
	// - Farbauswahl
	// - Selbstkonfiguration K-Faktoren	
	
	// Überschreibt die interne IPS_Create($id) Funktion
        public function Create() 
        {
            	// Diese Zeile nicht löschen.
            	parent::Create();
		
		$this->RegisterPropertyBoolean("Open", false);
		$this->RegisterPropertyBoolean("System", false);
	    	$this->RegisterPropertyString("IPAddress", "127.0.0.1");
		$this->RegisterPropertyString("IPS_User_primary", "IPS-Benutzername");
		$this->RegisterPropertyString("IPS_Password_primary", "IPS-Passwort");
		$this->RegisterPropertyString("IPS_User_secondary", "IPS-Benutzername");
		$this->RegisterPropertyString("IPS_Password_secondary", "IPS-Passwort");
		
	}
	
	public function GetConfigurationForm() 
	{ 
		$arrayStatus = array(); 
		$arrayStatus[] = array("code" => 101, "icon" => "inactive", "caption" => "Instanz wird erstellt"); 
		$arrayStatus[] = array("code" => 102, "icon" => "active", "caption" => "Instanz ist aktiv");
		$arrayStatus[] = array("code" => 104, "icon" => "inactive", "caption" => "Instanz ist inaktiv");
				
		$arrayElements = array(); 
		$arrayElements[] = array("name" => "Open", "type" => "CheckBox",  "caption" => "Aktiv");
		
		$arrayOptionsSystem = array();
		$arrayOptionsSystem[] = array("label" => "Primärsystem", "value" => 0);
		$arrayOptionsSystem[] = array("label" => "Sekundärsystem", "value" => 1);
		
		$arrayElements[] = array("type" => "Select", "name" => "System", "caption" => "Systembestimmung", "options" => $arrayOptionsSystem );
		If ($this->ReadPropertyBoolean("System") == false) {
			$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
			$arrayElements[] = array("type" => "Label", "label" => "Daten des Primärsystems:");
			$arrayElements[] = array("type" => "Label", "label" => "Daten des IP-Symcon Fernzugriffs:");
			$arrayElements[] = array("name" => "IPS_User_primary", "type" => "ValidationTextBox",  "caption" => "IP-Symcon Benutzername");
			$arrayElements[] = array("name" => "IPS_Password_primary", "type" => "PasswordTextBox",  "caption" => "IP-Symcon Password");
			$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
			$arrayElements[] = array("type" => "Label", "label" => "Daten des Sekundärsystems:");
			$arrayElements[] = array("name" => "IPAddress", "type" => "ValidationTextBox",  "caption" => "IP");
			$arrayElements[] = array("type" => "Label", "label" => "Daten des IP-Symcon Fernzugriffs:");
			$arrayElements[] = array("name" => "IPS_User_secondary", "type" => "ValidationTextBox",  "caption" => "IP-Symcon Benutzername");
			$arrayElements[] = array("name" => "IPS_Password_secondary", "type" => "PasswordTextBox",  "caption" => "IP-Symcon Password");
			$arrayElements[] = array("type" => "Label", "label" => "_____________________________________________________________________________________________________");
		}
  				
		$arrayActions = array();
		
		$arrayActions[] = array("type" => "Label", "label" => "Aktuell sind keine Funktionen definiert");
		
 		return JSON_encode(array("status" => $arrayStatus, "elements" => $arrayElements, "actions" => $arrayActions)); 		 
 	}    
	
	public function ApplyChanges()
	{
		//Never delete this line!
		parent::ApplyChanges();
		
		// Logging setzen
				
		// Registrierung für Nachrichten
	
		//$this->SetTimerInterval("Messzyklus", ($this->ReadPropertyInteger("Messzyklus") * 1000));
		
		If ($this->ReadPropertyBoolean("Open") == true) {
			$this->SetStatus(102);
		}
		else {
			$this->SetStatus(104);
		}
		
	}
	
	public function RequestAction($Ident, $Value) 
	{
  		switch($Ident) {
	        case "SetpointTemperature":
	            	
	            	break;
	        
	        default:
	            throw new Exception("Invalid Ident");
	    	}
	}
	
	public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    	{
		switch ($Message) {
			case 10803:
				
				break;
			
		}
    	}
		



	

}
?>