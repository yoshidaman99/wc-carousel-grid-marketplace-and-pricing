/* eslint-disable */
/**
 * WC CGMP Elementor Editor Debug Script
 *
 * Open browser DevTools Console (F12) and look for [WC-CGMP-DEBUG] messages.
 * This script monitors what's blocking the Elementor panel from finishing loading.
 *
 * To remove: delete the debug script enqueue in Elementor_Integration.php
 */
(function () {
    'use strict';

    var TAG = '[WC-CGMP-DEBUG]';
    var startTime = Date.now();

    function elapsed() {
        return ((Date.now() - startTime) / 1000).toFixed(2) + 's';
    }

    function log() {
        var args = Array.prototype.slice.call(arguments);
        console.log.apply(console, [TAG + ' (' + elapsed() + ')'].concat(args));
    }

    function warn() {
        var args = Array.prototype.slice.call(arguments);
        console.warn.apply(console, [TAG + ' (' + elapsed() + ')'].concat(args));
    }

    function error() {
        var args = Array.prototype.slice.call(arguments);
        console.error.apply(console, [TAG + ' (' + elapsed() + ')'].concat(args));
    }

    log('Debug script loaded. Monitoring Elementor editor...');

    // ========================================
    // 1. MONITOR JS ERRORS
    // ========================================
    var originalOnError = window.onerror;
    window.onerror = function (msg, url, line, col, err) {
        error('JS Error caught:', msg, '\n  File:', url, 'Line:', line, 'Col:', col);
        if (err && err.stack) {
            error('Stack:', err.stack);
        }
        if (originalOnError) {
            return originalOnError.apply(this, arguments);
        }
        return false;
    };

    window.addEventListener('unhandledrejection', function (e) {
        error('Unhandled Promise Rejection:', e.reason);
    });

    // ========================================
    // 2. MONITOR ELEMENTOR PANEL STATE
    // ========================================
    var panelState = 'unknown';
    var panelCheckInterval = setInterval(function () {
        var panel = document.querySelector('#elementor-panel');
        if (!panel) {
            if (panelState !== 'not-found') {
                panelState = 'not-found';
                warn('Panel element #elementor-panel NOT FOUND in DOM');
            }
            return;
        }

        var currentClass = panel.className || '';
        var isLoading = currentClass.indexOf('elementor-panel-state-loading') !== -1;
        var isReady = currentClass.indexOf('elementor-panel-state-ready') !== -1;

        if (isLoading && panelState !== 'loading') {
            panelState = 'loading';
            log('Panel state changed to: LOADING (class: elementor-panel-state-loading)');
        } else if (isReady && panelState !== 'ready') {
            panelState = 'ready';
            log('Panel state changed to: READY');
            clearInterval(panelCheckInterval);
        } else if (!isLoading && !isReady && panelState !== 'other') {
            panelState = 'other';
            warn('Panel has unexpected state. Classes:', currentClass);
        }

        if (isLoading && (Date.now() - startTime) > 15000) {
            warn('Panel STILL loading after 15s. Possible blockages:');
            log('  - Checking pending AJAX requests...');
            log('  - Checking for console errors above');
            log('  - Checking if marketplace.js threw an error on init...');
            dumpPendingAjax();
        }
    }, 500);

    // ========================================
    // 3. MONITOR ALL AJAX REQUESTS
    // ========================================
    var pendingAjax = {};
    var ajaxId = 0;

    var origOpen = XMLHttpRequest.prototype.open;
    var origSend = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function (method, url) {
        this._wcCgmpDebugId = ++ajaxId;
        this._wcCgmpDebugUrl = url;
        this._wcCgmpDebugMethod = method;
        pendingAjax[this._wcCgmpDebugId] = {
            method: method,
            url: url,
            startTime: Date.now()
        };
        return origOpen.apply(this, arguments);
    };

    XMLHttpRequest.prototype.send = function () {
        var self = this;
        var id = this._wcCgmpDebugId;
        var origOnLoad = this.onload;
        var origOnError = this.onerror;
        var origOnTimeout = this.ontimeout;

        this.addEventListener('load', function () {
            delete pendingAjax[id];
            var duration = ((Date.now() - pendingAjax[id + '_start'] || Date.now()) / 1000).toFixed(2);
            if (duration > 3) {
                warn('Slow AJAX completed (' + duration + 's):', self._wcCgmpDebugMethod, self._wcCgmpDebugUrl, 'Status:', self.status);
            }
        });

        this.addEventListener('error', function () {
            delete pendingAjax[id];
            error('AJAX FAILED:', self._wcCgmpDebugMethod, self._wcCgmpDebugUrl, 'Status:', self.status);
        });

        this.addEventListener('timeout', function () {
            delete pendingAjax[id];
            error('AJAX TIMEOUT:', self._wcCgmpDebugMethod, self._wcCgmpDebugUrl);
        });

        pendingAjax[id + '_start'] = Date.now();
        return origSend.apply(this, arguments);
    };

    function dumpPendingAjax() {
        var keys = Object.keys(pendingAjax).filter(function (k) { return !k.endsWith('_start'); });
        if (keys.length === 0) {
            log('  No pending AJAX requests.');
        } else {
            warn('  ' + keys.length + ' pending AJAX request(s):');
            keys.forEach(function (k) {
                var req = pendingAjax[k];
                if (req) {
                    var dur = ((Date.now() - req.startTime) / 1000).toFixed(2);
                    warn('    #' + k + ' [' + dur + 's] ' + req.method + ' ' + req.url);
                }
            });
        }
    }

    // jQuery AJAX monitoring
    if (typeof jQuery !== 'undefined') {
        var origAjax = jQuery.ajax;
        jQuery.ajax = function (options) {
            var url = (typeof options.url === 'string') ? options.url : (options.url && options.url.url) || 'unknown';
            log('jQuery AJAX:', options.type || 'GET', url);

            var origSuccess = options.success;
            var origError = options.error;

            options.success = function () {
                if (origSuccess) origSuccess.apply(this, arguments);
            };
            options.error = function (xhr, status, err) {
                error('jQuery AJAX Error:', options.type || 'GET', url, 'Status:', status, err);
                if (origError) origError.apply(this, arguments);
            };

            return origAjax.apply(this, arguments);
        };
    }

    // ========================================
    // 4. MONITOR SCRIPT/STYLE LOADING
    // ========================================
    log('Checking script/style loading status...');

    var scriptsToCheck = [
        'wc-cgmp-marketplace',
        'wc-cgmp-frontend',
        'elementor-editor',
        'elementor-panel'
    ];

    setTimeout(function () {
        scriptsToCheck.forEach(function (id) {
            var el = document.getElementById(id);
            if (!el) {
                log('  Script #' + id + ': NOT in DOM (may load async or not needed)');
            } else if (el.tagName === 'SCRIPT') {
                log('  Script #' + id + ': FOUND (loaded=' + !!el.dataset.loaded + ')');
            } else {
                log('  Element #' + id + ': FOUND as <' + el.tagName.toLowerCase() + '>');
            }
        });
    }, 2000);

    // ========================================
    // 5. MONITOR ELEMENTOR EVENTS
    // ========================================
    if (typeof elementor !== 'undefined' && elementor.channels) {
        log('Elementor channels detected. Monitoring...');

        var origEditorApply = null;
        try {
            if (elementor.getPanel) || elementor.panel) {
                log('Elementor panel API available');
            }
        } catch (e) {
            // no panel API yet
        }

        // Monitor editor loading events
        var editorLoadingEvents = [
            'editor:init',
            'panel:open',
            'panel:close',
            'panel:loading:start',
            'panel:loading:end'
        ];

        // Use MutationObserver on elementor modules if available
        try {
            if (elementor.modules && elementor.modules.editor) {
                log('Elementor editor modules loaded');
            }
        } catch (e) { }
    } else {
        warn('Elementor channels NOT detected yet. Will recheck...');
        var checkElementorInterval = setInterval(function () {
            if (typeof elementor !== 'undefined') {
                clearInterval(checkElementorInterval);
                log('Elementor object detected on recheck');
                if (elementor.channels) {
                    log('Elementor channels available');
                }
                if (typeof $e !== 'undefined') {
                    log('Elementor $e (internal commands) available');
                }
            }
        }, 1000);
    }

    // ========================================
    // 6. MONITOR WC_CGMP_Marketplace INIT
    // ========================================
    if (typeof WC_CGMP_Marketplace !== 'undefined') {
        log('WC_CGMP_Marketplace object EXISTS');
        log('  initialized:', WC_CGMP_Marketplace.initialized);
        log('  isLoading:', WC_CGMP_Marketplace.isLoading);
    } else {
        log('WC_CGMP_Marketplace object NOT FOUND (expected on frontend, OK in editor)');
    }

    if (typeof wc_cgmp_ajax !== 'undefined') {
        log('wc_cgmp_ajax localized data EXISTS');
        log('  ajax_url:', wc_cgmp_ajax.ajax_url);
        log('  debug:', wc_cgmp_ajax.debug);
    } else {
        log('wc_cgmp_ajax localized data NOT FOUND');
    }

    // ========================================
    // 7. FINAL STATUS REPORT (after 10s)
    // ========================================
    setTimeout(function () {
        log('=== 10-SECOND STATUS REPORT ===');
        log('Panel state:', panelState);
        log('Panel element exists:', !!document.querySelector('#elementor-panel'));

        var panel = document.querySelector('#elementor-panel');
        if (panel) {
            log('Panel classes:', panel.className);
        }

        dumpPendingAjax();

        log('Document readyState:', document.readyState);

        if (panelState === 'loading') {
            error('PANEL STILL LOADING AFTER 10s!');
            warn('POSSIBLE CAUSES:');
            warn('  1. A JS error above prevented Elementor from finishing init');
            warn('  2. An AJAX request is hanging (see pending AJAX above)');
            warn('  3. A widget render() is causing an infinite loop or timeout');
            warn('  4. Another plugin is conflicting with Elementor');
            warn('');
            warn('TO ISOLATE: Try deactivating this plugin temporarily.');
            warn('If panel loads fine without it, the issue is in this plugin.');
            warn('If panel still hangs, the issue is from another plugin or theme.');
        } else if (panelState === 'ready') {
            log('Panel loaded successfully!');
        }
    }, 10000);

    // ========================================
    // 8. EXTRA: 30-SECOND REPORT if still stuck
    // ========================================
    setTimeout(function () {
        if (panelState === 'loading') {
            error('=== 30-SECOND REPORT: PANEL STILL STUCK ===');
            dumpPendingAjax();

            var panel = document.querySelector('#elementor-panel');
            if (panel) {
                error('Panel innerHTML length:', panel.innerHTML.length);
                error('Panel children count:', panel.children.length);
            }

            error('Active event listeners cannot be inspected from JS.');
            error('Recommend: Check Network tab for any FAILED/HANGING requests.');
            error('Recommend: Try disabling ALL other plugins except Elementor + WooCommerce.');
        }
    }, 30000);

})();
