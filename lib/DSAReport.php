<?php

require_once __DIR__ . '/config.inc.php';

/**
 * class of helper methods to process reports as prescribed
 * by the Digital Services Act (EU) 2022/2065, Article 16
 * 
 * @author	Markus Wollny <markus.wollny@computec.de>
 */
class DSAReport {
	/**
	 * @var string
	 */
	private $secretKey;
	/**
	 * @var integer
	 */
	private $CSRFvalidPeriod;

	/**
	 * the constructor
	 */
	public function __construct() {
		global $config;
		$this->config = $config;
		$this->CSRFvalidPeriod = 7200;
	}

	/**
	 * this returns the current token for protection against
	 * Cross Site Request Forgery attacks
	 * 
	 * @return String CSRF token
	 */
	public function generateCSRFToken() {
		$timestamp = time(); // Current time in seconds since the Unix epoch
		$dataToHash = $timestamp . ":" . $this->config['secretKey']; // Combining the timestamp with the secret key
		$hash = hash('sha256', $dataToHash); // Hashing the combined string using SHA-256
		$token = $hash . ":" . $timestamp; // The token is the hash followed by the original timestamp
		return $token;
	}

	/**
	 * validates a CSFR token against the secret key and the current time
	 * 
	 * @param String $token the CSRF token passed in the request
	 * 
	 * @return Boolean true, if the token is valid, false otherwise
	 */
	public function validateCSRFToken($token) {
		list($tokenHash, $tokenTimestamp) = explode(':', $token); // Splitting the token into its hash and timestamp parts
		$dataToHash = $tokenTimestamp . ":" . $this->config['secretKey']; // Recombining the timestamp from the token with the secret key
		$expectedHash = hash('sha256', $dataToHash); // Hashing the combined string to get what the hash should be

		// Check if the hash matches and the token is not expired
		if ($tokenHash === $expectedHash) {
			$currentTime = time(); // Current time
			$timeDiff = $currentTime - $tokenTimestamp; // Difference in current time and the token's timestamp
			
			// Ensure the token is within the valid period
			if ($timeDiff <= $this->CSRFvalidPeriod) {
				return true; // Token is valid
			}
		}

		return false; // Token is invalid
	}

	/**
	 * validates the origin passed in the request against a list of allowed domains
	 * 
	 * @param String $origin the origin
	 * 
	 * @return Boolean true if the origin is allowed, false otherwise
	 */
	public function validateOrigin($origin){
		// Parse the origin to extract the domain
		$parsedUrl = parse_url($origin);
		$host = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
		$domainParts = explode('.', $host);
		$domain = '';
		if (count($domainParts) > 2) {
			// Get the last two parts of the domain name
			$domain = $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
		} else if(count($domainParts) === 2) {
			$domain = $host; // Directly use the host if it's already a second-level domain
		}
		return in_array($domain, $this->config['dsaAllowDomains']);
	}

	/**
	 * returns the mail recipient for DSA reports from the configuration
	 * 
	 * @return String the recipient of DSA reports
	 */
	public function getMailrecipient(){
		return $this->config['recipientMail'];
	}

	/**
	 * returns the array of allowed domains from the configuration
	 * 
	 * @return Array the allowed domains
	 */
	public function getDsaAllowDomains(){
		return $this->config['dsaAllowDomains'];
	}

	/**
	 * returns the URL of the webservice endpoint from the configuration
	 * 
	 * @return String the webservice endpoint url
	 */
	public function getWebserviceURL(){
		return $this->config['webserviceURL'];
	}

	/**
	 * @param String $subject the subject for the report mail
	 * @param String $body the body for the report mail
	 * 
	 * @return array[
	 *		'success' => String,
	 *		'message' => String
	 *	]
	 */
	public function sendMessage($subject, $body) {
		$to = $this->config['recipientMail'];
		$headers = 'From: ' . $this->config['senderMail'] . "\r\n" .
					'X-Mailer: PHP/' . phpversion();
		
		if(mail($to, $subject, $body, $headers)) {
			return ['success' => true, 'message' => 'Nachricht gesendet'];
		} else {
			return ['success' => false, 'message' => 'Fehler beim Senden der Nachricht'];
		}
	}
}
