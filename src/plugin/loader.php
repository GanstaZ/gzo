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
	protected array $plugins = [];

	protected array $type = ['section', 'name'];

	public function __construct(
		protected driver_interface $db,
		protected service_collection $collection,
		public readonly data $data,
		protected readonly string $plugins_table
	)
	{
	}

	public function load(string|array $name = null, string $type = 'section'): void
	{
		if (!in_array($type, $this->type))
		{
			return;
		}

		if ($blocks = $this->get_requested_blocks($name, $type))
		{
			foreach ($blocks as $block)
			{
				$block->load_plugin();
			}
		}
	}

	protected function get_requested_blocks(string|array $name = null, string $type): array
	{
		$where = (null !== $name) ? $this->where_clause($name, $type) : 'active = 1';

		$sql = 'SELECT name, ext_name, section
				FROM ' . $this->plugins_table . '
				WHERE ' . $where . '
				ORDER BY position';
		$result = $this->db->sql_query($sql, 86400);

		$blocks = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$block = $this->collection[$this->get_service_name($row['name'], $row['ext_name'])];

			if ($block->loadable)
			{
				$blocks[$row['name']] = $block;
			}

			// Set section data for twig blocks tag/function
			if ($row['section'])
			{
				$data = [
					'name'	   => (string) $row['name'],
					'ext_name' => (string) $row['ext_name'],
				];

				$data['name'] = $this->vendor($data);
				$this->data->set_template_data($row['section'], [$data['name'] => $data['ext_name']]);
			}
		}
		$this->db->sql_freeresult($result);

		return $blocks;
	}

	protected function where_clause(string|array $name, string $type): string
	{
		if (is_array($name))
		{
			return $this->db->sql_in_set($type, $name) . ' AND active = 1';
		}
		else if (is_string($name))
		{
			return "{$type} = '" . $this->db->sql_escape($name) . "' AND active = 1";
		}
	}

	public function get_service_name(string $service, string $ext_name): string
	{
		$ext_name = str_replace('_', '.', $ext_name);
		return "{$ext_name}.plugin." . utf8_substr($service, utf8_strpos($service, '_') + 1);
	}

	public function vendor(array $data): string
	{
		if (strstr($data['ext_name'], '_', true) === 'ganstaz')
		{
			$data['name'] = str_replace('ganstaz_', '', $data['name']);
		}

		return $data['name'];
	}
}
