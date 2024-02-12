<?php

	// Enable error reporting for development (disable in production)
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

	require_once __DIR__ . '/lib/DSAReport.php';
	$DSAReport = new DSAReport();
?><!DOCTYPE html>
<html lang="de">

<head>

	<meta charset="UTF-8">
	<meta name="robots" content="noindex,nofollow,noarchive,nosnippet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Meldung rechtswidriger Inhalt</title>

	<style>
		html {
			margin: 0;
			padding: 0;
			font-size: 62.5%;
		}

		body {
			font-family: Arial, sans-serif;
			line-height: 1.6;
			width: 100%;
			word-break: break-word;
			hyphens: auto;
		}

		h1 {
			font-size: 3.2rem;
			line-height: 3.6rem;
		}

		.reportFormContainer{
			max-width: 80em;
			width: 100%;
			margin: auto;
			padding: 0 2rem;
		}

		label {
			display: block;
			font-size: 1.8rem;
			line-height: 2.2rem;
			margin: 10px 0 5px;
		}

		.intro, #submit-result{
			font-weight: bold;
			font-size: 1.6rem;
			line-height: 2.6rem;
			margin-bottom: 3rem;
		}

		input[type=text],
		input[type=email],
		textarea {
			width: 100%;
			padding: 10px;
			margin-bottom: 10px;
			border-radius: 5px;
			border: 1px solid #ccc;
		}

		input[type="checkbox"] {
			margin: 0.4rem 0.7rem 0.5rem 0rem;
		}

		button.report-submit-button {
			padding: 10px 20px;
			border: none;
			border-radius: 5px;
			background: #175b96;
			color: white;
			cursor: pointer;
			width: 100%;
			margin: 2rem 0 2rem;
		}

		@media (min-width: 600px) {
			button.report-submit-button {
				max-width: 270px;
			}
		}

		textarea {
			overflow-y: hidden;
		}

		input.error, textarea.error {
			border-color: red;
		}

		.error-message{
			font-size: 1.6rem;
			line-height: 2rem;
			color: red;
			margin-bottom: 2rem;
			opacity: 0;
			max-height: 0;
			overflow: hidden;
			transition: opacity 1.2s, max-height 2s;
			visibility: hidden;
		}

		.error-message.show {
			opacity: 1;
			max-height: 10rem;
			visibility: visible;
		}

		button.visually-disabled {
			background-color: #ccc;
			color: #666;
			cursor: not-allowed;
		}

		.email-field {
			display: none;
		}

		.checkbox-line{
			display: flex;
			align-items: start;
			margin: 10px 0 5px;
		}

		.checkbox-line label{
			margin: 0;
		}
		*, *::before, *::after {
			box-sizing: border-box;
		}
	</style>
</head>

