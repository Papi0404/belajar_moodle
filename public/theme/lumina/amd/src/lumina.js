/**
 * Lumina LMS — Main JS module
 * Handles: rail toggle + user pref save, ⌘K search shortcut, mobile overlay
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    'use strict';

    var SIDEBAR_COLLAPSED_KEY = 'lumina-rail-collapsed';
    var SIDEBAR_PREF_NAME     = 'lumina-rail-collapsed';

    function saveRailPref(collapsed) {
        Ajax.call([{
            methodname: 'core_user_update_user_preferences',
            args: {
                preferences: [{
                    type: SIDEBAR_PREF_NAME,
                    value: collapsed ? '1' : '0'
                }]
            }
        }]);
    }

    function initRailToggle() {
        var $body   = $('body');
        var $btn    = $('#luminaRailToggle');
        var $mbtn   = $('#luminaMobileToggle');
        var $sidebar = $('#luminaSidebar');
        var $overlay = $('#luminaMobileOverlay');

        // Desktop rail toggle
        $btn.on('click', function() {
            var collapsed = $body.toggleClass('lumina-rail-collapsed').hasClass('lumina-rail-collapsed');
            saveRailPref(collapsed);
        });

        // Mobile open
        $mbtn.on('click', function() {
            $sidebar.addClass('mobile-open');
            $overlay.addClass('show');
        });

        // Overlay / close mobile
        $overlay.on('click', function() {
            $sidebar.removeClass('mobile-open');
            $overlay.removeClass('show');
        });
    }

    function initSearchShortcut() {
        $(document).on('keydown', function(e) {
            // ⌘K (Mac) or Ctrl+K (Win/Linux)
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                var $input = $('.lumina-search-input');
                if ($input.length) {
                    $input.focus().select();
                }
            }
        });
    }

    function initToastStyle() {
        // Re-apply Lumina colours to Moodle toast alerts
        $(document).on('DOMNodeInserted', '#toast-wrapper', function() {
            $(this).find('.alert').addClass('lumina-toast');
        });
    }

    return {
        init: function() {
            initRailToggle();
            initSearchShortcut();
            initToastStyle();
        }
    };
});

