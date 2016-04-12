<?php
/**
*
* @package paywindow
* @copyright (c) 2014 aleksey
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\simplechat\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
/**
* Assign functions defined in this class to event listeners in the core
*
* @return array
* @static
* @access public
*/
	/** @var \phpbb\db\driver\driver_interface */
    protected $db;	
	/** @var \phpbb\user */
	protected $user;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\config\db_text */
	protected $config_text;
	/** @var \phpbb\template\template */
	protected $template;
	protected $phpbb_root_path;
    

	
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
			'core.page_footer'						=> 'content_footer',
		);
	}
	/**
	* Constructor
	*/
	public function __construct(\phpbb\request\request_interface $request, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\user $user, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\template\template $template, $phpbb_root_path, $table_prefix )
	{
        $this->request = $request;
		$this->db = $db;
		$this->auth = $auth;
        $this->user = $user;
		$this->config = $config;
		$this->text = $config_text;
		$this->template = $template;
        //$this->language = $language;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->table_prefix = $table_prefix;
		define(__NAMESPACE__ . '\USER_TABLE', $this->table_prefix . 'users');
        define('CHAT_MESSAGES_TABLE',	$this->table_prefix . 'chat_messages');
        define('CHAT_SESSIONS_TABLE',	$this->table_prefix . 'chat_sessions');


	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'Sumanai/simplechat',
			'lang_set' => 'chat',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
   
public function content_footer()
{
/*
switch ($action)
{
	// Load chat body
	case ACT_LOAD:
		//include_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
         
		//page_header($user->lang['chat']);
		//$this->template->set_custom_template($phpbb_chat_path.'template', 'simplechat');
		//$this->template->set_custom_style('simplechat', $phpbb_chat_path.'template');
		//$this->template->set_filenames(array('bodys' => 'chat_body.html'));
		$this->template->assign_vars(array(
			'COPYRIGHT' 	=> 'проверка входа'.$this->user->lang['POWERED_BY_CHAT'],
			'BUILD_TIME' 	=> BUILD_TIME
		));
		//generate_smilies("inline", false);
		//page_footer();
	//exit;
    
}        
*/

/*		$countdayadd=$this->config['before_day'];
		$dayend=date('z',time()+((60*60*24*$countdayadd)));
		$daynow=date('z',time());
		$sql = 'SELECT user_id, username, user_avatar, user_avatar_type , STR_TO_DATE(user_birthday,"%d-%m-%Y") as user_birthday, NOW() 
            FROM ' . USER_TABLE .' 
			WHERE (UNIX_TIMESTAMP()- user_lastpost_time)<(60*60*24*'.$this->config['active_post_begin_day'].')  			and DAYOFYEAR(STR_TO_DATE(user_birthday,"%d-%m-%Y")) between '.$daynow.' and '.$dayend .' 
			order by DAYOFYEAR(STR_TO_DATE(user_birthday,"%d-%m-%Y"))' ;
        $result = $this->db->sql_query($sql);
        while ($row = $this->db->sql_fetchrow($result))
        {
				  if ($birthday>0)
				  {
						$this->template->assign_block_vars('rowbd', array(
							'ID' 				=> $row['user_id'],
							'NAME'				=> get_user_avatar($row['user_avatar'], $row['user_avatar_type'], 25, 'auto').'</br>'.$row['username'],
							'BIRTHDAY'				=>'Через '.($birthday).' '.$d.'  исполнится '.$yarh.$y,
						));
					}
        }*/
	}
}