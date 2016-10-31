<?php defined('SYSPATH') OR die('No direct access allowed.');

class View extends View_Core {

	public function __construct($name, $data = NULL, $type = NULL)
	{
		if (Kohana::config('smarty.integration') == TRUE)
		{
			$this->MY_Smarty = new MY_Smarty;
		}
		
		$smarty_ext = Kohana::config('smarty.templates_ext');

		if (Kohana::config('smarty.integration') == TRUE AND Kohana::find_file('views', $name, FALSE, (empty($type) ? $smarty_ext : $type)))
		{
			$type = empty($type) ? $smarty_ext : $type;
		}

		parent::__construct($name, $data, $type);
	}
	
	public function load_view($template, $vars)
	{
		if ($template == '')
			return;

		if (substr(strrchr($template, '.'), 1) === Kohana::config('smarty.templates_ext'))
		{
			// Assign variables to the template
			if (is_array($vars) AND count($vars) > 0)
			{
				foreach ($vars AS $key => $val)
				{
					$this->MY_Smarty->assign($key, $val);
				}
			}

			// Send Kohana::instance to all templates
			$this->MY_Smarty->assign('this', $this);

			// Fetch the output
			$output = $this->MY_Smarty->fetch($template);

		}
		else
		{
			$output = parent::load_view($template, $vars);
		}

		return $output;
	}
}
