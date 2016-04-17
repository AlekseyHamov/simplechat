<?php
/**
*
* @package simplechat
* @copyright (c) 2015 Sumanai
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace Sumanai\simplechat\migrations;

class version_0_0_2 extends \phpbb\db\migration\migration
{
   	public function effectively_installed()
	{
		return isset($this->config['simplechat_version']) && version_compare($this->config['simplechat_version'], '0.0.2', '>=');;
	}
	static public function depends_on()
	{
		return array('\Sumanai\simplechat\migrations\version_0_0_1');
	}

    public function update_data()
	{
		return array(
			// Current version
			array('config.add', array('simplechat_version', '0.0.2')),
            array('config.add', array('chat_bot', 0)),
		);
	}
}
