<?php
	
class test_class
{	
	public  function get_some_public ( $with )
	{
		return array(
			'some' => $with['text'] . "some",
		);
	}

	protected  function get_some_protected ( $with )
	{
	}

	private  function get_some_private ( $with )
	{
	}

	static  function get_some_static ( $with )
	{
		return array(
			'some' => $with['text'] . "some",
		);
	}
}