<body>
	<div class="reportFormContainer">
		<h1>Meldung rechtswidriger Inhalte</h1>
		<form id="reportForm" aria-label="Formular für die Meldung rechtswidriger Inhalte">

			<div class="intro">
				Dieses Formular dient dazu, Inhalte zu melden, die Ihrer Meinung nach rechtswidrig sind. Mit Ihrer Meldung helfen Sie uns, schnell und effektiv gegen solche Inhalte vorzugehen. Nach dem Absenden der E-Mail, die über den "Meldung erstellen"-Button erzeugt wird, wird Ihre Meldung an unser Postfach für Meldungen rechtswidriger Inhalte nach Verordnung (EU) 2022/2065, Artikel 16 ("Digital Services Act") zugestellt. Sie erhalten anschließend automatisch eine Eingangsbestätigung an Ihre Absenderadresse. Sobald Ihre Meldung bearbeitet wurde, informieren wir Sie über die ergriffenen Maßnahmen. Beachten Sie bitte, dass keine Rückmeldung zu den Maßnahmen erfolgen wird, falls die Prüfung die Meldung als missbräuchlich einstuft (z.B. Spam oder anlasslose Meldung).
			</div>

			<label for="urls">URL(s) zum beanstandeten Inhalt:</label>
			<textarea
				type="text"
				id="urls"
				name="urls"
				aria-required="true"
				placeholder="Bitte erfassen Sie die präzise URL-Adresse(n) und ggf. weitere Angaben zur Ermittlung der rechtswidrigen Inhalte"
			></textarea>
			<div id="urls-error"></div>

			<label for="offense">Grund der Meldung:</label>
			<textarea
				type="text"
				id="offense"
				name="type"
				aria-required="true"
				minlength="3"
				placeholder="Bitte spezifizieren Sie den rechtlichen Verstoß, z.B. Urheberrechtsverletzung, Hassrede, illegale Inhalte."
			></textarea>
			<div id="offense-error"></div>

			<label for="reason">Begründete Erläuterung der Meldung:</label>
			<textarea
				id="reason"
				name="reason"
				aria-required="true"
				minlength="50"
				placeholder="Bitte geben Sie eine hinreichend begründete Erläuterung, warum Sie die fraglichen Informationen als rechtswidrige Inhalte ansehen."
			></textarea>
			<div id="reason-error"></div>

			<div class="checkbox-line">
				<input type="checkbox" id="abuse" name="abuse">
				<label for="abuse">Es handelt sich mutmaßlich um einen Rechtsverstoß aus dem Bereich Kindesmissbrauch (Bekämpfung des sexuellen Missbrauchs und der sexuellen Ausbeutung von Kindern sowie der Kinderpornografie, Anwendungsbereich von Richtlinie 2011/93/EU )</label>
			</div>

			<label for="fullname">Vorname und Name der meldenden Person:</label>
			<input type="text" id="fullname" name="fullname">
			<div id="fullname-error"></div>

			<div class="email-field">
				<label for="email">E-Mail:</label>
				<input type="email" id="email" name="email">
				<div>
					Die Angabe Ihres Namens und Ihrer E-Mail-Adresse ist bei Meldungen zu mutmaßlichem Kindesmissbrauch freiwillig, wir gehen der Meldung auch nach, wenn Sie uns dise Daten nicht mitteilen. Bitte beachten Sie, dass wir Sie zu im Zusammenhang mit Ihrer Meldung getroffenen Maßnahmen nur informieren können, wenn Sie uns Ihre E-Mail-Adresse mitteilen.
				</div>
			</div>

			<div class="checkbox-line">
				<input type="checkbox" id="confirmation" name="confirmation" aria-required="true">
				<label for="confirmation">Ich bin in gutem Glauben davon überzeugt, dass die in der Meldung enthaltenen Angaben und Anführungen richtig und vollständig sind.</label>
			</div>
			<div id="confirmation-error"></div>

			<button type="submit" id="submitBtn" class="report-submit-button visually-disabled">Meldung erstellen</button>
		</form>
		<div id="submit-result"></div>
	</div>
<template id="emailBodyTemplate">Betreff: Meldung eines rechtswidrigen Inhalts gemäß Verordnung (EU) 2022/2065, Artikel 16

Sehr geehrte Damen und Herren,

hiermit melde ich einen Inhalt, der meiner Einschätzung nach gegen geltendes Recht verstößt und somit als rechtswidrig einzustufen ist. Nachstehend finden Sie die notwendigen Details, die eine Prüfung und entsprechende Maßnahmen Ihrerseits ermöglichen sollten:

URL des betreffenden Inhalts:
{{urls}}

Beschreibung des rechtswidrigen Inhalts:
{{offense}}

Grund der Meldung: 
{{reason}}

Beweise/Belege: [Falls vorhanden, fügen Sie weitere Links oder Screenshots als Beweis bei]

Ich bin in gutem Glauben davon überzeugt, dass die in der Meldung enthaltenen Angaben und Anführungen richtig und vollständig sind.

