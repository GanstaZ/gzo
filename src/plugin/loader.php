<?php
/**
*
* An extension for the phpBB Forum Software package.
*
* @copyright (c) GanstaZ, https://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ganstaz\gzo\src\plugin;

use phpbb\db\driver\driver_interface;
use phpbb\di\service_collection;

final class loader
{
	protected array $testing = [];
	protected array $plugins = [];
	protected array $sections = [];

	public function __construct(
		protected driver_interface $db,
		protected service_collection $plugins_collection,
		public readonly data $data,
		protected readonly string $plugins_table,
		protected readonly string $plugins_on_page_table
	)
	{
	}

	public function get_sections(): array
	{
		return $this->sections ?? [];
	}

	public function load_available_plugins(string $page_name): void
	{
		$this->get_requested_plugins($page_name);

		if (count($this->plugins))
		{
			foreach ($this->plugins as $item)
			{
				$item->load_plugin();
			}
		}
	}

	protected function get_requested_plugins(string $page_name): void
	{
		$sql_array = [
			'SELECT'	=> 'p.name, p.ext_name, p.section, op.page_name',
			'FROM'		=> [
				$this->plugins_table => 'p',
				$this->plugins_on_page_table => 'op',
			],
			'WHERE'		=> "p.name = op.name
				AND op.page_name = '" . $this->db->sql_escape($page_name)  . "'" . '
				AND op.active = 1',
			'ORDER_BY'	=> 'p.position',
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql, 86400);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$plugin = $this->plugins_collection[$this->get_service_name($row['name'], $row['ext_name'])];

			if ($plugin->loadable)
			{
				$this->plugins[$row['name']] = $plugin;
			}

			// Set template data for twig blocks
			if ($row['section'])
			{
				$this->sections[] = $row['section'];

				$data = [
					'name'	   => $row['name'],
					'ext_name' => $row['ext_name'],
				];

				$data['name'] = $this->vendor($data);
				$this->data->set_template_data($row['section'], $data['name'], $data['ext_name']);
			}

			$this->testing[$row['page_name']][$row['section']][$row['name']] = $row['ext_name'];
		}
		$this->db->sql_freeresult($result);

		var_dump($this->testing);
	}

	public function get_service_name(string $service, string $ext_name): string
	{
		$ext_name = str_replace('_', '.', $ext_name);
		return "{$ext_name}.plugin." . utf8_substr($service, utf8_strpos($service, '_') + 1);
	}

	public function vendor(array $data): string
	{
		if (str_contains($data['ext_name'], 'ganstaz'))
		{
			$data['name'] = str_replace('ganstaz_', '', $data['name']);
		}

		return $data['name'];
	}
}
