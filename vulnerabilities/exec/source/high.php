<?php

if( isset( $_POST[ 'Submit' ]  ) ) {
	// Get input
	$target = trim( $_REQUEST[ 'ip' ] );

	// FIX CWE-78 (1): Whitelist validation - only accept a valid IPv4/IPv6 address.
	// Replaces the old blacklist, which could be bypassed (e.g. "127.0.0.1|whoami").
	if( filter_var( $target, FILTER_VALIDATE_IP ) === false ) {
		$html .= '<pre>ERROR: You have entered an invalid IP.</pre>';
	}
	else {
		// FIX CWE-78 (2): Escape the argument so the shell treats it as a single literal value.
		$safeTarget = escapeshellarg( $target );

		// Determine OS and execute the ping command.
		if( stristr( php_uname( 's' ), 'Windows NT' ) ) {
			// Windows
			$cmd = shell_exec( 'ping  ' . $safeTarget );
		}
		else {
			// *nix
			$cmd = shell_exec( 'ping  -c 4 ' . $safeTarget );
		}

		// FIX (3): Encode output to prevent XSS.
		$html .= '<pre>' . htmlspecialchars( $cmd ) . '</pre>';
	}
}

?>
