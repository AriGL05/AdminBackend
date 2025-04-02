$(function() {
    'use strict';

    // Fix for vector map errors
    if (typeof $.fn.vectorMap === 'function') {
        // Check if the world-map element exists
        if ($('#world-map').length === 0) {
            console.log('World map element not found, skipping initialization');
            return;
        }

        // Add width and height to prevent undefined attributes
        $('#world-map').css({
            'width': '100%',
            'height': '250px',
            'min-height': '250px'
        });

        try {
            // Try to initialize the map with explicit dimensions
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
                },
                series: {
                    regions: [{
                        values: {},
                        scale: ['#C8EEFF', '#0071A4'],
                        normalizeFunction: 'polynomial'
                    }]
                },
            });
        } catch (e) {
            console.log('Vector map initialization error:', e);
        }
    }

    // Fix for Sparkline not defined error
    if (typeof $.fn.sparkline === 'undefined') {
        $.fn.sparkline = function() {
            console.log('Sparkline plugin not loaded, skipping initialization');
            return this;
        };
    }
});
