// Import Floating UI (core or DOM, depending on your use case)
import { computePosition } from '@floating-ui/dom';

// Import Alpine.js and expose globally
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Import jQuery and expose globally
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

// Import SweetAlert2 and expose globally
import Swal from 'sweetalert2';
window.Swal = Swal;

//Cookie saving function
function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

//cookeie reading function
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

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
