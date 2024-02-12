<?php

	// Enable error reporting for development (disable in production)
	// ini_set('display_errors', 1);
	// error_reporting(E_ALL);

	require_once __DIR__ . '/lib/DSAReport.php';
	$DSAReport = new DSAReport();
?><!DOCTYPE html>
<html lang="de">

<head>

	<meta charset="UTF-8">
	<meta name="robots" content="noindex,nofollow,noarchive,nosnippet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Meldung rechtswidriger Inhalt</title>
	<link href="./DSAReport.css" rel="stylesheet" type="text/css"/>
</head>

<body>
	<?php readfile('./lib/formTemplate.inc.html'); ?>

	<template id="emailBodyTemplate"><?php readfile('./lib/mailbodyTemplate.inc.html'); ?></template>

	<template id="wsSubmitResultTemplate">
		<div class="ws-success-{{success}}">
			<div class="ws-info">{{info}}</div>
			<div class="ws-message">{{message}}</div>
		</div>
	</template>
	<script src="./DSAReport.js"></script>
	<script>
		DSAReportSetup(
			'<?php echo $DSAReport->getMailrecipient(); ?>',
			<?php echo json_encode($DSAReport->getDsaAllowDomains()); ?>,
			'<?php echo $DSAReport->getWebserviceURL(); ?>',
			'<?php echo $DSAReport->generateCSRFToken(); ?>'
		);
	</script>
</body>
</html>