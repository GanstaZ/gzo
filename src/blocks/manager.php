<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\blocks;

use phpbb\db\driver\driver_interface;
use phpbb\di\service_collection;

class manager
{
	/** @var array sections */
	protected $sections = ['gzo_right', 'gzo_bottom', 'gzo_left', 'gzo_top', 'gzo_middle'];

	/** @var array type */
	protected $type = ['section', 'name'];

	/** @var array error */
	protected $error = [];

	public function __construct(
		private driver_interface $db,
		private service_collection $collection,
		private string $blocks_data
	)
	{
	}

	/**
	* Get sections
	*/
	public function get_sections(): array
	{
		return $this->sections ?? [];
	}

	/**
	* Blocks data table
	*/
	public function blocks_data(): string
	{
		return $this->blocks_data;
	}

	/**
	* Get error log for invalid block names
	*/
	public function get_error_log(): array
	{
		return $this->error ?? [];
	}

	/**
	* Check for new block/s
	*/
	public function check_for_new_blocks(array $data_ary, object $container): array
	{
		$return = [];
		foreach ($this->collection as $service => $service_data)
		{
			$data = $this->check($service, $service_data->get_block_data(), $container);

			// Validate data and set it for installation
			if ($data && !in_array($data['name'], array_column($data_ary, 'name')))
			{
				$return[$data['name']] = $data;
			}
		}

		return $return ?? [];
	}

	/**
	* Check conditioning
	*/
	public function check(string $service, array $row, object $container): array
	{
		$this->_section($service, $row['section']);
		$this->_ext_name($service, $row['ext_name'], $container);

		$row['name'] = str_replace(
			utf8_substr($row['ext_name'], utf8_strpos($row['ext_name'], '_') + 1) . '_block_',
			'',
			str_replace('.', '_', $service)
		);

		$this->_block_name($service, $row, $container);

		return empty($this->error[$service]) ? $data = [
			'name'	   => $row['name'],
			'section'  => $row['section'],
			'ext_name' => $row['ext_name'],
		] : [];
	}

	/**
	* Get service name
	*/
	public function get_service_name(string $service, string $ext_name): string
	{
		return str_replace('_', '.', "{$ext_name}.block." . utf8_substr($service, utf8_strpos($service, '_') + 1));
	}

	/**
	* Check if section is valid
	*/
	protected function _section(string $service, string $section): void
	{
		if (!in_array($section, $this->sections))
		{
			$this->error[$service]['section'] = $section;

			if (empty($section))
			{
				$this->error[$service]['section'] = 'VAR_EMPTY';
			}
		}
	}

	/**
	* Check if ext_name is valid
	*/
	protected function _ext_name($service, string $ext_name, $container): void
	{
		if (!$container->get('ext.manager')->is_enabled(str_replace('_', '/', $ext_name)))
		{
			$this->error[$service]['ext_name'] = $ext_name;
			$this->error[$service]['service'] = 'PRE_ERROR';

			if (empty($ext_name))
			{
				$this->error[$service]['ext_name'] = 'VAR_EMPTY';
				unset($this->error[$service]['service']);
			}
		}
	}

	/**
	* Check if block service name is valid
	*/
	protected function _block_name($service, $row, $container): void
	{
		if (isset($this->error[$service]['section']))
		{
			$this->error[$service]['error'] = 'NOT_AVAILABLE';
		}

		if (!$container->has($this->get_service_name($row['name'], $row['ext_name'])))
		{
			if (empty($this->error[$service]['service']))
			{
				$this->error[$service]['service'] = 'SER_ERROR';
			}

			$this->error[$service]['error'] = 'NOT_AVAILABLE';
		}
	}

	/**
	* Get vendor name
	*/
	public function get_vendor(string $ext_name): string
	{
		return strstr($ext_name, '_', true);
	}

	/**
	* If vendor name is ganstaz, remove package
	*/
	public function is_vendor_ganstaz(array $data): string
	{
		if ($this->get_vendor($data['ext_name']) === 'ganstaz')
		{
			$data['name'] = str_replace('ganstaz_', '', $data['name']);
		}

		return $data['name'];
	}
}
