<?php
/**
*
* @package simplechat
* @copyright (c) 2015 Sumanai
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace Sumanai\simplechat\migrations;

class version_0_0_3 extends \phpbb\db\migration\migration
{
   	public function effectively_installed()
	{
		return isset($this->config['simplechat_version']) && version_compare($this->config['simplechat_version'], '0.0.3', '>=');;
	}
	static public function depends_on()
	{
		return array('\Sumanai\simplechat\migrations\version_0_0_2');
	}

	public function update_schema()
	{
		return array(
			'add_columns'        => array(
				$this->table_prefix . 'chat_messages'        => array(
					'hidemessage'    => array('BOOL', 1),
				),
		));
	}
	public function revert_schema()
	{
		return array(
			'drop_columns'        => array(
				$this->table_prefix . 'chat_messages'        => array(
					'hidemessage',
				),
		));

	}
}