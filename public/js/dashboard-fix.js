/**
 * AdminLTE Dashboard Error Fix
 * This script prevents common errors with AdminLTE dashboard components.
 */
(function() {
    'use strict';

    // Run before document ready to catch errors early
    function preventDashboardErrors() {
        console.log('Dashboard fix script initialized');

        // Create a mock Sparkline object if it doesn't exist
        if (typeof window.Sparkline === 'undefined') {
            window.Sparkline = function() {
                return {
                    init: function() { return this; },
                    render: function() { return this; }
                };
            };
        }

        // Prevent JQVMap errors by adding a check before initialization
        const originalJQueryFn = $.fn || {};
        if (originalJQueryFn.vectorMap) {
            const originalVectorMap = originalJQueryFn.vectorMap;
            $.fn.vectorMap = function(options) {
                // Check if the element exists and has dimensions
                if (this.length === 0 || !this[0].getBoundingClientRect) {
                    console.warn('VectorMap: Target element not found or invalid');
                    return this;
                }

                const rect = this[0].getBoundingClientRect();

                // Add explicit dimensions if missing
                if (!this.attr('style') || !this.css('width') || this.css('width') === '0px') {
                    this.css({
                        'width': '100%',
                        'height': '250px',
                        'min-height': '250px'
                    });
                }

                try {
                    return originalVectorMap.call(this, options);
                } catch (e) {
                    console.warn('VectorMap initialization suppressed error:', e.message);
                    return this;
                }
            };
        }

        // Check if sparkline plugin exists and create mock if not
        if ($.fn && !$.fn.sparkline) {
            $.fn.sparkline = function(values, options) {
                console.warn('Sparkline plugin not loaded, visualization skipped');
                return this;
            };
        }

        // Prevent errors in AdminLTE dashboard initialization
        window.addEventListener('error', function(e) {
            if (e.message && (
                e.message.includes('Sparkline is not defined') ||
                e.message.includes('vectorMap') ||
                e.message.includes('Expected length'))) {

                console.warn('Suppressed AdminLTE error:', e.message);
                e.stopPropagation();
                e.preventDefault();
                return true;
            }
        }, true);
    }

    // Run fix immediately before other scripts load
    preventDashboardErrors();

    // Also run on document ready to catch any missed issues
    $(function() {
        // Fix for world-map element if it exists
        if ($('#world-map').length > 0) {
            $('#world-map').css({
                'width': '100%',
                'height': '250px',
                'min-height': '250px'
            });

            console.log('Fixed world-map dimensions');
        } else {
            console.log('World map element not found, skipping initialization');
        }

        // Override any remaining jQuery method errors
        ['sparkline', 'knob', 'daterangepicker', 'slimScroll'].forEach(function(plugin) {
            if ($.fn && !$.fn[plugin]) {
                $.fn[plugin] = function() {
                    return this;
                };
            }
        });
    });
})();
