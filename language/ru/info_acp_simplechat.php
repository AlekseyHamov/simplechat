<?php
/**
*
* mytest [Russian]
*
* @package My test
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'ACP_SIMPLECHAT'                => 'Настройка чата',
    'ACP_SIMPLECHAT_EXPLAIN'        => 'Здесь можно настроить параметры расширения.',
    'ACP_SIMPLECHAT_SHOW_GREETING'     => 'Добавлять приветсивие',
	'ACP_SIMPLECHAT_PRIMER'     	   => 'Пример заполнения: Да/Нет',
    'ACP_SIMPLECHAT_MESSAGES_LIMIT'  => 'Сохранение сообщений  PS Влияет на скорость',
	'ACP_SIMPLECHAT_ANTIFLOOD_SENSITIVITY' =>  'Не более подряд сообщений',
    'ACP_SIMPLECHAT_ANTIFLOOD_EXTINCTION' =>   'Набор сообщения меньше чем (сек)',
    'ACP_SIMPLECHAT_ANTIFLOOD_DURATION' =>     'Время блокировки флудера (сек)',
	'ACP_CHATBOT_NAME' =>	'Имя бота в чате',
    'ACP_SIMPLECHAT_CHAT_BOT' =>     'Чат бот',
	'ACP_SIMPLECHAT_EXCLUDED'=>'Выделить исключаемые форумы для бота',
	
));