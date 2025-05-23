// pages/login

let LoginForm = {

    elements: {

        loginContainer: '#login-form',
        emailInput: '#email-input',
        passwordInput: '#password-input',
        submitButton: '#login-button',
        errorMessage: '#error-message',
        burgerButton: '.burger',
        rememberMeCheckbox: '#remember_me',

    },

    handlers : { 

        loginProccess: function() {
            const self = LoginForm;
            const email = window.$(self.elements.emailInput).val();
            const password = window.$(self.elements.passwordInput).val();
            const $error = window.$(self.elements.errorMessage);
            const $button = window.$(self.elements.submitButton);
            $error.text('').removeClass('is-danger is-success');

            if (!email || !password) {
                $error.text('Please fill in all fields.').addClass('is-danger');
                return;
            }

            $button.prop('disabled', true).addClass('is-loading');

            window.$.ajax({
                url: 'user/login', 
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    username: email, // backend expects 'username', not 'email'
                    password: password
                }),
                success: function(response) {
                    $error.text('').removeClass('is-danger is-success');
                    if (response.message === 'Login successful') {
                        $error.text('Login successful').addClass('is-success');
                        setTimeout(function() {
                            window.location.href = 'home';
                        }, 700);
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
                        $error.text('Login failed. Please try again.').addClass('is-danger');
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
        },

        init : function() { 
            
        }

    },

    binders : {
         init : function() { 
            
        }
    },

    events : {	

        loginSubmit : function() { 
            const self = LoginForm;
            const loginContainer = window.$(self.elements.loginContainer);
            const submitButton = window.$(self.elements.submitButton);

            loginContainer.on('submit', function(e) {
                e.preventDefault();
                self.handlers.loginProccess();
            });
 
            submitButton.on('click', function(e) {
                 e.preventDefault();
                self.handlers.loginProccess();
            });
        },

        loginBurger: function() {

            // jQuery version of the burger menu toggle
            const self = LoginForm;

            var $burger = window.$(self.elements.burgerButton);

            if ($burger.length === 0) return;

            var targetId = $burger.data('target');
            var $menu = window.$('#' + targetId);

            $burger.on('click', function() {
                $burger.toggleClass('is-active');
                $menu.toggleClass('is-active');
            });

        },

        rememeberMe: function() {
            const self = LoginForm;
            const rememberMeCheckbox = window.$(self.elements.rememberMeCheckbox);
            const emailInput = window.$(self.elements.emailInput);
            const password = window.$(self.elements.passwordInput);

            // Add event listener for checkbox change
            rememberMeCheckbox.on('change', function() {
                if (rememberMeCheckbox.is(':checked')) {
                    setLocalStorage('email', emailInput.val());
                    setLocalStorage('password', password.val());
                } else {
                    removeLocalStorage('email');
                    removeLocalStorage('password');
                }
            });
        },
        setInputsFromStorage: function() {
            const self = LoginForm;
            const rememberMeCheckbox = window.$(self.elements.rememberMeCheckbox);
            const emailInput = window.$(self.elements.emailInput);
            const password = window.$(self.elements.passwordInput);
            // Set initial values of email and password inputs from localStorage
            if (getLocalStorage('email') && getLocalStorage('password')) {
                emailInput.val(getLocalStorage('email'));
                password.val(getLocalStorage('password'));
                rememberMeCheckbox.prop('checked', true);
            } else {
                emailInput.val('');
                password.val('');
                rememberMeCheckbox.prop('checked', false);
            }
        },
         init : function() { 
            var self = LoginForm;
            self.events.setInputsFromStorage();
            self.events.loginSubmit(); 
            self.events.loginBurger();
            self.events.rememeberMe();
        }
    },

    init : function() {
        this.binders.init();
        this.handlers.init();
        this.events.init();
    },
}


document.addEventListener('DOMContentLoaded', function() { 
    LoginForm.init(); 
});