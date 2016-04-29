<?php

defined('__ROOT__') or define('__ROOT__', realpath(__DIR__ . '/..'));

defined('__APP__') or define('__APP__', realpath(__ROOT__ . '/app'));

defined('__CONTROLLER__') or define('__CONTROLLER__', 'App\\Controller');

defined('VIEW_ROOT') or define('VIEW_ROOT', realpath(__ROOT__ . '/resource/views'));

defined('CONFIG_ROOT') or define('CONFIG_ROOT', realpath(__ROOT__ . '/config'));

defined('VIEW_SUFFIX') or define('VIEW_SUFFIX', '.php');

defined('DEFALULT_ACTION') or define('DEFALULT_ACTION', 'index');

defined('DEBUG') or define('DEBUG', true);