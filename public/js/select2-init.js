/**
 * Select2 Auto-Initialization Script
 * 
 * Automatically initializes Select2 on any element with the 'select2' class.
 * Supports dynamic content and fallback CDN loading.
 * 
 * Usage: Just add 'select2' class to any <select> element
 */

(function() {
    'use strict';
    
    window.select2Ready = false;
    
    /**
     * Get Select2 configuration for an element
     */
    function getSelect2Config(element) {
        const $element = $(element);
        const config = {
            placeholder: $element.data('placeholder') || $element.find('option:first').text() || 'Select an option',
            allowClear: true,
            width: '100%'
        };

        // Custom dropdown height
        if ($element.data('dropdown-height')) {
            config.dropdownCssClass = 'custom-dropdown-height';
            // Add CSS rule dynamically
            const height = $element.data('dropdown-height');
            if (!$('#select2-custom-height').length) {
                $('<style id="select2-custom-height">.custom-dropdown-height .select2-results { max-height: ' + height + 'px; }</style>').appendTo('head');
            }
        }

        // Custom dropdown width
        if ($element.data('dropdown-width')) {
            config.width = $element.data('dropdown-width') + 'px';
        }

        // Maximum results to show
        if ($element.data('max-results')) {
            config.maximumSelectionLength = $element.data('max-results');
        }

        return config;
    }

    /**
     * Initialize Select2 on all elements with .select2 class
     */
    function initializeSelect2() {
        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
            $('.select2').not('.select2-hidden-accessible').each(function() {
                $(this).select2(getSelect2Config(this));
            });
            window.select2Ready = true;
            $(document).trigger('select2Ready');
            return true;
        }
        return false;
    }
    
    /**
     * Initialize Select2 on new elements within a context
     * @param {Element} context - The context to search for .select2 elements
     */
    window.initNewSelect2 = function(context) {
        if (window.select2Ready && typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
            $(context).find('.select2').not('.select2-hidden-accessible').each(function() {
                $(this).select2(getSelect2Config(this));
            });
        } else {
            // Wait for Select2 to be ready
            $(document).one('select2Ready', function() {
                $(context).find('.select2').not('.select2-hidden-accessible').each(function() {
                    $(this).select2(getSelect2Config(this));
                });
            });
        }
    };
    
    /**
     * Auto-initialize Select2 on dynamically added elements
     */
    function setupMutationObserver() {
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                // Check if the added node has select2 class or contains elements with select2 class
                                if ($(node).hasClass('select2') || $(node).find('.select2').length > 0) {
                                    setTimeout(() => window.initNewSelect2(node), 100);
                                }
                            }
                        });
                    }
                });
            });
            
            // Start observing
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }
    
    /**
     * Load Select2 from fallback CDN if primary fails
     */
    function loadFallbackSelect2() {
        return $.getScript('https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js')
            .done(function() {
                setTimeout(() => {
                    initializeSelect2();
                }, 100);
            })
            .fail(function() {
                console.error('Failed to load Select2 from fallback CDN');
            });
    }
    
    /**
     * Initialize everything when DOM is ready
     */
    $(document).ready(function() {
        // Setup mutation observer for dynamic content
        setupMutationObserver();
        
        // Wait a bit for page content to load, then initialize
        setTimeout(() => {
            if (!initializeSelect2()) {
                loadFallbackSelect2();
            }
        }, 1000);
    });
    
})();