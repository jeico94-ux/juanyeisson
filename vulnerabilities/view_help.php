<?php

define( 'DVWA_WEB_PAGE_TO_ROOT', '../' );
require_once DVWA_WEB_PAGE_TO_ROOT . 'dvwa/includes/dvwaPage.inc.php';

dvwaPageStartup( array( 'authenticated' ) );

$page = dvwaPageNewGrab();
$page[ 'title' ] = 'Help' . $page[ 'title_separator' ].$page[ 'title' ];

if (array_key_exists ("id", $_GET) &&
	array_key_exists ("security", $_GET) &&
	array_key_exists ("locale", $_GET)) {
	$id       = $_GET[ 'id' ];
	$security = $_GET[ 'security' ];
	$locale   = $_GET[ 'locale' ];

	// FIX CWE-94 (1): Whitelist of help files that really exist on the server.
	// Built from the filesystem, never from user input.
	$allowedFiles = glob( DVWA_WEB_PAGE_TO_ROOT . 'vulnerabilities/*/help/help*.php' );

	// Build the requested path only to look it up in the whitelist.
	if ($locale == 'en') {
		$requested = DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}/help/help.php";
	} else {
		$requested = DVWA_WEB_PAGE_TO_ROOT . "vulnerabilities/{$id}/help/help.{$locale}.php";
	}

	$index = array_search( $requested, $allowedFiles, true );

	if ( $index !== false ) {
		// FIX CWE-94 (2): No eval(). Include the trusted path taken from the whitelist,
		// not the user-controlled string.
		ob_start();
		include $allowedFiles[ $index ];
		$help = ob_get_contents();
		ob_end_clean();
	} else {
		$help = "<p>Not Found</p>";
	}
} else {
	$help = "<p>Not Found</p>";
}

$page[ 'body' ] .= "
<script src='/vulnerabilities/help.js'></script>
<link rel='stylesheet' type='text/css' href='/vulnerabilities/help.css' />

<div class=\"body padded\">
	{$help}
</div>\n";

dvwaHelpHtmlEcho( $page );

?>
