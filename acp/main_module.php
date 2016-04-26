<?php
/**
*
* @package phpBB Extension - My test
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\simplechat\acp;

class main_module
{
    var $u_action;

    function main($id, $mode)
    {
        global $db, $user, $auth, $template, $cache, $request;
        global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx, $phpbb_container;

        $this->tpl_name = 'acp_simplechat_body';
        $this->page_title = $user->lang('ACP_SIMPLECHAT');
        add_form_key('Sumanai/simplechat');
		header('Content-Type: text/html; charset=utf-8;');

        $config_text = $phpbb_container->get('config_text');

        if ($request->is_set_post('submit'))
        {
            if (!check_form_key('Sumanai/simplechat'))
            {
                trigger_error('FORM_INVALID');
            }
			
            //$config->set('show_greeting', $request->variable('show_greeting', ''));
            $config->set('show_greeting', $request->variable('show_greeting',0));
            $config->set('message_limit', $request->variable('message_limit',0));
            $config->set('antiflood_sensitivity', $request->variable('antiflood_sensitivity',0));
            $config->set('antiflood_extinction', $request->variable('antiflood_extinction',0));
            $config->set('antiflood_duration', $request->variable('antiflood_duration',0));
			$config->set('chatbot_name', utf8_normalize_nfc(trim($request->variable('chatbot_name','', true))) );
            $config->set('chat_bot', $request->variable('chat_bot',0));
			$config_text->set('simplechat_excluded', implode(',', $request->variable('simplechat_excluded', array(0))));            
            trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
        }

        $template->assign_vars(array(
            'U_ACTION'        => $this->u_action,
            'VALUE'=> $config['show_greeting'],
            //'SHOW_GREETING'   => (isset($config['show_greeting'])) ? $config['show_greeting'] : '',
            'SHOW_GREETING'         => ((int)isset($config['show_greeting'])==1) ? 1 : 0 ,
            'SHOW_GREETING_CH'      => ((int)$config['show_greeting']==1) ? 'checked' : '' ,
            'MESSAGES_LIMIT'        => ((int)isset($config['message_limit'])) ? $config['message_limit']:'',
            'ANTIFLOOD_SENSITIVITY' => ((int)isset($config['antiflood_sensitivity'])) ? $config['antiflood_sensitivity']:'',
            'ANTIFLOOD_EXTINCTION'  => ((int)isset($config['antiflood_extinction'])) ? $config['antiflood_extinction']:'',
            'ANTIFLOOD_DURATION'    => ((int)isset($config['antiflood_duration'])) ? $config['antiflood_duration']:'', 
			'CHATBOT_NAME'    => (isset($config['chatbot_name'])) ? $config['chatbot_name']:'', 
            'CHAT_BOT'         => ((int)isset($config['chat_bot'])==1) ? 1 : 0 ,
            'CHAT_BOT_CH'      => ((int)$config['chat_bot']==1) ? 'checked' : '' ,
			'SIMPLECHAT_EXCLUDED_FORUMS' => make_forum_select(explode(',', $config_text->get('simplechat_excluded')), false, false, true),
        ));
        
    }
}
