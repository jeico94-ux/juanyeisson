<?php

if( isset( $_GET[ 'Submit' ] ) ) {
	// Get input
	$id = $_GET[ 'id' ];
	$exists = false;

	switch ($_DVWA['SQLI_DB']) {
		case MYSQL:
			// FIX CWE-89: Prepared statement. The user input travels as a bound
			// parameter and can never change the structure of the SQL query.
			$exists = false;
			try {
				$stmt = mysqli_prepare( $GLOBALS["___mysqli_ston"], "SELECT first_name, last_name FROM users WHERE user_id = ?;" );
				mysqli_stmt_bind_param( $stmt, 'i', $id );
				mysqli_stmt_execute( $stmt );
				mysqli_stmt_store_result( $stmt );
				$exists = ( mysqli_stmt_num_rows( $stmt ) > 0 );
				mysqli_stmt_close( $stmt );
			} catch (Exception $e) {
				print "There was an error.";
				exit;
			}
			((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
			break;
		case SQLITE:
			global $sqlite_db_connection;

			// FIX CWE-89: Prepared statement with typed parameter.
			$query = "SELECT first_name, last_name FROM users WHERE user_id = :id;";
			try {
				$stmt = $sqlite_db_connection->prepare($query);
				$stmt->bindValue(':id', $id, SQLITE3_INTEGER);
				$results = $stmt->execute();
				$row = $results->fetchArray();
				$exists = $row !== false;
			} catch(Exception $e) {
				$exists = false;
			}

			break;
	}

	if ($exists) {
		// Feedback for end user
		$html .= '<pre>User ID exists in the database.</pre>';
	} else {
		// User wasn't found, so the page wasn't!
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' 404 Not Found' );

		// Feedback for end user
		$html .= '<pre>User ID is MISSING from the database.</pre>';
	}

}

?>
