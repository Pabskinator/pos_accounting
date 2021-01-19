<?php

	$str = '{"2938":"[{\"rack\":\"3A-P1B\",\"rack_description\":\"\",\"stock_man\":\"GIL FALCATAN,JEM CARMELOTES\",\"qty\":1,\"rack_id\":\"928\"}]","2095":"[{\"rack\":\"3A-N1A\",\"rack_description\":\"\",\"stock_man\":\"GIL FALCATAN,JEM CARMELOTES\",\"qty\":1,\"rack_id\":\"939\"}]","2120":"[{\"rack\":\"3A-N1B\",\"rack_description\":\"\",\"stock_man\":\"GIL FALCATAN,JEM CARMELOTES\",\"qty\":4,\"rack_id\":\"940\"}]","567":"[{\"rack\":\"3A-AF1A\",\"rack_description\":\"\",\"stock_man\":\"GIL FALCATAN,JEM CARMELOTES\",\"qty\":1,\"rack_id\":\"1576\"}]","2124":"[{\"rack\":\"3A-N1C\",\"rack_description\":\"\",\"stock_man\":\"GIL FALCATAN,JEM CARMELOTES\",\"qty\":1,\"rack_id\":\"941\"}]"}';

	$json = json_decode($str,true);


	print_r($json);
	foreach($json as $item_id => $array){
		echo "<p>id $item_id</p>";
		$array = json_decode($array,true);
		foreach($array as $data){
			print_r($data);
		}

	}
?>