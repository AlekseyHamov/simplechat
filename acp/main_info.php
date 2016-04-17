<?php
/**
*
* @package phpBB Extension - My test
* @copyright (c) 2013 phpBB Groupn
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\simplechat\acp;

class main_info
{
    function module()
    {
        return array(
            'filename'    => '\Sumanai\simplechat\acp\main_module',
            'title'        => 'ACP_SIMPLECHAT',
            'version'    => '0.0.2',
            'modes'        => array(
            'settings'    => array('title' => 'ACP_SIMPLECHAT', 'auth' => 'ext_Sumanai/simplechat && acl_a_board', 'cat' => array('ACP_SIMPLECHAT')),
            ),
        );
    }
}