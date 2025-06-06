// layout/users/user-layout

let UserLayout = {

    elements: {
        contentPages: '.content-page',
        scrollProgressBar: '[data-scroll-indicator]',
        scrollStatusIndicator: '.scroll-status-indicator',
        scrollPositionText: '.scroll-position-text',
        scrollPercentageText: '.scroll-percentage-text',
        scrollControls: '.scroll-controls',
        scrollToTopBtn: '.scroll-to-top',
        scrollToBottomBtn: '.scroll-to-bottom',
    },

    handlers: {

        setupAutoHideScrollbar: function() {
            const self = UserLayout;
            const $contentPages = window.$(self.elements.contentPages);
            
            if ($contentPages.length === 0) return;
            
            $contentPages.each(function() {
                const $contentPage = window.$(this);
                let scrollTimeout;
                let wheelTimeout;
                let isScrolling = false;
                
                // Handle scroll events
                $contentPage.on('scroll.userLayout', function() {
                    const $this = window.$(this);
                    
                    // Add scrolling class when scrolling starts
                    if (!isScrolling) {
                        $this.addClass('scrolling');
                        isScrolling = true;
                        
                        // Trigger scroll start event
                        self.events.onScrollStart($this);
                    }
                    
                    // Clear existing timeout
                    clearTimeout(scrollTimeout);
                    
                    // Update scroll position data
                    self.handlers.updateScrollData($this);
                    
                    // Trigger scroll progress event
                    self.events.onScrollProgress($this);
                    
                    // Remove scrolling class after scroll ends
                    scrollTimeout = setTimeout(() => {
                        $this.removeClass('scrolling');
                        isScrolling = false;
                        
                        // Trigger scroll end event
                        self.events.onScrollEnd($this);
                    }, 1500); // Hide after 1.5 seconds of no scrolling
                });
                
                // Handle mouse wheel events for immediate scrollbar visibility
                $contentPage.on('wheel.userLayout', function(e) {
                    const $this = window.$(this);
                    
                    // Show scrollbar immediately on wheel event
                    $this.addClass('scrolling');
                    
                    // Clear existing timeouts
                    clearTimeout(scrollTimeout);
                    clearTimeout(wheelTimeout);
                    
                    // Hide scrollbar after wheel event ends
                    wheelTimeout = setTimeout(() => {
                        if (!isScrolling) {
                            $this.removeClass('scrolling');
                        }
                    }, 2000); // Longer timeout for wheel events
                });
                
                // Handle touch events for mobile scrolling
                $contentPage.on('touchstart.userLayout', function() {
                    const $this = window.$(this);
                    $this.addClass('scrolling');
                });
                
                $contentPage.on('touchend.userLayout', function() {
                    const $this = window.$(this);
                    
                    clearTimeout(scrollTimeout);
                    scrollTimeout = setTimeout(() => {
                        $this.removeClass('scrolling');
                        isScrolling = false;
                    }, 2000); // Longer timeout for touch
                });
                
                // Handle keyboard navigation (arrow keys, page up/down)
                $contentPage.on('keydown.userLayout', function(e) {
                    const scrollKeys = [32, 33, 34, 35, 36, 37, 38, 39, 40]; // Space, Page Up/Down, Home, End, Arrow keys
                    
                    if (scrollKeys.includes(e.keyCode)) {
                        const $this = window.$(this);
                        $this.addClass('scrolling');
                        
                        clearTimeout(scrollTimeout);
                        scrollTimeout = setTimeout(() => {
                            $this.removeClass('scrolling');
                            isScrolling = false;
                        }, 1500);
                    }
                });
            });
        },

        updateScrollData: function($element) {
            const element = $element[0];
            const scrollTop = element.scrollTop;
            const scrollHeight = element.scrollHeight;
            const clientHeight = element.clientHeight;
            const scrollPercentage = (scrollTop / (scrollHeight - clientHeight)) * 100;
            
            // Store scroll data as data attributes
            $element.data('scroll-top', scrollTop);
            $element.data('scroll-percentage', scrollPercentage);
            $element.data('scroll-height', scrollHeight);
            $element.data('client-height', clientHeight);
            
            // Add classes based on scroll position
            $element.toggleClass('at-top', scrollTop === 0);
            $element.toggleClass('at-bottom', scrollTop >= (scrollHeight - clientHeight - 5)); // 5px tolerance
            $element.toggleClass('near-top', scrollTop < 100);
            $element.toggleClass('near-bottom', scrollTop > (scrollHeight - clientHeight - 100));
        },

        updateScrollStatusIndicator: function($element) {
            const self = UserLayout;
            const percentage = Math.round($element.data('scroll-percentage') || 0);
            const $percentageText = window.$(self.elements.scrollPercentageText);
            const $positionText = window.$(self.elements.scrollPositionText);
            
            // Update percentage text
            $percentageText.text(percentage + '%');
            
            // Update position text
            let positionText = 'Middle';
            if (percentage <= 5) {
                positionText = 'Top';
            } else if (percentage >= 95) {
                positionText = 'Bottom';
            } else if (percentage <= 25) {
                positionText = 'Near Top';
            } else if (percentage >= 75) {
                positionText = 'Near Bottom';
            }
            
            $positionText.text(positionText);
        },

        setupKeyboardShortcuts: function() {
            const self = UserLayout;
            const $contentPages = window.$(self.elements.contentPages);
            
            $contentPages.on('keydown.userLayout', function(e) {
                const $this = window.$(this);
                
                // Home key - scroll to top
                if (e.keyCode === 36) {
                    e.preventDefault();
                    self.handlers.smoothScrollTo($this, 0);
                }
                // End key - scroll to bottom
                else if (e.keyCode === 35) {
                    e.preventDefault();
                    const scrollHeight = this.scrollHeight;
                    const clientHeight = this.clientHeight;
                    self.handlers.smoothScrollTo($this, scrollHeight - clientHeight);
                }
            });
        },

        setupDoubleClickToTop: function() {
            const self = UserLayout;
            const $contentPages = window.$(self.elements.contentPages);
            
            $contentPages.on('dblclick.userLayout', function(e) {
                // Only if double-clicking on empty space (not on interactive elements)
                if (e.target === this) {
                    self.handlers.smoothScrollTo(window.$(this), 0);
                }
            });
        },

        smoothScrollTo: function($element, position, duration = 500) {
            // Show scrollbar during smooth scroll
            $element.addClass('scrolling');
            
            $element.animate({
                scrollTop: position
            }, {
                duration: duration,
                complete: function() {
                    // Hide scrollbar after smooth scroll completes
                    setTimeout(() => {
                        $element.removeClass('scrolling');
                    }, 1000);
                }
            });
        },

        handleContentResize: function() {
            const self = UserLayout;
            const $contentPages = window.$(self.elements.contentPages);
            
            // Handle window resize to adjust content page heights
            window.$(window).on('resize.userLayout', function() {
                $contentPages.each(function() {
                    const $this = window.$(this);
                    // Recalculate scroll data on resize
                    self.handlers.updateScrollData($this);
                    
                    // Trigger a scroll event to update scrollbar visibility
                    $this.trigger('scroll');
                });
            });
        },

        init: function() {
            const self = UserLayout;
            self.handlers.setupAutoHideScrollbar();
            self.handlers.handleContentResize();
            self.handlers.setupKeyboardShortcuts();
            self.handlers.setupDoubleClickToTop();
        }

    },

    binders: {
        
        bindScrollControls: function() {
            const self = UserLayout;
            
            // Bind any scroll control buttons with data attributes
            window.$('[data-scroll-to-top]').on('click.userLayout', function(e) {
                e.preventDefault();
                const target = window.$(this).data('scroll-target') || self.elements.contentPages;
                const $target = window.$(target).first();
                
                if ($target.length) {
                    self.handlers.smoothScrollTo($target, 0);
                }
            });
            
            window.$('[data-scroll-to-bottom]').on('click.userLayout', function(e) {
                e.preventDefault();
                const target = window.$(this).data('scroll-target') || self.elements.contentPages;
                const $target = window.$(target).first();
                
                if ($target.length) {
                    const scrollHeight = $target[0].scrollHeight;
                    const clientHeight = $target[0].clientHeight;
                    self.handlers.smoothScrollTo($target, scrollHeight - clientHeight);
                }
            });
        },

        init: function() {
            const self = UserLayout;
            self.binders.bindScrollControls();
        }
    },

    events: {

        onScrollStart: function($element) {
            // Custom event when scrolling starts
            $element.trigger('userLayout:scrollStart', {
                element: $element,
                scrollTop: $element.data('scroll-top') || 0
            });
        },

        onScrollProgress: function($element) {
            const self = UserLayout;
            const percentage = $element.data('scroll-percentage') || 0;
            
            // Custom event during scrolling
            $element.trigger('userLayout:scrollProgress', {
                element: $element,
                percentage: percentage,
                scrollTop: $element.data('scroll-top') || 0
            });
            
            // Update scroll indicator if it exists
            const $indicator = window.$(self.elements.scrollProgressBar);
            if ($indicator.length) {
                $indicator.css('width', percentage + '%');
            }

            // Update scroll status indicator
            // self.handlers.updateScrollStatusIndicator($element);
        },

        onScrollEnd: function($element) {
            // Custom event when scrolling ends
            $element.trigger('userLayout:scrollEnd', {
                element: $element,
                finalPosition: $element.data('scroll-top') || 0,
                percentage: $element.data('scroll-percentage') || 0
            });
        },

        scrollbarEvents: function() {
            const self = UserLayout;
            self.handlers.setupAutoHideScrollbar();
        },

        resizeEvents: function() {
            const self = UserLayout;
            self.handlers.handleContentResize();
        },

        customScrollEvents: function() {
            const self = UserLayout;
            
            // Listen for custom scroll events
            window.$(document).on('userLayout:scrollStart', self.elements.contentPages, function(e, data) {
                // Scroll started
            });
            
            window.$(document).on('userLayout:scrollProgress', self.elements.contentPages, function(e, data) {
                // Scroll in progress
            });
            
            window.$(document).on('userLayout:scrollEnd', self.elements.contentPages, function(e, data) {
                // Scroll ended
            });
        },

        init: function() {
            const self = UserLayout;
            self.events.scrollbarEvents();
            self.events.resizeEvents();
            self.events.customScrollEvents();
        }
    },

    // Utility methods
    utils: {
        
        isScrollable: function($element) {
            return $element[0].scrollHeight > $element[0].clientHeight;
        },

        getScrollPercentage: function($element) {
            const element = $element[0];
            return (element.scrollTop / (element.scrollHeight - element.clientHeight)) * 100;
        },

        getScrollPosition: function($element) {
            return {
                top: $element[0].scrollTop,
                percentage: this.getScrollPercentage($element),
                isAtTop: $element[0].scrollTop === 0,
                isAtBottom: $element[0].scrollTop >= ($element[0].scrollHeight - $element[0].clientHeight - 5)
            };
        },

        scrollToElement: function($container, $target, offset = 0) {
            if (!$container.length || !$target.length) return;
            
            const containerTop = $container.offset().top;
            const targetTop = $target.offset().top;
            const scrollTop = $container.scrollTop();
            const targetPosition = scrollTop + (targetTop - containerTop) - offset;
            
            this.smoothScrollTo($container, targetPosition);
        }
    },

    init: function() {
        this.binders.init();
        this.handlers.init();
        this.events.init();
    },
}

document.addEventListener('DOMContentLoaded', function() { 
    UserLayout.init(); 
});