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
    'ACP_SIMPLECHAT_COLPROFILE'     => 'За сколько дней начинаем показывать',
	'ACP_ACTIVE_DAY'			   => 'Дней активности на сайте',
	'ACP_SIMPLECHAT_PRIMER'     	   => 'Пример заполнения: Да/Нет',
	
));