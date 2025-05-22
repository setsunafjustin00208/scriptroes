// pages/login
let LoginForm = {

    elements: {

        emailInput: '#email-input',
        passwordInput: '#password-input',
        submitButton: '#submit-button',
        errorMessage: '#error-message',
        burgerButton: '.burger',

    },

    handlers : { 

        loginProccess: function() {
            
            const self = LoginForm;
            const email = window.$(self.elements.emailInput).val();
            const password = window.$(self.elements.passwordInput).val();

            if (email === '' || password === '') {
                window.$(self.elements.errorMessage).text('Please fill in all fields.');
                return;
            }

            window.$ajax ({
                url: 'user/login',
                type: 'POST',
                data: {
                    email: email,
                    password: password
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = '/dashboard';
                    } else {
                        window.$(self.elements.errorMessage).text(response.message);
                    }
                },
                error: function() {
                    window.$(self.elements.errorMessage).text('An error occurred. Please try again.');
                }
            });
            window.$(self.elements.errorMessage).text('');
            window.$(self.elements.submitButton).prop('disabled', true);

        },

        init : function() { 
            
        }

    },

    binders : {
         init : function() { 
            
        }
    },

    events : {	

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
            const rememberMeCheckbox = window.$('#remember-me');
            const emailInput = window.$(self.elements.emailInput);
            const password = window.$(self.elements.passwordInput);

            if (rememberMeCheckbox.is(':checked')) {
                setLocalStorage('email', emailInput.val());
                setLocalStorage('password', password.val());
            } else {
                removeLocalStorage('email');
                removeLocalStorage('password');
            }

            //setValues of email and password inputs
            if (getLocalStorage('email') && getLocalStorage('password')) {
                emailInput.val(getLocalStorage('email'));
                password.val(getLocalStorage('password'));
            } else {
                emailInput.val('');
                password.val('');
            }


        },
         init : function() { 
            var self = LoginForm;
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