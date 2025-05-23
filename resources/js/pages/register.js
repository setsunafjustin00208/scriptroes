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
                    if (response.message === 'User registered') {
                        $error.text('Registration successful! You can now log in.').addClass('is-success');
                        setTimeout(function() {
                            window.location.href = '/login';
                        }, 1000);
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