Ich bitte um eine Überprüfung gemäß Artikel 16 der Verordnung (EU) 2022/2065 und um die Ergreifung angemessener Maßnahmen zur Entfernung oder Sperrung des Zugangs zu den gemeldeten Inhalten.

Für Rückfragen stehe ich gerne zur Verfügung und erwarte Ihre baldige Rückmeldung über die unternommenen Schritte.

Mit freundlichen Grüßen,

{{fullname}}</template>
	<template id="wsSubmitResultTemplate">
		<div class="ws-success-{{success}}">
			<div class="ws-info">{{info}}</div>
			<div class="ws-message">{{message}}</div>
		</div>
	</template>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const recipientMail = '<?php echo $DSAReport->getMailrecipient(); ?>';
			const dsaAllowDomains = <?php echo json_encode($DSAReport->getDsaAllowDomains()); ?>;
			const webserviceURL = '<?php echo $DSAReport->getWebserviceURL(); ?>';

			const form = document.getElementById('reportForm');
			const urls = document.getElementById('urls');
			const offense = document.getElementById('offense');
			const reason = document.getElementById('reason');
			const abuseCheckbox = document.getElementById('abuse');
			const fullname = document.getElementById('fullname');
			const emailField = document.querySelector('.email-field');
			const emailInput = document.getElementById('email');
			const confirmation = document.getElementById('confirmation');
			const submitBtn = document.getElementById('submitBtn');
			const submitResultDiv = document.getElementById('submit-result');

			const makeTextareaAutoExpand = function(textareaElement) {
				function autoExpand() {
					// Reset field height to auto to ensure the height shrinks when deleting text, if necessary
					textareaElement.style.height = 'auto';

					// Calculate the new height based on scrollHeight
					const newHeight = textareaElement.scrollHeight + "px";

					// Set the new height
					textareaElement.style.height = newHeight;
				}

				// Add the autoExpand function as an event listener for input events
				textareaElement.addEventListener('input', autoExpand);

				// Initialize the height adjustment in case there's initial content
				autoExpand();
			}

			makeTextareaAutoExpand(urls);
			makeTextareaAutoExpand(offense);
			makeTextareaAutoExpand(reason);

			const urlRegex = /^(https?:\/\/)?([\w\d-]+\.)*([\w\d-]+\.[\w\d-]+)\/?.*$/;
			const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

			const debounce = function(func, delay) {
				let debounceTimer;
				return function() {
					const context = this, args = arguments;
					clearTimeout(debounceTimer);
					debounceTimer = setTimeout(() => func.apply(context, args), delay);
				};
			}

			const safeRemoveElement = function(element) {
				if (element && element.parentNode) {
					element.parentNode.removeChild(element);
				}
			}

			const displayError = function(input, message) {
				const errorDiv = document.createElement('div');
				errorDiv.textContent = message;
				errorDiv.className = 'error-message';

				input.classList.add('error');
				const messageContainer = document.getElementById(`${input.id}-error`);
				if (messageContainer && !messageContainer.querySelector('.error-message')) {
					messageContainer.appendChild(errorDiv);
					errorDiv.classList.add('show');
				}
				setTimeout(() => {
					setTimeout(() => {
						errorDiv.classList.remove('show');
						setTimeout(() => safeRemoveElement(errorDiv), 2000);
					}, 2000);
				}, 6000);
			}

			const compileMessageBody = function() {
				const template = document.getElementById('emailBodyTemplate').innerHTML;
				const urls = document.getElementById('urls').value;
				return template.replace('{{urls}}', urls)
										.replace('{{reason}}', reason.value)
										.replace('{{fullname}}', fullname.value)
										.replace('{{offense}}', offense.value);
			}

			const updateButtonState = function(isValid) {
				if (isValid) {
					submitBtn.classList.remove('visually-disabled');
				} else {
					submitBtn.classList.add('visually-disabled');
				}
			}

			const validateForm = function() {
				let isValid = true;
				const urlsValid = urls.value.split(',').some(url => {
					const match = url.match(urlRegex);
					return match && dsaAllowDomains.includes(match[3]);
				});

				removeErrorMessage(urls);
				removeErrorMessage(offense);
				removeErrorMessage(reason);
				removeErrorMessage(fullname);
				removeErrorMessage(confirmation);
				submitResultDiv.innerHTML = '';

				if (!urlsValid || urls.value.trim() === '') {
					isValid = false;
					displayError(urls, "Bitte geben Sie eine gültige URL an; fremde URLs können nicht gemeldet werden.");
				}

				if (offense.value.length < 3) {
					isValid = false;
					displayError(offense, "Der Text für den Grund der Meldung ist zu kurz.");
				}

				if (reason.value.length < 50) {
					isValid = false;
					displayError(reason, "Der Text für die Begründung ist zu kurz.");
				}

				if (!abuseCheckbox.checked && fullname.value.length < 3){
					isValid = false;
					displayError(fullname, "Bitte geben Sie Ihren vollständigen Namen an.");
				}

				if (!confirmation.checked) {
					isValid = false;
					displayError(confirmation, "Sie müssen Ihre Angaben bestätigen.");
				}

				updateButtonState(isValid);

				return isValid;
			}

			const removeErrorMessage = function(input) {
				const messageContainer = document.getElementById(`${input.id}-error`);
				const errorElement = messageContainer.querySelector('div.error-message');
				if (errorElement) {
					safeRemoveElement(errorElement);
				}
				input.classList.remove('error');
			}

			abuseCheckbox.addEventListener('change', function() {
				if (abuseCheckbox.checked) {
					removeErrorMessage(fullname);
					emailField.style.display = 'block';
				} else {
					emailField.style.display = 'none';
				}
			});

			urls.addEventListener('input', debounce(validateForm, 50));
			reason.addEventListener('input', debounce(validateForm, 50));
			offense.addEventListener('change', debounce(validateForm, 50));
			confirmation.addEventListener('change', debounce(validateForm, 50));
			submitBtn.addEventListener('click', (e) => {
				e.preventDefault();
				const isValid = validateForm();
				if (isValid){
					// compile message
					const fqdn = urls.value.match(urlRegex)[3];
					let subject = `Meldung zu rechtswidrigen Inhalten auf ${fqdn}`;
					let body = compileMessageBody();

					// check if we need to compile an e-mail for the client or use the webservice
					const useEmail = (!abuseCheckbox.checked || emailRegex.test(emailInput));

					if(useEmail){
						subject = encodeURIComponent(subject);
						body = encodeURIComponent(body);
						const mailtoLink = `mailto:${recipientMail}?subject=${subject}&body=${body}`;
						window.location.href = mailtoLink;
					} else {
						
						// we need to use the webservice
						fetch(webserviceURL, {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
							},
							body: JSON.stringify({
								method: 'sendMessage',
								subject: subject,
								body: body,
								abuse: abuseCheckbox.checked,
								token: '<?php echo $DSAReport->generateCSRFToken(); ?>'
							})
						})
						.then(response => response.json())
						.then(data => {
							let info;
							if (data.success){
								info = 'Vielen Dank für Ihre Meldung. Wir werden diese nun prüfen und bei berechtigten Meldungen unverzüglich die notwendigen Maßnahmen ergreifen.';
								safeRemoveElement(submitBtn);
							} else {
								info = 'Leider konnte Ihre Meldung nicht gespeichert werden.';
							}
							const template = document.getElementById('wsSubmitResultTemplate').innerHTML;
							submitResultDiv.innerHTML = template.replace('{{success}}', data.success)
										.replace('{{info}}', info)
										.replace('{{message}}', data.message||'');
						});
					}

				}
			});

		});
	</script>
</body>
</html>