function eraseCookie(name) { 
   //create to erase cookies
    document.cookie = name + '=; Max-Age=-99999999;';
}
function setLocalStorage (name, value) {
    localStorage.setItem(name, value);
}
function getLocalStorage (name) {
    return localStorage.getItem(name);
}
function removeLocalStorage (name) {
    localStorage.removeItem(name);
} 


document.addEventListener('DOMContentLoaded', function() { 

    console.log(environment); 
 
    if (environment == 'production') {
        // Disable right-click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });

        // Disable F12, Ctrl+Shift+I/J/C, Ctrl+U
        document.addEventListener('keydown', function(e) {
            // F12
            if (e.key == 'F12') {
                e.preventDefault();
            }
            // Ctrl+Shift+I/J/C
            if (e.ctrlKey && e.shiftKey && ['I', 'J', 'C'].includes(e.key.toUpperCase())) {
                e.preventDefault();
            }
            // Ctrl+U
            if (e.ctrlKey && e.key.toUpperCase() == 'U') {
                e.preventDefault();
            }
        });
    }
});