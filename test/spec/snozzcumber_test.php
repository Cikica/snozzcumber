<?php

	class snozzcumber_test extends PHPUnit_Framework_TestCase
	{
		public function setUp ( )
		{
			$this->map = array(
				'test_class' => 'help/test_class.php',
				'fake_test'  => '../help/test_class.php',
			);
		}

		public function test_that_get_returns_information_about_an_invalid_file_path ()
		{
			$result = snozzcumber::make(array(	
				'map'       => $this->map,
				'requested' => array(
					'class'      => 'fake_test',
					'method'     => 'some',
					'paramaters' => array( 'some', 'some' )
				)
			));

			$this->assertEquals(
				json_encode( array( 
					"report" => array(
						"error"   => true,
						"message" => 'Invalid path : ../help/test_class.php for class fake_test',
					)
				) ),
				$result
			);

		}

		public function test_find_out_if_class_method_is_callable_and_if_not_why ()
		{	
			require $this->map['test_class'];

			$result = snozzcumber::find_out_if_class_method_is_callable_and_if_not_why(array(
				'path'   => $this->map['test_class'],
				'name'   => 'some_stuff',
				'method' => 'get_some_public',
			));

			$this->assertEquals(
				array(
					"report" => array(
						"error"   => true,
						"message" => "Class some_stuff was not defined in the file help/test_class.php"
					)
				),
				$result
			);

			$result2 = snozzcumber::find_out_if_class_method_is_callable_and_if_not_why(array(
				'path'   => $this->map['test_class'],
				'name'   => 'test_class',
				'method' => 'some_method',
			));

			$this->assertEquals(
				array(
 					"report" => array(
 						"error"   => true,
 						"message" => "Class test_class in file help/test_class.php does not have the method called some_method"
 					)
 				),
				$result2
			);

			$result3 = snozzcumber::find_out_if_class_method_is_callable_and_if_not_why(array(
				'path'   => $this->map['test_class'],
				'name'   => 'test_class',
				'method' => 'get_some_private',
			));

			$this->assertEquals(
				array(
					"method" => array( 
						"valid" => false,
						"type"  => "private"
					),
 					"report" => array(
 						"error"   => true,
 						"message" => "Method get_some_private is private in class test_class in file help/test_class.php"
 					)
 				),
				$result3
			);

			$result3 = snozzcumber::find_out_if_class_method_is_callable_and_if_not_why(array(
				'path'   => $this->map['test_class'],
				'name'   => 'test_class',
				'method' => 'get_some_protected',
			));

			$this->assertEquals(
				array(
					"method" => array( 
						"valid" => false,
						"type"  => "protected"
					),
 					"report" => array(
 						"error"   => true,
 						"message" => "Method get_some_protected is protected in class test_class in file help/test_class.php"
 					)
 				),
				$result3
			);

			$result4 = snozzcumber::find_out_if_class_method_is_callable_and_if_not_why(array(
				'path'   => $this->map['test_class'],
				'name'   => 'test_class',
				'method' => 'get_some_public',
			));

			$this->assertEquals(
				array(
					"method" => array( 
						"valid" => true,
						"type"  => "public"
					),
 					"report" => array(
 						"error"   => false,
 						"message" => ""
 					)
 				),
				$result4
			);

			$result5 = snozzcumber::find_out_if_class_method_is_callable_and_if_not_why(array(
				'path'   => $this->map['test_class'],
				'name'   => 'test_class',
				'method' => 'get_some_static',
			));

			$this->assertEquals(
				array(
					"method" => array( 
						"valid" => true,
						"type"  => "static"
					),
 					"report" => array(
 						"error"   => false,
 						"message" => ""
 					)
 				),
				$result5
			);
		}
	}