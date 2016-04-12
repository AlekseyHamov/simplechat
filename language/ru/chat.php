<?php
if (!defined('IN_PHPBB')) exit;
if (empty($lang) || !is_array($lang)) $lang = array();

$lang = array_merge($lang, array(
	'CHAT'					=> 'Чат',
	'COLOR'					=> 'Цвет',
	'UPDATING'				=> 'Обновление',
	'SENDING'				=> 'Отправка',
	'SERVER_ERROR'			=> 'Ошибка соединения',
	'SOUND'					=> 'Звук',
	'SAY'					=> 'Сказать',
	'PRIVATE'				=> 'Приватное сообщение',
	'LOGIN_EXPLAIN_CHAT'	=> 'Вам необходимо авторизоваться, чтобы войти в чат.',
	'CHAT_BANNED'			=> 'Доступ к чату для вас закрыт.',
	'CHAT_BLOCKED'			=> 'Вы заблокированы и не можете писать из-за флуда. Блокировка продлится %1$s сек.',
	'USER_JOINED'			=> 'Нас приветствует',
	'USER_LEFT'				=> 'Нас покидает',
	'SECONDS'				=> 'сек.',
	'NOW_IN_CHAT'			=> 'Сейчас в чате',
	'N_MESSAGES'			=> 'сообщений',
	'N_UPDATES'				=> 'обновлений',
	// Будьте добры, не удаляйте копирайт и ссылки!
	'POWERED_BY_CHAT'			=> 'Работает на phpBB Simple Chat',
));
