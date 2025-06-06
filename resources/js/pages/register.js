let RegisterForm = {
    elements: {
        registerContainer: '#register-form',
        emailInput: '#register-email-input',
        passwordInput: '#register-password-input',
        passwordConfirmInput: '#register-password-confirm-input',
        firstNameInput: '#register-firstname-input',
        lastNameInput: '#register-lastname-input',
        phoneInput: '#register-phone-input',
        addressInput: '#register-address-input',
        submitButton: '#register-button',
        errorMessage: '#register-error-message',
    },
    handlers: {
        validateInputs: function(inputs) {
            const errors = [];
            if (!inputs.email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inputs.email)) {
                errors.push('Please enter a valid email address.');
            }
            if (!inputs.password || inputs.password.length < 8) {
                errors.push('Password must be at least 8 characters long.');
            }
            if (inputs.password !== inputs.passwordConfirm) {
                errors.push('Passwords do not match.');
            }
            if (!inputs.firstName || !inputs.lastName) {
                errors.push('Please fill in all required fields.');
            }
            return errors;
        },
        registerProcess: function() {
            const self = RegisterForm;
            const $ = window.$;
            const inputs = {
                email: $(self.elements.emailInput).val().trim(),
                password: $(self.elements.passwordInput).val(),
                passwordConfirm: $(self.elements.passwordConfirmInput).val(),
                firstName: $(self.elements.firstNameInput).val().trim(),
                lastName: $(self.elements.lastNameInput).val().trim(),
                phone: $(self.elements.phoneInput).val().trim(),
                address: $(self.elements.addressInput).val().trim()
            };

            const $error = $(self.elements.errorMessage);
            const $button = $(self.elements.submitButton);
            $error.text('').removeClass('is-danger is-success');

            const validationErrors = self.handlers.validateInputs(inputs);
            if (validationErrors.length > 0) {
                $error.text(validationErrors[0]).addClass('is-danger');
                return;
            }

            $button.prop('disabled', true).addClass('is-loading');

            const userData = {
                username: inputs.email,
                email: inputs.email,
                password: inputs.password,
                type: 'user',
                permissions: 16,
                personal_info: {
                    first_name: inputs.firstName,
                    last_name: inputs.lastName,
                    phone: inputs.phone,
                    address: inputs.address
                }
            };

            $.ajax({
                url: 'user/register',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(userData),
                success: function(response) {
                    self.handlers.handleRegistrationResponse(response, $error, $button);
                },
                error: function(xhr) {
                    self.handlers.handleRegistrationError(xhr, $error, $button);
                }
            });
        },
        handleRegistrationResponse: function(response, $error, $button) {
            $error.text('').removeClass('is-danger is-success');
            if (response.message && response.message.includes('User registered')) {
                $error.text('Registration successful! Please check your email for the confirmation code.').addClass('is-success');
                RegisterForm.confirmation.showCodeInput(response.email || '', true);
            } else {
                let errorText = '';
                if (response.message) {
                    errorText = response.message;
                } else if (response.error) {
                    errorText = response.error;
                } else if (response.messages) {
                    errorText = typeof response.messages === 'object' 
                        ? Object.values(response.messages).join(' ')
                        : response.messages;
                } else {
                    errorText = 'Registration failed. Please try again.';
                }
                $error.text(errorText).addClass('is-danger');
            }
            $button.prop('disabled', false).removeClass('is-loading');
        },
        handleRegistrationError: function(xhr, $error, $button) {
            const errorMsg = xhr.responseJSON 
                ? (xhr.responseJSON.messages?.error || xhr.responseJSON.error || 'Registration failed.')
                : 'An error occurred. Please try again.';
            $error.text(errorMsg).addClass('is-danger');
            $button.prop('disabled', false).removeClass('is-loading');
        }
    },
    confirmation: {
        showCodeInput: function(email, withTransition) {
            const self = RegisterForm;
            const $ = window.$;
            const $registerForm = $(self.elements.registerContainer);
            
            const transitionHandler = () => {
                $registerForm.hide();
                RegisterForm.confirmation.renderCodeInput(email);
            };

            if (withTransition) {
                $registerForm.addClass('animate__animated animate__fadeOut animate__delay-2s');
                setTimeout(transitionHandler, 400);
            } else {
                transitionHandler();
            }
        },
        renderCodeInput: function(email) {
            const $ = window.$;
            const confirmationSection = $('#confirmation-code-section');
            const template = `
                <div id="confirmation-code-section" class="box mt-4 animate__animated animate__fadeIn animate__delay-2s">
                    <label class="label">Enter Confirmation Code</label>
                    <div class="field has-addons">
                        <div class="control">
                            <input id="confirmation-code-input" class="input" type="text" maxlength="6" placeholder="6-digit code">
                        </div>
                        <div class="control">
                            <button id="confirm-code-button" class="button is-link">Confirm</button>
                        </div>
                    </div>
                    <p id="confirmation-code-message" class="help"></p>
                </div>
            `;

            if (confirmationSection.length === 0) {
                $(RegisterForm.elements.registerContainer).after(template);
            } else {
                confirmationSection.show().addClass('animate__animated animate__fadeIn animate__delay-2s');
            }

            $('#confirm-code-button').off('click').on('click', function(e) {
                e.preventDefault();
                RegisterForm.confirmation.submitCode(email);
            });
        },
        submitCode: function(email) {
            const $ = window.$;
            const code = $('#confirmation-code-input').val().trim();
            const $msg = $('#confirmation-code-message');
            const $button = $('#confirm-code-button');

            $msg.text('').removeClass('is-danger is-success');

            if (!code || code.length !== 6) {
                $msg.text('Please enter the 6-digit code sent to your email.').addClass('is-danger');
                return;
            }

            $button.prop('disabled', true).addClass('is-loading');

            $.ajax({
                url: 'user/activate',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ email: email, confirmation_code: code }),
                success: function(response) {
                    if (response.message) {
                        $msg.text(response.message).addClass('is-success');
                        $('#confirmation-code-section').removeClass('animate__fadeIn').addClass('animate__fadeOut');
                        setTimeout(() => window.location.href = '/login', 1200);
                    } else {
                        $msg.text('Activation failed.').addClass('is-danger');
                    }
                    $button.prop('disabled', false).removeClass('is-loading');
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON 
                        ? (xhr.responseJSON.messages?.error || xhr.responseJSON.error || 'Activation failed.')
                        : 'An error occurred. Please try again.';
                    $msg.text(errorMsg).addClass('is-danger');
                    $button.prop('disabled', false).removeClass('is-loading');
                }
            });
        }
    },
    events: {
        registerSubmit: function() {
            const self = RegisterForm;
            const $ = window.$;
            const $registerContainer = $(self.elements.registerContainer);
            const $submitButton = $(self.elements.submitButton);

            const submitHandler = (e) => {
                e.preventDefault();
                self.handlers.registerProcess();
            };

            $registerContainer.on('submit', submitHandler);
            $submitButton.on('click', submitHandler);
        },
        init: function() {
            this.registerSubmit();
        }
    },
    init: function() {
        this.events.init();
    }
};

document.addEventListener('DOMContentLoaded', function() {
    RegisterForm.init();
});
