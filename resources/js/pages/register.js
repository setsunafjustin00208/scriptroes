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
        registerProcess: function() {
            const self = RegisterForm;
            const $ = window.$;
            const email = $(self.elements.emailInput).val().trim();
            const password = $(self.elements.passwordInput).val();
            const passwordConfirm = $(self.elements.passwordConfirmInput).val();
            const firstName = $(self.elements.firstNameInput).val().trim();
            const lastName = $(self.elements.lastNameInput).val().trim();
            const phone = $(self.elements.phoneInput).val().trim();
            const address = $(self.elements.addressInput).val().trim();
            const $error = $(self.elements.errorMessage);
            const $button = $(self.elements.submitButton);
            $error.text('').removeClass('is-danger is-success');

            // Required fields check
            if (!email || !password || !passwordConfirm || !firstName || !lastName) {
                $error.text('Please fill in all required fields.').addClass('is-danger');
                return;
            }
            if (password !== passwordConfirm) {
                $error.text('Passwords do not match.').addClass('is-danger');
                return;
            }

            $button.prop('disabled', true).addClass('is-loading');

            // Algorithm for permissions and type (mimic UserCommand.php)
            let type = 'user';
            let permInt = 16; // UserModel::PERM_VIEW
            // Optionally, you could allow user to select type/permissions in the UI
            // For now, default to 'user' and PERM_VIEW

            // Personal info fields
            let personalInfo = {
                first_name: firstName,
                last_name: lastName,
                phone: phone,
                address: address
            };

            $.ajax({
                url: 'user/register',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    username: email,
                    email: email,
                    password: password,
                    type: type,
                    permissions: permInt,
                    personal_info: personalInfo
                }),
                success: function(response) {
                    $error.text('').removeClass('is-danger is-success');
                    if (response.message && response.message.indexOf('User registered') !== -1) {
                        $error.text('Registration successful! Please check your email for the confirmation code.').addClass('is-success');
                        RegisterForm.confirmation.showCodeInput(email, true); // Pass true to trigger transition
                    } else if (response.message) {
                        $error.text(response.message).addClass('is-danger');
                    } else if (response.error) {
                        $error.text(response.error).addClass('is-danger');
                    } else if (response.messages) {
                        let errorText = '';
                        if (typeof response.messages === 'object') {
                            errorText = Object.values(response.messages).join(' ');
                        } else {
                            errorText = response.messages;
                        }
                        $error.text(errorText).addClass('is-danger');
                    } else {
                        $error.text('Registration failed. Please try again.').addClass('is-danger');
                    }
                    $button.prop('disabled', false).removeClass('is-loading');
                },
                error: function(xhr) {
                    let msg = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.messages) {
                        msg = xhr.responseJSON.messages.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    }
                    $error.text(msg).addClass('is-danger');
                    $button.prop('disabled', false).removeClass('is-loading');
                }
            });
        }
    },
    confirmation: {
        showCodeInput: function(email, withTransition) {
            const self = RegisterForm;
            const $ = window.$;
            const $registerForm = $(self.elements.registerContainer);
            if (withTransition) {
                $registerForm.addClass('animate__animated animate__fadeOut animate__delay-2s');
                setTimeout(function() {
                    $registerForm.hide();
                    RegisterForm.confirmation.renderCodeInput(email);
                }, 400);
            } else {
                $registerForm.hide();
                RegisterForm.confirmation.renderCodeInput(email);
            }
        },
        renderCodeInput: function(email) {
            const $ = window.$;
            if ($('#confirmation-code-section').length === 0) {
                $(RegisterForm.elements.registerContainer).after(`
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
                `);
            } else {
                $('#confirmation-code-section').show().addClass('animate__animated animate__fadeIn animate__delay-2s');
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
            $msg.text('').removeClass('is-danger is-success');
            if (!code || code.length !== 6) {
                $msg.text('Please enter the 6-digit code sent to your email.').addClass('is-danger');
                return;
            }
            $('#confirm-code-button').prop('disabled', true).addClass('is-loading');
            $.ajax({
                url: 'user/activate',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ email: email, confirmation_code: code }),
                success: function(response) {
                    if (response.message) {
                        $msg.text(response.message).addClass('is-success');
                        // Fade out code input, then redirect
                        $('#confirmation-code-section').removeClass('animate__fadeIn').addClass('animate__fadeOut');
                        setTimeout(function() {
                            $('#confirmation-code-section').hide();
                            window.location.href = '/login';
                        }, 1200);
                    } else {
                        $msg.text('Activation failed.').addClass('is-danger');
                    }
                    $('#confirm-code-button').prop('disabled', false).removeClass('is-loading');
                },
                error: function(xhr) {
                    let msg = 'Activation failed.';
                    if (xhr.responseJSON && xhr.responseJSON.messages) {
                        msg = xhr.responseJSON.messages.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    }
                    $msg.text(msg).addClass('is-danger');
                    $('#confirm-code-button').prop('disabled', false).removeClass('is-loading');
                }
            });
        }
    },
    events: {
        registerSubmit: function() {
            const self = RegisterForm;
            const $ = window.$;
            const registerContainer = $(self.elements.registerContainer);
            const submitButton = $(self.elements.submitButton);
            registerContainer.on('submit', function(e) {
                e.preventDefault();
                self.handlers.registerProcess();
            });
            submitButton.on('click', function(e) {
                e.preventDefault();
                self.handlers.registerProcess();
            });
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

// pages/register
