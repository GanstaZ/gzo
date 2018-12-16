<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks;

use phpbb\db\driver\driver_interface;

/**
* DLS Web blocks manager
*/
class manager
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var blocks data table */
	protected $blocks_data;

	/** @var \dls\web\core\blocks\event */
	protected $event;

	/** @var array Contains validated block services */
	protected static $blocks = false;

	/** @var array Contains info about current status */
	protected $status;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db Db object
	* @param string $blocks_data The name of the blocks data table
	* @param \dls\web\core\blocks\event $event Data object
	*/
	public function __construct(driver_interface $db, \phpbb\di\service_collection $blocks_collection, $blocks_data, event $event)
	{
		$this->db = $db;
		$this->blocks_data = $blocks_data;
		$this->event = $event;

		$this->register_validated_blocks($blocks_collection);
	}

	/**
	* Register all validated blocks
	*
	* @param Service collection of blocks
	* @return null
	*/
	protected function register_validated_blocks($blocks_collection)
	{
		$sql = 'SELECT block_name, active
				FROM ' . $this->blocks_data . '
				ORDER BY block_id';
		$result = $this->db->sql_query($sql, 3600);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$rowset[$row['block_name']] = [
				'block_name' => $row['block_name'],
				'active'	 => $row['active'],
			];
		}
		$this->db->sql_freeresult($result);

		self::$blocks = [];
		foreach ($blocks_collection as $block)
		{
			$data = $block->get_data();

			if ($this->is_valid_name($data))
			{
				self::$blocks[$data['block_name']] = $block;

				// Check for new blocks
				$this->check_available($data, $rowset);
			}
		}
	}

	/**
	* Set event data
	*
	* @param string $cat_name
	* @param array $blocks
	* @return void
	*/
	public function set($cat_name, $blocks)
	{
		$this->event->set_data($cat_name, $blocks);
	}

	/**
	* Get status
	*
	* @param string $status
	* @return array
	*/
	public function status($status)
	{
		return ($this->status[$status]) ? $this->status[$status] : [];
	}

	/**
	* Blocks data table
	*
	* @return string table name
	*/
	public function blocks_data()
	{
		return $this->blocks_data;
	}

	/**
	* Get block data
	*
	* @param string $service Service name
	* @return object
	*/
	public function get($service)
	{
		if (self::$blocks[$service])
		{
			return self::$blocks[$service];
		}
	}

	/**
	* Check for new blocks
	*
	* @param array $data
	* @param array $rowset
	* @return null
	*/
	public function check_available($data, $rowset)
	{
		if (!$rowset[$data['block_name']])
		{
			$this->status['update'][] = $data['block_name'];
			$this->status['add'][] = [
				'block_name' => $data['block_name'],
				'ext_name'	 => $data['ext_name'],
				'position'	 => 0,
				'active'	 => 0,
				'cat_name'   => $data['cat_name'],
			];
		}
	}

	/**
	* Is our block/service available
	*
	* @param string $block_name The name of the block we want to validate
	* @return null
	*/
	public function check_availability($block_name)
	{
		if (!$this->get($block_name))
		{
			$this->status['purge'][] = $block_name;
		}
	}

	/**
	* Check for update/purge status
	*
	* @return string $status
	*/
	public function check_status()
	{
		if (!$this->status)
		{
			return;
		}
		else if ($this->status['update'])
		{
			$status = 'update';
		}
		else if ($this->status['purge'])
		{
			$status = 'purge';
		}

		return $status;
	}

	/**
	* Get vendor name
	*
	* @param string $ext_name Name of the extension
	* @return string vendor name
	*/
	public function get_vendor($ext_name)
	{
		return strstr($ext_name, '_', true);
	}

	/**
	* Check if our block name is valid
	*
	* @param array $data Stores data that we need to validate
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_valid_name(array $data)
	{
		$ext_name = $this->get_vendor($data['ext_name']);
		$validate = utf8_strpos($data['block_name'], $ext_name);

		return ($validate !== false) ? true : false;
	}

	/**
	* If extension name is dls, remove prefix
	*
	* @param array $data Data array
	* @return string $data['block_name']
	*/
	public function is_dls(array $data)
	{
		if ($this->get_vendor($data['ext_name']) === 'dls')
		{
			$data['block_name'] = str_replace('dls_', '', $data['block_name']);
		}

		return $data['block_name'];
	}
}