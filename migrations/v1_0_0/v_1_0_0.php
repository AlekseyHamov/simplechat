<?php
/**
*
* @package simplechat
* @copyright (c) 2015 Sumanai
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace Sumanai\simplechat\migrations\v1_0_0;

class v_1_0_0 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
			return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		// If 'chat_messages' table exists, likely tables are created, and does not require updates
		if (!$this->db_tools->sql_table_exists($this->table_prefix . 'chat_messages'))
		{
			return array(
				'add_tables'	=> array(
					$this->table_prefix . 'chat_messages'	=> array(
						'COLUMNS'	=> array(
							'msg_id'						=> array('UINT:11', null, 'auto_increment'),
							'user_id'						=> array('UINT', 0),
							'username'						=> array('VCHAR:255', ''),
							'time'							=> array('TIMESTAMP', 0),
							'text'							=> array('VCHAR:255', ''),
							'color'							=> array('VCHAR:6', ''),
						),
						'PRIMARY_KEY'	=> 'msg_id',
					),
					$this->table_prefix . 'chat_sessions'	=> array(
						'COLUMNS'	=> array(
							'user_id'						=> array('UINT', 0),
							'username'						=> array('VCHAR:255', ''),
							'last_active'					=> array('TIMESTAMP', 0),
							'user_status'					=> array('TINT:3', 0),
							'user_activity'					=> array('UINT:6', 0),
							'user_blocked'					=> array('BOOL', 0),
						),
						'KEYS'	=> array(
							'user_id'	=> array('UNIQUE', 'user_id'),
						),
					),
				),
			);
		}
		return array(
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'chat_messages',
				$this->table_prefix . 'chat_sessions',
			),
		);
	}
}
