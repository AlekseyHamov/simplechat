<?php
/**
*
* @package phpBB Extension - My test
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace Sumanai\simplechat\controller;

use Symfony\Component\HttpFoundation\Response;

class simplechat
//class listener implements EventSubscriberInterface
{
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
    	/** @var string */
	protected $php_ext;


	public function __construct(\phpbb\request\request_interface $request, \phpbb\db\driver\driver_interface $db, \phpbb\auth\auth $auth, \phpbb\user $user, \phpbb\config\config $config, \phpbb\config\db_text $config_text, \phpbb\template\template $template, $phpbb_root_path, $table_prefix, $php_ext)
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
        $this->php_ext = $php_ext;   
		//define(__NAMESPACE__ . '\USER_TABLE', $this->table_prefix . 'users');
        define(__NAMESPACE__ . '\CHAT_MESSAGES_TABLE',	$this->table_prefix . 'chat_messages');
        define(__NAMESPACE__ . '\CHAT_SESSIONS_TABLE',	$this->table_prefix . 'chat_sessions');


        // Settings
        define('SESSION_LIFE',			180);			// Session lifetime
//        define('MESSAGES_LIMIT',		100);			// Store messages limit
//        define('MESSAGES_LIMIT',		(int)$this->config['message_limit']);			// Store messages limit
        //define('JOIN_MESSAGES',			false);			// Display join messages
        //define('LEFT_MESSAGES',			false);			// Display left messages
        define('ANTIFLOOD_SENSITIVITY',	9);				// Antiflood sensitivity (less is more sensitive)
        define('ANTIFLOOD_EXTINCTION',	3);				// Antiflood extinction (less is faster)
        define('ANTIFLOOD_DURATION',	30);			// Antiflood ban duration in seconds
        define('BUILD_TIME', filemtime(__FILE__));		// Internal version

        // Statuses (currently unused)
        define('STATUS_ONLINE',	0); // Online
        define('STATUS_CHAT',	1); // Chat with me!
        define('STATUS_AWAY',	2); // Away
        define('STATUS_DND',	3); // Do not disturb

        // Actions
        define('ACT_LOAD', 	'load');
        define('ACT_SYNC', 	'sync');
        define('ACT_SAY',	'say');
		define('ACT_DEL',	'del');

        // Special messages
        define('MSG_JOIN',	'/hello');
        define('MSG_LEFT',	'/bye');
	}
    static public function getSubscribedEvents()
	{
		return array(
			'core.submit_post_end'=> 'first_post_sticky',
		);
	}

	public function first_post_sticky($event)
	{
		global $post_data;
		$data = $event['data'];
		$post_id = (int) $data['post_id'];
		$topic_id = (int) $data['topic_id'];
		$forum_id = (int) $data['forum_id'];
		$mode = $event['mode'];
        echo('Тест');        
		// Set initial value for the new topic
//		$post_data['topic_first_post_show'] = (isset($post_data['topic_first_post_show'])) ? $post_data['topic_first_post_show'] : 0;

		// <Chec></Chec>k if the checkbox has been checked
//		$topic_first_post_show = isset($_POST['topic_first_post_show']);

		// Show/Unshow first post on every page
	/*	if (($mode == 'edit' && $post_id == $data['topic_first_post_id']) || $mode == 'post')
		{
			$perm_show_unshow = ($this->auth->acl_get('m_lock', $forum_id) ||
				($this->auth->acl_get('f_user_lock', $forum_id) && $this->user->data['is_registered'] && !empty($post_data['poster_id']) && $this->user->data['user_id'] == $post_data['poster_id'])
			);

			if ($post_data['topic_first_post_show'] != $topic_first_post_show && $perm_show_unshow)
			{
				$sql = 'UPDATE ' . TOPICS_TABLE . '
					SET topic_first_post_show = ' . (($topic_first_post_show) ? 1 : 0) . " 
					WHERE topic_id = $topic_id";
				$this->db->sql_query($sql);
			}
		}
        */
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

	public function main()
	{   
		if (!$this->auth->acl_gets('u_simplechat'))
        {
            if ($this->user->data['user_id'] != ANONYMOUS)
            {
                trigger_error('NOT_AUTHORISED');
            }
            login_box('', $this->user->lang['LOGIN_EXPLAIN_VIEW_CONTACTS']);
        }

       $action = $this->request->variable('action', ACT_LOAD);
       header('content-type: text/' . (($action == ACT_LOAD)?'html':'javascript') . '; charset=UTF-8');
if($action != ACT_LOAD && $this->request->variable('build', BUILD_TIME) != BUILD_TIME)
{
    echo('FullReset();');
    exit;
}
// Auth check

if (!$this->user->data['is_registered'] && $this->user->data['user_id'] != ANONYMOUS)
{
	if ($action!=ACT_LOAD)
	{
	   echo('FullReset();');
       exit;
	}
	else
	{
		//$this->template->assign_var('COPYRIGHT', $this->user->lang['POWERED_BY_CHAT']);
		//login_box($phpbb_chat_path . 'chat/index.php', $user->lang['LOGIN_EXPLAIN_CHAT']);
        //login_box($phpbb_chat_path . 'simplechat', $this->user->lang['LOGIN_EXPLAIN_CHAT']);
        login_box($this->phpbb_root_path.'simplechat'.$this->php_ext, $this->user->lang['LOGIN_EXPLAIN_CHAT']);
	}
}
// проверка настроек  сайта 
$message_limit=(int)$this->config['message_limit'];
$show_greeting=$this->config['show_greeting'];
$antiflood_sensitivity=$this->config['antiflood_sensitivity'];
$antiflood_extinction=$this->config['antiflood_extinction'];
$antiflood_duration=$this->config['antiflood_duration'];

// Detect left users
$die_time = time() - SESSION_LIFE;
//if (LEFT_MESSAGES)
if ($show_greeting!=0)
{
	$sql = "SELECT *
		FROM " . CHAT_SESSIONS_TABLE . "
		WHERE last_active < ".$die_time."
		ORDER BY last_active";
	$result = $this->db->sql_query($sql);
    
	while ($row = $this->db->sql_fetchrow($result))
	{
		// Add message that user is left
		$message = array(
			'user_id'	=> $row['user_id'],
			'username'	=> $row['username'],
			'time'		=> $row['last_active'] + SESSION_LIFE,
			'text'		=> MSG_LEFT,
			'color'		=> '000000',
			'hidemessage' => true
		);
		$sql = "INSERT INTO " . CHAT_MESSAGES_TABLE . " " . $this->db->sql_build_array('INSERT', $message);
		$this->db->sql_query($sql);
        
	}
}
// Remove left users
$sql = "DELETE
	FROM " . CHAT_SESSIONS_TABLE . "
	WHERE last_active < '{$die_time}'";
$this->db->sql_query($sql);


// Create new or prolong old session
$sql = 'SELECT *
	FROM ' . CHAT_SESSIONS_TABLE . '
	WHERE user_id = ' . $this->user->data['user_id'];
$result = $this->db->sql_query($sql);
$chat_session = $this->db->sql_fetchrow($result);
$this->db->sql_freeresult($result);
if(!$chat_session)
{
	// Add new user if needed
	$chat_session = array(
		'user_id'		=> $this->user->data['user_id'],
		'username'		=> $this->user->data['username'],
 		'last_active'	=> time(), // if user is banned - time to unban, time of the last message if else
		'user_status'	=> STATUS_ONLINE,
		'user_activity'	=> 0,
		'user_blocked'	=> 0
	);
	$sql = 'INSERT INTO ' . CHAT_SESSIONS_TABLE . ' ' .	$this->db->sql_build_array('INSERT', $chat_session);
	$this->db->sql_query($sql);
//    if (JOIN_MESSAGES)
	if ($show_greeting!=0)
	{
		// Add message that new user is joined
		$message = array(
			'user_id'	=> $this->user->data['user_id'],
			'username'	=> $this->user->data['username'],
			'time'		=> time(),
			'text'		=> MSG_JOIN,
			'color'		=> '000000',
			'hidemessage' => true
		);
	$sql = "INSERT INTO " . CHAT_MESSAGES_TABLE . " " . $this->db->sql_build_array('INSERT', $message);
    $this->db->sql_query($sql);
	}
}
else
{
	// Update user activity time and antiflood ban necessity detection
	$chat_session['user_activity'] -= time() - $chat_session['last_active'];
	if($chat_session['user_activity'] < 0) $chat_session['user_activity'] = 0;
	if(!$chat_session['user_blocked'] && $action == ACT_SAY)
	{
		//$chat_session['user_activity'] += ANTIFLOOD_EXTINCTION;
        $chat_session['user_activity'] += $antiflood_extinction;
		//if($chat_session['user_activity'] > ANTIFLOOD_SENSITIVITY)
        if($chat_session['user_activity'] > $antiflood_sensitivity)
		{
			//$chat_session['user_activity'] = ANTIFLOOD_DURATION;
            $chat_session['user_activity'] = $antiflood_duration;
			$chat_session['user_blocked'] = 1;
		}
	}
	if($chat_session['user_activity'] == 0)
	{
		$chat_session['user_blocked'] = 0;
	}
	$chat_session['last_active'] = time();
	$sql = 'UPDATE ' . CHAT_SESSIONS_TABLE . '
		SET ' . $this->db->sql_build_array('UPDATE', $chat_session) . '
		WHERE user_id = ' . $this->user->data['user_id'];
	$this->db->sql_query($sql);
}
// Handle commands
switch ($action)
{
	// Load chat body
	case ACT_LOAD:
		//include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
        include($this->phpbb_root_path. 'includes/functions_posting.' . $this->php_ext);
		page_header($this->user->lang['chat']);
		//$template->set_custom_template($phpbb_chat_path.'template', 'simplechat');
//		$this->template->set_custom_style('simplechat', $phpbb_chat_path.'template');
        $this->template->set_filenames(array('body' => 'chat_body.html'));
		if ($this->auth->acl_gets('a_', 'm_'))
		{$ACPCHEK = '<input type="checkbox" name="visiblemes" id="visiblemes" >';
		}else {
			$ACPCHEK= '<input type="checkbox" name="visiblemes" id="visiblemes" disabled="disabled" >';
		}
		$this->template->assign_vars(array(
			'COPYRIGHT' 	=> '',//'проверка входа'.$user->lang['POWERED_BY'],
			'BUILD_TIME' 	=> BUILD_TIME,
			'ACPCHEK'		=> $ACPCHEK
		));		
		generate_smilies('inline', false);
		page_footer();
	exit;
	// Add new message
	case ACT_SAY:
		if($chat_session['user_blocked'])
		{
			$time = date("H:i", time());
			$name = "";
			$text = sprintf($this->user->lang['CHAT_BLOCKED'], $chat_session['user_activity']);
			echo("LogMessage(0,'$time','$name','$text','000000');\n");
			exit;
		}
		$text = trim(utf8_normalize_nfc($this->request->variable('text', '', true))); 
		/*
		// Words longer than 70 symbols are not allowed
		$data = explode(' ', $text);
		$text = "";
		foreach($data as $word)
		{
			if(utf8_strlen($word) > 70) $word = utf8_substr($word, 0, 70);
			$text .= $word . " ";
		}
		$text = trim($text);
		*/
		// Messages longer than 255 symbols are not allowed
        
		if(utf8_strlen($text) > 255) $text = utf8_substr($text, 0, 255);
		$color	= $this->request->variable('color', '000000');
		if (!preg_match('#^[0-9a-f]{6}$#', $color)) $color = '000000';
		if($text!='')
		{
			$message = array(
				'user_id'	=> $this->user->data['user_id'],
				'username'	=> $this->user->data['username'],
				'time'		=> time(),
				'text'		=> $text,
				'color'		=> $color,
			'hidemessage' => true
			);
			$sql = "INSERT INTO " . CHAT_MESSAGES_TABLE . " " . $this->db->sql_build_array('INSERT', $message);
			$this->db->sql_query($sql);
		}
  	exit;


	case ACT_DEL:
		$ID = $this->request->variable('ID', '');
		if ($this->auth->acl_gets('a_', 'm_'))
		{
			$sql = " UPDATE " . CHAT_MESSAGES_TABLE . "  SET hidemessage = not hidemessage  WHERE msg_id =" . $ID ;
		}else 
		{
			$sql = " UPDATE " . CHAT_MESSAGES_TABLE . "  SET hidemessage = not hidemessage  WHERE user_id	=". $this->user->data['user_id']." and msg_id =" . $ID ;
		}
		$this->db->sql_query($sql);
		echo("SetLastId($ID);\n");	
  	exit;

	// Chat sync
	case ACT_SYNC:
		//include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
        include($this->phpbb_root_path . 'includes/message_parser.' . $this->php_ext);

		// Users list
		$sql = "SELECT user_id as id, username as name, user_status as status FROM " . CHAT_SESSIONS_TABLE; // . " WHERE status != " . STATUS_HIDDEN;
		$json = json_encode($this->db->sql_fetchrowset($this->db->sql_query($sql)));
		echo("SetUsers($json);\n");
        
		// Output new messages
		$visiblemes=$this->request->variable('visiblemes','');
		//if ($visiblemes == 'on')
		//	{$last_id = 0;}
		//else
		$last_id = $this->request->variable('lastid', 0);
		$sql = "SELECT * FROM " . CHAT_MESSAGES_TABLE ;
		$sql .= " WHERE msg_id > " . $last_id ;
		if ($visiblemes == 'on')
			{$sql .= " and hidemessage= false " ;}
		else
			{$sql .= " and hidemessage= true ";}	
		$sql.=" ORDER BY msg_id";	
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			if($row['msg_id'] > $last_id) $last_id = $row['msg_id'];
			$msg_id = $row['msg_id'];
			$username = addslashes($row['username']);
			$user_id = $row['user_id'];
            
			$color = addslashes($row['color']);
            //echo($this->user->format_date($row['time'], "H:i", true));
			$time = addslashes($this->user->format_date($row['time'], "H:i", true));
			$text = trim($row['text']);
			if($text == MSG_JOIN)
			{
				echo("LogUserJoin($msg_id, '$time', '$username');\n");
				continue;
			}
			if($text == MSG_LEFT)
			{
				echo("LogUserLeft($msg_id, '$time', '$username');\n");
				continue;
			}

			// Handle private messages
			if ($row['hidemessage'] == true || $visiblemes == 'on' )
			{$show = true;}
			else 
			{$show = false;} 
			if( utf8_substr($text, 0, utf8_strlen("private ["))=="private [" )
			{
				$show = false;
				$tmp = $text;
				while(utf8_strpos($tmp, "private [")===0)
				{
					$endp = utf8_strpos($tmp, "]");
					$to = str_replace("private [", "", utf8_substr($tmp, 0, $endp));
					if($to == $this->user->data['username']) $show = true;
					$tmp = trim(utf8_substr($tmp, $endp+1));
				}
				//echo($tmp);
				$msgpriv = trim(utf8_substr($text, 0, utf8_strlen($text)-utf8_strlen($tmp)));
				$text = "<span class=\"private\">" . $msgpriv . "</span> " . $tmp;
			}
			if((!$show) && ($this->user->data['username'] != $row['username']) ) continue;

			// Parse smilies and links in the message
//            echo($text);
			if(utf8_strlen($text)>1)
			{
				$message_parser = new \parse_message($text);
				$message_parser->magic_url(false);
				$message_parser->smilies(0);
				$text = (string) $message_parser->message;
				unset($message_parser);
				$text = str_replace("<a ", "<a target='_blank' ", $text);
                
				$text = str_replace("{SMILIES_PATH}", "{$phpbb_root_path}{$this->config['smilies_path']}", $text);
                
			}
			$text = str_replace("to [".$this->user->data['username']."]", "<span class=\"to\">to [".$this->user->data['username']."]</span>", $text);
			$text = addslashes(str_replace(array("\r", "\n"), ' ', $text));
			echo("LogMessage($msg_id, '$time', '$username', '$text', '$color');\n");
		}
		echo("SetLastId($last_id);\n");

		// Delete obsolete messages
		$sql = "DELETE FROM " . CHAT_MESSAGES_TABLE . " WHERE msg_id < " . ($last_id - $message_limit);
		$this->db->sql_query($sql);
	exit;
}
        
        
// окончание 
		//page_header($this->user->lang('CHAT'));

		//$this->template->set_filenames(array('body' => 'chat_body.html'));

		//page_footer();
		//return new Response($this->template->return_display('body'), 200);
	}
}