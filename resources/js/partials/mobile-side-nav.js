// partials/mobile-side-nav

let mobileSideNav = {
    isOpen: false,

    events: {
        toggleSidebar: function() { 
            const self = mobileSideNav;

            if (typeof Alpine !== 'undefined' && Alpine.store('mobileSideNav')) { 
                // Use Alpine store if available
                const store = Alpine.store('mobileSideNav');
                store.toggle();
                self.isOpen = store.isOpen;
                
            } else {
                // Fallback to vanilla JS
                self.isOpen = !self.isOpen;
                self.updateUI();
            }
        },

        closeSidebar: function() {
            const self = mobileSideNav;
            
            if (typeof Alpine !== 'undefined' && Alpine.store('mobileSideNav')) {
                Alpine.store('mobileSideNav').close();
            } else {
                self.isOpen = false;
                self.updateUI();
            }
        },

        openSidebar: function() {
            const self = mobileSideNav;
            
            if (typeof Alpine !== 'undefined' && Alpine.store('mobileSideNav')) {
                Alpine.store('mobileSideNav').open();
            } else {
                self.isOpen = true;
                self.updateUI();
            }
        },

        bindEvents: function() {
            const self = mobileSideNav;
            
            // Bind burger button click
            const burgerBtn = document.getElementById('mobile-burger-sidebar');
            if (burgerBtn) {
                burgerBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    self.events.toggleSidebar();
                });
            }

            // Close on outside click
            document.addEventListener('click', function(e) {
                const sidebar = document.querySelector('[data-target="sidebar_mobile"]');
                const burgerBtn = document.getElementById('mobile-burger-sidebar');
                
                if (sidebar && burgerBtn && self.isOpen) {
                    if (!sidebar.contains(e.target) && !burgerBtn.contains(e.target)) {
                        self.events.closeSidebar();
                    }
                }
            });

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && self.isOpen) {
                    self.events.closeSidebar();
                }
            });
        },

        init: function() {
            const self = mobileSideNav;
            
            // Wait for Alpine to be ready before initializing
            if (typeof Alpine !== 'undefined') {
                // If Alpine is already loaded, bind events immediately
                self.events.bindEvents();
            } else {
                // Wait for Alpine to load
                document.addEventListener('alpine:init', function() {
                    self.events.bindEvents();
                });
            }
        }
    },

    // Update UI for vanilla JS fallback
    updateUI: function() {
        const self = this;
        const burgerBtn = document.getElementById('mobile-burger-sidebar');
        const sidebar = document.querySelector('[data-target="sidebar_mobile"]');
        
        if (burgerBtn) {
            if (self.isOpen) {
                burgerBtn.classList.add('is-active');
            } else {
                burgerBtn.classList.remove('is-active');
            }
        }
        
        if (sidebar) {
            if (self.isOpen) {
                sidebar.classList.add('is-active');
            } else {
                sidebar.classList.remove('is-active');
            }
        }
    },
    
    init: function() {
        const self = mobileSideNav;
        self.events.init();
    }
};

// Initialize Alpine store when Alpine is ready (this runs first)
document.addEventListener('alpine:init', () => {
    Alpine.store('mobileSideNav', {
        isOpen: false,
        toggle() {
            this.isOpen = !this.isOpen;
            // Sync with vanilla JS object
            mobileSideNav.isOpen = this.isOpen;
        },
        close() {
            this.isOpen = false;
            mobileSideNav.isOpen = false;
        },
        open() {
            this.isOpen = true;
            mobileSideNav.isOpen = true;
        }
    });
});

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    mobileSideNav.init();
});
