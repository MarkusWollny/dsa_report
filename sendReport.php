<?php

	// Enable error reporting for development (disable in production)
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	header('Content-Type: application/json');

	require_once __DIR__ . '/lib/DSAReport.php';
	$DSAReport = new DSAReport();

	// Validate the origin of the request
	$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
	if (!$DSAReport->validateOrigin($origin)) {
		echo json_encode(['success' => false, 'message' => 'Aufruf von nicht autorisierter Seite.']);
		exit;
	} else {
		header("Access-Control-Allow-Origin: $origin");
		header('Access-Control-Allow-Methods: POST, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type');
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		// Get JSON as a string
		$json_str = file_get_contents('php://input');
		
		// Decode the JSON string into an object
		$json_obj = json_decode($json_str);
	
		// Validate CSRF token before proceeding
		if (!$DSAReport->validateCSRFToken($json_obj->token)) {
			echo json_encode(['success' => false, 'message' => 'Token ungültig oder abgelaufen']);
			exit;
		}
	
		// Process the request based on the "method" value
		switch ($json_obj->method) {
			case 'sendMessage':
				$response = $DSAReport->sendMessage($json_obj->subject, $json_obj->body);
				echo json_encode($response);
				break;
			default:
				// http_response_code(501); // Method Not Implemented
				echo json_encode(['success' => false, 'message' => 'Ungültiger Aufruf']);
				break;
		}
	} else {
		// Not a POST request
		if ($_SERVER['REQUEST_METHOD'] !== 'OPTIONS'){
			http_response_code(405); // Method Not Allowed
			echo json_encode(['success' => false, 'message' => 'Invalid request method']);
		}
	}
	
	?>