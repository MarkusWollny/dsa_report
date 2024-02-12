<cfset DSAReport = new lib.DSAReport() /><!DOCTYPE html>
<html lang="de">
<head>

	<meta charset="UTF-8">
	<meta name="robots" content="noindex,nofollow,noarchive,nosnippet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Meldung rechtswidriger Inhalt</title>
	<link href="./DSAReport.css" rel="stylesheet" type="text/css"/>
</head>

<body>
	<cfinclude template="./lib/formTemplate.inc.html" />

	<template id="emailBodyTemplate"><cfinclude template="./lib/mailbodyTemplate.inc.html" /></template>

	<template id="wsSubmitResultTemplate">
		<div class="ws-success-{{success}}">
			<div class="ws-info">{{info}}</div>
			<div class="ws-message">{{message}}</div>
		</div>
	</template>
	<script src="./DSAReport.js"></script>
	<script>
	<cfoutput>
		DSAReportSetup(
			'#DSAReport.getMailrecipient()#',
			#SerializeJSON(DSAReport.getDsaAllowDomains())#,
			'#DSAReport.getWebserviceURL()#',
			'#DSAReport.generateCSRFToken()#'
		);
	</cfoutput>
	</script>
</body>
</html>