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
    protected $php_ext;
    

	
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'load_language_on_setup',
            'core.submit_post_end'					=> 'first_post_sticky',
		);
	}
	/**
	* Constructor
	*/
	public function __construct(\phpbb\request\request_interface $request, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\user $user, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\template\template $template, $phpbb_root_path, $table_prefix, $php_ext )
	{
        $this->request = $request;
		$this->db = $db;
		$this->auth = $auth;
        $this->user = $user;
		$this->config = $config;
		$this->config_text = $config_text;
		$this->template = $template;
        //$this->language = $language;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->table_prefix = $table_prefix;
        $this->php_ext = $php_ext; 
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

	public function first_post_sticky($event)
	{
		//global $post_data;
		$data = $event['data'];
		$post_id = (int) $data['post_id'];
		$topic_id = (int) $data['topic_id'];
		$forum_id = (int) $data['forum_id'];
        $topic_title = $data['topic_title'];
		$mode = $event['mode'];  
        if ((int)$this->config['chat_bot']==1 ) return;
		$tests=explode(',', $this->config_text->get('simplechat_excluded'));
		$i=0;
		While ($i < count($tests))
		{
			if ($forum_id=(int)$tests[$i])
			{
			  return;					
			}
			$i++;
		}
        if ((int)$this->user->data['user_id'] != 0)
        {
            $topic_notification = ($mode == 'reply' || $mode == 'quote') ? true : false;
            $forum_notification = ($mode == 'post') ? true : false;
            if (!$topic_notification && !$forum_notification) return;

            $poster_id = $this->user->data['user_id'];
            $page_name=generate_board_url();
            $forum_url = "{$page_name}/viewforum.{$this->php_ext}?f={$forum_id}";                                        //generate_board_url() . "/viewforum.$phpEx?f=$forum_id";
            $topic_url = "{$page_name}/viewtopic.{$this->php_ext}?f={$forum_id}&t={$topic_id}&p={$post_id}#p{$post_id}";//generate_board_url() . "/viewtopic.$phpEx?f=$forum_id&t=$topic_id&p=$post_id&e=$post_id";

            $notify = "<strong>" . $this->user->data['username'] . "</strong> ";
            if($forum_notification)
            {
                $notify .= " создал в форуме «<a href='{$forum_url}'>{$forum_name}</a>» новую тему: <a href='{$topic_url}'>{$topic_title}</a>";
            }
            if($topic_notification)
            {
                $notify .= " ответил в теме: <a href=".$topic_url.">{$topic_title}</a>";
            }

            $message = array(
                'user_id'	=> 0,//$this->user->data['user_id'],
                'username'	=> 'Чат',//$this->user->data['username'],
                'time'		=> time(),
                'text'		=> $notify,
                'color'		=> '000000'
            );
        	$sql = "INSERT INTO " . CHAT_MESSAGES_TABLE . " " . $this->db->sql_build_array('INSERT', $message);
	        $this->db->sql_query($sql);
        }

	}
	
}