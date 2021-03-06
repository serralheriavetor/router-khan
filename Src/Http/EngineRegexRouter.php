<?php

	
		namespace App\Http;

		class EngineRegexRouter {

			private static $instance = null,
						   $prefix = "/[{}]/";

			public static function create(){
				if(self::$instance == null){
					self::$instance = new EngineRegexRouter();
				}
				return self::$instance;
			}

			protected function __construct(){}

			public static function validate($regex){ 
				return preg_match(self::$prefix, $regex); 
			}

			public static function work($regex, $rota){
			    if(EngineRegexRouter::validate($regex)){
			        $m = []; $param = [];
			        $rex = explode('/', $regex);
			        $repleced = $regex;
			        foreach($rex as $key => $r){ 
			        	if(EngineRegexRouter::validate($r)){ 
			        		$m[] = $r;
			        		$param[] = str_replace(['{','}'], '', $r);
			        	}
			    	}
			        $repleced = str_replace($m, '(.*)', $repleced);
			        $repleced = "/^".str_replace("/", "\/", $repleced)."$/";
			        $getters = [];
			        $rt = preg_match($repleced, $rota, $getters);
			        if($rt){ 
			        	unset($getters[0]); 
			        	return array_combine($param, $getters); 
			        }
			        return false;
			    }
			}

			public function build($routes, $url){
				//$routes = self::$routess["params"];
				$routerActive = [];
				foreach($routes as $rota => $fn){ 
					$lengRoute = explode("/", substr($rota, 1));
				    $lengUri = explode("/", substr($url, 1));
					if(EngineRegexRouter::validate($rota)):
						if(count($lengRoute) === count($lengUri)){
							$routerActive[] = $rota; 
						}
					endif; 
				}
				if(count($routerActive) > 0){
					foreach ($routerActive as $key => $rota) {
						[$lengRoute, $lengUri] = [
							explode("/", substr($rota, 1)),  
							explode("/", substr($url, 1))
						];
				     	$validate = EngineRegexRouter::work($rota, $url);
				     	if(count($lengUri) == count($lengRoute) && $validate){
							return ["rota" => $rota,"params" => $validate];
							break;
				     	}	
					}
				}
				return false;
			}

		}
