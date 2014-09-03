<?php

/**
* THe entry for the rest sytem
*/
class snozzcumber
{
	
	static function make( $what ) {
		if ( array_key_exists( $what['requested']['class'], $what['map'] ) ) { 
			return json_encode( snozzcumber::get(array(
				'path'       => $what['map'][$what['requested']['class']],
				'name'       => $what['requested']['class'],
				'method'     => $what['requested']['method'],
				'paramaters' => $what['requested']['paramaters']
			)) );
		} else { 
			return '{}';
		}
	}

	static function get ( $with )
	{	
		if ( file_exists( $with['path'] ) ) {
			
			$method_information = snozzcumber::find_out_if_class_method_is_callable_and_if_not_why(array(
				"path"   => $with['path'],
				"name"   => $with['name'],
				"method" => $with['method']
			));

			if (
				$method_information['report']['error'] === true or 
				$method_information['method']['valid'] === false
			) {
				return $method_information['report'];
			} else {

				if ( $method_information['method']['type'] === "public" ) {
					$class = new $with['name']();
					return array(
						'response' => call_user_func( 
							array( $class, $with['method'] ), 
							$with['paramaters'] 
						)
					);
				}

				if ( $method_information['method']['type'] === "static" ) {
					return array(
						'response' => call_user_func( 
							array( $with['name'], $with['method'] ), 
							$with['paramaters'] 
						)
					);
				}
			}

		} else { 
			return array(
				"report" => array(
					"error"   => true,
					"message" => "Invalid path : {$with['path']} for class {$with['name']}"
				)
			);
		}
	}

	static function find_out_if_class_method_is_callable_and_if_not_why ( $with )
	{
		if ( class_exists( $with['name'] ) ) { 

 			$reflection = new ReflectionClass($with['name']);

 			if ( $reflection->hasMethod( $with['method'] ) ) { 

 				$method_reflection = $reflection->getMethod( $with['method'] );

 				if ( $method_reflection->isStatic() ) {
 					return array(
						"method" => array( 
							"valid" => true,
							"type"  => "static"
						),
	 					"report" => array(
	 						"error"   => false,
	 						"message" => ""
	 					)
	 				);
 				} 
 				if ( $method_reflection->isPublic() ) { 
 					return array(
						"method" => array( 
							"valid" => true,
							"type"  => "public"
						),
	 					"report" => array(
	 						"error"   => false,
	 						"message" => ""
	 					)
	 				);
 				}

 				if ( $method_reflection->isProtected() ) {
 					return array(
 						"method" => array( 
							"valid" => false,
							"type"  => "protected"
						),
	 					"report" => array(
	 						"error"   => true,
	 						"message" => "Method {$with['method']} is protected in class {$with['name']} in file {$with['path']}"
	 					)
	 				);
 				}

 				if ( $method_reflection->isPrivate() ) {
 					return array(
 						"method" => array( 
							"valid" => false,
							"type"  => "private"
						),
	 					"report" => array(
	 						"error"   => true,
	 						"message" => "Method {$with['method']} is private in class {$with['name']} in file {$with['path']}"
	 					)
	 				);
 				}

 			} else { 
 				return array(
 					"report" => array(
 						"error"   => true,
 						"message" => "Class {$with['name']} in file {$with['path']} does not have the method called {$with['method']}"
 					)
 				);
 			}
		} else { 
			return array(
				"report" => array(
					"error"   => true,
					"message" => "Class {$with['name']} was not defined in the file {$with['path']}"
				)
			);
		}

		return "some";
	}
}