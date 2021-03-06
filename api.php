<?php
    $url = "http://suitecrm.zzz.com.ua/service/v4_1/rest.php";
    $username = "vk"; 
    $password = "heiniken526"; 
  
    function call($method, $parameters, $url)
    {
        ob_start();
        $curl_request = curl_init();
        curl_setopt($curl_request, CURLOPT_URL, $url);
        curl_setopt($curl_request, CURLOPT_POST, 1);
        curl_setopt($curl_request, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curl_request, CURLOPT_HEADER, 1);
        curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl_request, CURLOPT_FOLLOWLOCATION, 0);
        $jsonEncodedData = json_encode($parameters);
        $post = array(
             "method" => $method,
             "input_type" => "JSON",
             "response_type" => "JSON",
             "rest_data" => $jsonEncodedData
        );
        curl_setopt($curl_request, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($curl_request);
        curl_close($curl_request);
        $result = explode("\r\n\r\n", $result, 2);
        $response = json_decode($result[1]);
        ob_end_flush();
        return $response;
    }
    //login --------------------------------------------
    $login_parameters = array(
         "user_auth"=>array(
              "user_name"=>$username,
              "password"=>md5($password),
              "version"=>"1"
         ),
         "application_name"=>"RestTest",
         "name_value_list"=>array(),
    );
    $login_result = call("login", $login_parameters, $url);
   
    $session_id = $login_result->id;
    $params = array(
        "Leads"=>'first_name',
        "Contacts"=>'first_name',
        "Accounts"=>'name',
        "Tasks" =>'name',
        "Opportunities"=>'name',
        "Users"=>'user_name',  
        );
    
   function getEntry($session_id,$key,$value){
    $get_entry_list_parameters = array(
         
         'session' => $session_id,
        
         'module_name' => $key,
        
        
         'query' => "",
        
         'order_by' => "",
        
         'offset' => '0',
  
         'select_fields' => array(
              'id',
               $value,
              'last_name',
         ),
         
         'link_name_to_fields_array' => array(
         ),
         
         'max_results' => '20',
         
         'deleted' => '0',
    
         'Favorites' => false,
    );
    return $get_entry_list_parameters;
    }
    foreach($params as $key => $value){
    $get_entry_list_result = call('get_entry_list', getEntry($session_id,$key,$value), $url);
    echo $key.'<hr>';
    for($i = 0; $i<10; $i++){
    
   $get_entry_list_result = (array)$get_entry_list_result;
   echo "<br>";
    
    echo $get_entry_list_result['entry_list'][$i]->name_value_list->name->value;
    echo $get_entry_list_result['entry_list'][$i]->name_value_list->first_name->value;
    echo " ".$get_entry_list_result['entry_list'][$i]->name_value_list->last_name->value;
    echo " ".$get_entry_list_result['entry_list'][$i]->name_value_list->user_name->value;
          
     }
     echo '<hr>';
}
?>
