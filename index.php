<?php
// Check PHP version >= 5.5
if (version_compare(PHP_VERSION, getenv('MIN_PHP_VERSION'), '<')) {
	http_response_code(500);
	exit(sprintf('PHP version %s or greater is needed', getenv('MIN_PHP_VERSION')));
}
// Setup Autoloader
set_include_path(realpath(getenv('AUTOLOAD_DIR')) . PATH_SEPARATOR . get_include_path());
spl_autoload_extensions(getenv('AUTOLOAD_EXTS'));
spl_autoload_register(getenv('AUTOLOAD_FUNC'));

// Set Console logger and other classes
$console = \shgysk8zer0\Core\Console::getInstance()->asErrorHandler()->asExceptionHandler();
$timer = new \shgysk8zer0\Core\Timer();
$headers = \shgysk8zer0\Core\Headers::getInstance();
$url = new \shgysk8zer0\Core\URL();

// Verify that HTML is in the Accept header
if (in_array('text/html', explode(',', $headers->accept))) {
	$dom = new \shgysk8zer0\DOM\HTML();
	$dom->head->append('title', 'HTTP-OS');
	$dom->head->append('link', null, ['rel' => 'prefetch', 'href' => 'combined.svg', 'type' => 'image/svg+xml']);

	// Load all JavaScript async
	\shgysk8zer0\Core\ArrayObject::of(
		'scripts/custom.es6',
		'scripts/std-js/polyfills/element/close.js',
		'scripts/std-js/polyfills/element/remove.js',
		'scripts/std-js/polyfills/element/show.js',
		'scripts/std-js/polyfills/element/showModal.js',
		'scripts/std-js/appcache_listener.es6',
		'scripts/std-js/deprefixer.es6',
		'scripts/std-js/prototypes.es6',
		'scripts/std-js/zq.es6',
		'scripts/std-js/support_test.es6',
		'scripts/std-js/functions.es6',
		'scripts/std-js/json_response.es6'
	)->reduce(function(\DOMElement $head, $script) {
		$head->append('script', null, [
			'type' => 'application/javascript',
			'src' => $script,
			'asyne' => '',
			'defer' => ''
		]);
		return $head;
	}, $dom->head);

	// Import stylesheet
	$dom->head->append('link', null, [
		'rel' => 'stylesheet',
		'href' => 'stylesheets/styles/import.css',
		'media' => 'all'
	]);

	$dom->body->append('header')->append('h1', 'HTTP-OS');
	$dom->body->append('a', 'Home', ['href' => $url]);

	$details = $dom->body->append('main')->append('details');
	$details('summary', 'Included files');

	$footer = $dom->body->append('footer');
	\shgysk8zer0\Core\ArrayObject::from([
		'mark-github' => 'https://github.com/SuperUser/HTTP-OS',
		'issue-opened' => 'https://github.com/SuperUser/HTTP-OS/issues',
		'mail' => 'mailto:shgysk8zer0@gmail.com',
		'Facebook' => '#',
		'twitter' => 'https://twitter.com/shgysk8zer0',
		'Google_plus' => 'https://plus/google.com/+ChrisZuber'
	])->map(
		function($link, $icon) use ($footer)
		{
			$footer('a', null, [
				'href' => $link,
				'target' => '_blank'
			])->append('svg', null, [
				'width' => 50,
				'height' => 50
			])->append('use', null, [
				'xlink:href' => "combined.svg#{$icon}"
			]);
		}
	);
	// $svg = $footer('a', null, [
	// 	'href' => 'https://github.com/SuperUser/HTTP-OS',
	// 	'target' => '_blank'
	// 	])->append('svg', null, ['height' => 50, 'width' => 50]);
	// $svg('use', null, ['xlink:href' => 'combined.svg#mark-github']);

	// List all included files
	\shgysk8zer0\Core\ArrayObject::from(get_included_files())->reduce(
		function(\DOMElement $list, $file)
		{
			$list('li', $file);
			return $list;
		},
		$details('ol')
	);

	// Log loading time and exit
	$console->info("Loaded in $timer seconds.");
	$console->log($dom);
	exit($dom);
} elseif (in_array('application/json', explode(',', $headers->accept))) {
	$headers->content_type = 'application/json';
	$resp = new \shgysk8zer0\Core\JSON_Response();
	$resp->notify('HTTP-OS', 'Hello World');
	exit($resp);
} else {
	http_response_code(\shgysk8zer0\Core_API\Abstracts\HTTPStatusCodes::NOT_ACCEPTABLE);
}
