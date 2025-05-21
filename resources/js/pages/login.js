// pages/login
let LoginForm = {

    elements: {

        emailInput: '#email-input',
        passwordInput: '#password-input',
        submitButton: '#submit-button',
        errorMessage: '#error-message'
,
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
         init : function() { 
            
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