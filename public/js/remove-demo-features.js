/**
 * This script removes or overrides AdminLTE demo features
 * to prevent demo messages from appearing on pages
 */
(function() {
    'use strict';

    $(document).ready(function() {
        // Remove any demo notifications
        $('.demo-notification').remove();
        $('.demo-message').remove();
        $('.demo-warning').remove();

        // Override the AdminLTE demo initialization functions
        if (window.AdminLTEDemo) {
            window.AdminLTEDemo = {
                initDemo: function() { return false; },
                showDemo: function() { return false; },
                pushDemo: function() { return false; }
            };
        }

        // Remove any demo navbar items
        $('.navbar-nav .nav-item.dropdown .demo-message-items').remove();

        // Override internal AdminLTE demo functions
        if (typeof $.AdminLTE !== 'undefined' && typeof $.AdminLTE.demo !== 'undefined') {
            $.AdminLTE.demo = function() { return false; };
        }

        console.log('Demo features disabled successfully');
    });
})();
