 <?
    $curl = curl_init();
        // the api only allows up to 3 types of vehicle stats per pull, 
        //so, I have 2 pulls for the 5 stats I need.  
        //Yes, I could have made a function for it.

        //Pull 1
        $url="https://api.samsara.com/fleet/vehicles/stats?types=engineStates,fuelPercents,defLevelMilliPercent";
        curl_setopt($curl, CURLOPT_URL, $url);  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".$token
        ));  							   
            // Send the request
        $result = curl_exec($curl);
        if (curl_error($curl)) {
            $error_msg = curl_error($curl);
        }							
        curl_close($curl);
        
        $vehjson = json_decode($result, true);

foreach ($vehjson['data'] as $x=>$x_value){
    $vehicleid=$x_value['id'];
    $vehname=$x_value['name'];
    
    $vehfuel=$x_value['fuelPercent']['value'];
    $vehfueldate=$x_value['fuelPercent']['time'];
    $vehfueldate=date("Y-m-d H:i:s",strtotime($vehfueldate));
    $vehicles[$vehicleid]['id']=$vehicleid;
    $vehicles[$vehicleid]['name']=$vehname;
    $vehicles[$vehicleid]['fuel']=$vehfuel/100;
    $vehicles[$vehicleid]['fueldate']=$vehfueldate;
    
    $def=$x_value['defLevelMilliPercent']['value'];
    $defdate=$x_value['defLevelMilliPercent']['time'];
    $defdate=date("Y-m-d H:i:s",strtotime($defdate));
    $vehicles[$vehicleid]['deflevel']=$def/100000;
    $vehicles[$vehicleid]['defdate']=$defdate;	
    
    $enginestate=$x_value['engineState']['value'];
    $enginestatedate=$x_value['engineState']['time'];
    $vehicles[$vehicleid]['enginestate']=$enginestate;
    $vehicles[$vehicleid]['enginestatedate']=$enginestatedate;		
            
    
}
//Pull 2
    $curl = curl_init();

        $url="https://api.samsara.com/fleet/vehicles/stats?types=obdOdometerMeters,faultCodes";
        curl_setopt($curl, CURLOPT_URL, $url);  
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer ".$token
        ));  							   
            // Send the request
        $result = curl_exec($curl);
        if (curl_error($curl)) {
            $error_msg = curl_error($curl);
        }							
        curl_close($curl);
        
        $vehjson = json_decode($result, true);

foreach ($vehjson['data'] as $x=>$x_value){
    $vehicleid=$x_value['id'];
    $vehname=$x_value['name'];

    
    $odomoter=$x_value['obdOdometerMeters']['value'];
    $odomoter=$odomoter/1609.344;
    $odometerdate=$x_value['obdOdometerMeters']['time'];
    $vehicles[$vehicleid]['odometer']=$odomoter;
    $vehicles[$vehicleid]['odometerdate']=$odometerdate;									
    
    $faultcodes=$x_value['faultCodes'];
    $fcdate=$x_value['faultCodes']['time'];
    $vehicles[$vehicleid]['checkengine']=$faultcodes["obdii"]["checkEngineLightIsOn"];
    $vehicles[$vehicleid]['checkenginecodes']=$faultcodes["obdii"]["permanentDtcs"][0];
    $vehicles[$vehicleid]['pendingcheckenginecodes']=$faultcodes["obdii"]["pendingDtcs"][0];
    $vehicles[$vehicleid]['confirmedcheckenginecodes']=$faultcodes["obdii"]["confirmedDtcs"][0];
    $vehicles[$vehicleid]['checkenginedate']=$fcdate;									
            
}
echo "<pre>";
var_dump($vehicles);
echo "</pre>";
?>