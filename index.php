<?php
$path = isset($_GET['path']) ? trim($_GET['path'], '/') : 'homepage';
$PATH = explode('/', $path);
if($PATH[0] == 'admin') {
	$ENV = 'backend';
	array_shift($PATH);
	if(empty($PATH)) {
		$PATH[0] = 'homepage';
	}
	$PAGE = implode('-', $PATH);
	$URI = "";
} else {
	$ENV = 'frontend';
	$PAGE = array_shift($PATH);
	$URI = implode('/', $PATH);
}

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR. 'config.php';

if($DEBUG === true) {
	require_once CLASSES.'Debugger.php';
	ndebug();
}

$dibi = dibi::connect([
    'driver'   => DBTYPE,
    'host'     => DBHOST,
    'username' => DBUSER,
    'password' => DBPASS,
    'database' => DBNAME,
    'charset'  => DBCSET,
]);

require_once CONFIG.'config.pages.'.$ENV.'.php';
require_once CONFIG.'config.text.php';

$session = Session::getInstance();

$smarty = Smarty::getInstance();
$smarty->template_dir = TEMPLATES;
$smarty->compile_dir = TEMPLATES_C;

//d($ENV, $PATH, $PAGE, $URI);

$MAIN = 'main';

if(file_exists(SCRIPTS.'common.php')) {
	include_once SCRIPTS.'common.php';
}
if(file_exists(SCRIPTS.LANG.'_'.$PAGE.'.php')) {
	include_once SCRIPTS.LANG.'_'.$PAGE.'.php';
} elseif(file_exists(SCRIPTS.$PAGE.'.php')) {
	include_once SCRIPTS.$PAGE.'.php';
}

//check if there are any language mutations
if(file_exists(TEMPLATES.LANG.'_'.$PAGE.'.html')) {
	$TEMPLATE_LANG = LANG.'_'.$PAGE.'.html';
//common known page for all language mutations
} elseif(file_exists(TEMPLATES.$PAGE.'.html')) {
	$TEMPLATE_LANG = $PAGE.'.html';
//unknown page - keep url
} else {
	//Common::redirect(ROOT.'page404', 404);
	$PAGE = 'page404';
	include_once SCRIPTS.$PAGE.'.php';
	$TEMPLATE_LANG = $PAGE.'.html';
}

$smarty->assign('TEMPLATE', $TEMPLATE_LANG);

if(isset($_SESSION['error'])) {
	$smarty->assign('ERROR', $_SESSION['error']);
	unset($_SESSION['error']);
}
if(isset($_SESSION['alert'])) {
	$smarty->assign('ALERT', $_SESSION['alert']);
	unset($_SESSION['alert']);
}
if(isset($_SESSION['alert_type'])) {
	$smarty->assign('ALERT_TYPE', $_SESSION['alert_type']);
	unset($_SESSION['alert_type']);
}
if(isset($_SESSION['data'])) {
	$smarty->assign('DATA', $_SESSION['data']);
	unset($_SESSION['data']);
}

$smarty->assign(array(
	'ROOT' => ROOT,
	'DESIGN' => DESIGN,
	'JS' => JS,
	'LANG' => LANG,
	'PATH' => $PATH,
	'ENV' => $ENV,
	'PAGES' => $PAGES,
	'PAGE' => $PAGE,
	'URI' => $URI,
	'TEXT' => $TEXT[LANG],
	'TEXT_JS' => json_encode($TEXT[LANG]),
));

$smarty->display($MAIN.".html");
/*
$letters = '0123456789abcdef';
for($i = 0; $i < 16; $i++) {
	for($j = 0; $j < 16; $j++) {
		mkdir(FILES.substr($letters, $i, 1).substr($letters, $j, 1));
	}
}
*/
