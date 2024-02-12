const DSAReportSetup = function(
	recipientMail,
	dsaAllowDomains,
	webserviceURL,
	token
){
	document.addEventListener('DOMContentLoaded', function() {
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
							token: token
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

};