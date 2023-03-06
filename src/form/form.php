<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\form;

/**
* Form class
*/
final class form
{
	/** @var string form key */
	private string $form_key;

	/** @var array Contains form data */
	private array $children = [];

	/** @var array Contains config data */
	private array $config = [];

	/** @var array Contains form errors */
	private array $errors = [];

	/** @var array Contains form types */
	private array $types = ['choice', 'radio', 'number'];

	/** @var array Contains option types */
	private array $option_types = ['value', 'current', 'custom', 'choices', 'values', 'attributes', 'classes', 'constraints', 'explain', 'group', 'params', 'keys'];

	/** @var request */
	private object $request;

	/** @var template */
	private object $template;

	public function __construct($request, $template)
	{
		$this->request	= $request;
		$this->template = $template;
	}

	public function create_view(string $u_action)
	{
		foreach ($this->children as $child => $data)
		{
			$form_name = $data['group'] ? $data['group'] : 'form';
			$this->template->assign_block_vars($form_name, [
				'name'	  => $child,
				'type'	  => $data['type'],
				'OPTIONS' => $data['options'],
			]);
		}

		$this->template->assign_var('U_ACTION', $u_action);
	}

	/**
	* Build form
	*
	* @param array	$form_data
	* @return self
	*/
	public function build(array $form_data, bool $s_config = false): self
	{
		foreach ($form_data as $child => $data)
		{
			$options = isset($data['options']) ? $data['options'] : [];
			$this->add($child, $data['type'], $options, $s_config);
		}

		return $this;
	}

	/**
	* Add form data
	*
	* @param string $child	  Name attribute
	* @param string $type	  Type (input|choice|text...)
	* @param array	$options  Array of different options (Attributes etc).
	* @param bool	$s_config Default is false
	* @return self
	*/
	public function add(string $child, string $type, array $options = [], bool $s_config = false): self
	{
		if (!$child)
		{
			trigger_error('NO_NAME');
			//throw new http_exception(403, $this->lang->lang('NO_NAME'));
		}

		if (!in_array($type, $this->types))
		{
			$this->add_error($child, $type, 'NO_VALID_TYPE');
		}

		$this->check_options($child, $options, $s_config);

		$options['s_custom'] = isset($options['custom']) ? $options['custom'] : '';
		$options['s_config'] = $s_config;

		$this->children[$child] = [
			'name'	  => $child,
			'type'	  => $type,
			'group'	  => isset($options['group']) ? $options['group'] : '',
			'options' => $options,
		];

		return $this;
	}

	protected function check_options(string $child, array $options, bool $s_config): void
	{
		if (!$options)
		{
			return;
		}

		foreach (array_keys($options) as $row)
		{
			if (!in_array($row, $this->option_types))
			{
				$this->add_error($child, $row, 'NO_VALID_OPTION');
			}
		}

		if ($s_config)
		{
			$this->prepare($child, $options);
		}
	}

	protected function prepare(string $child, array $options): void
	{
		$params = isset($options['params']) ? $options['params'] : [0];

		if (count($params) > 2)
		{
			$this->add_error($child, 'params', 'NO_VALID_PARAM_COUNT');

			return;
		}

		$value = $this->request->variable($child, $params[0]);
		if (count($params) > 1)
		{
			$value = $this->request->variable($child, $params[0], $params[1]);
		}

		$type = 'common';
		if (isset($options['custom']))
		{
			$type = 'special';
		}

		$this->config[$type][$child] = $value;
	}

	public function _get($type): array
	{
		return $this->config[$type] ?? [];
	}

	public function config()
	{
		return $this->config;
	}

	public function err()
	{
		return $this->errors;
	}

	/**
	* Add form key for form validation checks
	*
	* @param string $key Form key
	* @return void
	*/
	public function add_form_key(string $key): void
	{
		add_form_key($key);

		$this->form_key = $key;
	}

	/**
	* Check if our form is valid
	*
	* @throws \phpbb\exception\http_exception
	* @return bool
	*/
	public function is_valid(): bool
	{
		if (!check_form_key($this->form_key))
		{
			trigger_error('FORM_INVALID');
			//throw new http_exception(403, $this->lang->lang('FORM_INVALID'));
		}

		return 0 === \count($this->errors);
	}

	public function is_submitted(): bool
	{
		return $this->is_set_post('submit');
	}

	public function is_preview(): bool
	{
		return $this->is_set_post('preview');
	}

	/**
	* Get form data from a given child
	*
	* @param string $child Name attribute
	* @return array
	*/
	public function get(string $child): array
	{
		return $this->children[$child] ?? [];
	}

	/**
	* Is set post
	*
	* @param string $name The name of the form variable which should have a
	* @return bool
	*/
	protected function is_set_post(string $name): bool
	{
		return $this->request->is_set_post($name);
	}

	protected function add_error(string $child, string $data, string $error): void
	{
		$this->errors[$child] = [$data, $error];
	}
}
