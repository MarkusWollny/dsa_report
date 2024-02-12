
component{

	/**
	 * the constructor
	 * 
	 * reads the config.inc.php from the same directory
	 */
	public any function init(){
		try{
			var fConfig = FileRead('config.inc.php');
			fConfig = ReReplace(fConfig, "(?s).*\$config\s*=\s*\[(.*)\].*", "{\1}");
			fConfig = replace(fConfig, "=>", ":", "all");
			fConfig = evaluate(fConfig);
			if(checkConfig(fConfig)){
				variables.config = fConfig;
			};
		} catch(any e){}

		if (!StructKeyExists(variables, "config")){
			Throw("PHP config file missing or malformed.");
		}

		variables.CSRFvalidPeriod = 7200;
		
		return THIS;
	}

	/**
	 * this returns the current token for protection against
	 * Cross Site Request Forgery attacks
	 * 
	 * @return String CSRF token
	 */
	public string function generateCSRFToken() {
		var timestamp	= getCurrentTimestamp();
		var dataToHash	= "#timestamp#:#config.secretKey#";
		var hash		= LCase(hash(dataToHash, "SHA-256"));
		var token		= "#hash#:#timestamp#";
		return token;
	}

	/**
	 * returns the mail recipient for DSA reports from the configuration
	 * 
	 * @return String the recipient of DSA reports
	 */
	public string function getMailrecipient(){
		return config.recipientMail;
	}

	/**
	 * returns the array of allowed domains from the configuration
	 * 
	 * @return Array the allowed domains
	 */
	public array function getDsaAllowDomains(){
		return config.dsaAllowDomains;
	}

	/**
	 * returns the URL of the webservice endpoint from the configuration
	 * 
	 * @return String the webservice endpoint url
	 */
	public string function getWebserviceURL(){
		return config.webserviceURL;
	}

	/**
	 * gets the current time in seconds since the Unix epoch
	 */
	private numeric function getCurrentTimestamp(){
		return int(now().getTime() / 1000);
	}

	/**
	 * checks validity of the configuration read from the
	 * PHP config file
	 *
	 * @structConfig Struct config to check
	 */
	private boolean function checkConfig(any structConfig){
		if (!isStruct(structConfig)) return false;
		if (!len(structConfig?.recipientMail)) return false;
		if (!len(structConfig?.secretKey)) return false;
		if (!len(structConfig?.senderMail)) return false;
		if (!len(structConfig?.webserviceURL)) return false;
		if (!IsArray(structConfig?.dsaAllowDomains) || !arrayLen(structConfig.dsaAllowDomains)) return false;
		return true;
	}
}