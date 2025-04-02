$(function() {
    'use strict';

    // Fix for missing Sparkline
    if (typeof $.fn.sparkline === 'undefined') {
        $.fn.sparkline = function() {
            console.log('Sparkline plugin not loaded');
            return this;
        }
    }

    // Only initialize jQVMap if container exists and library is loaded
    if ($('#world-map').length > 0 && typeof $.fn.vectorMap === 'function') {
        try {
            $('#world-map').vectorMap({
                map: 'usa_en',
                backgroundColor: 'transparent',
                regionStyle: {
                    initial: {
                        fill: 'rgba(255, 255, 255, 0.7)',
                        'fill-opacity': 1,
                        stroke: 'rgba(0,0,0,.2)',
                        'stroke-width': 1,
                        'stroke-opacity': 1
                    }
                }
            });
        } catch (e) {
            console.log('Vector map initialization error:', e);
        }
    }
});
