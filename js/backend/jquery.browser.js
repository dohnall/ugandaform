/**
 * Keeps old jQuery < 1.9 $.browser property
 * To work with legacy plugins, foxframe.js has to be linked immediately after jquery.js
 */
(function () {
    'use strict';
    var matched,
        browser;
    jQuery.uaMatch = function (ua) {
        var match;
        ua = ua.toLowerCase();
        match = /(chrome)[ \/]([\w.]+)/.exec(ua) ||
            /(webkit)[ \/]([\w.]+)/.exec(ua) ||
            /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(ua) ||
            /(msie) ([\w.]+)/.exec(ua.replace(/trident\/7.[0-9]+(.*); rv:11.0/, 'msie 11.0')) ||
            (ua.indexOf("compatible") < 0 && /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(ua)) ||
            [];
        return {
            browser: match[1] || "",
            version: match[2] || "0"
        };
    };
    matched = jQuery.uaMatch(navigator.userAgent);
    browser = {};
    if (matched.browser) {
        browser[matched.browser] = true;
        browser.version = matched.version;
        browser.versionInt = parseInt(matched.version, 10);
    }
    // Chrome is Webkit, but Webkit is also Safari.
    if (browser.chrome) {
        browser.webkit = true;
    } else if (browser.webkit) {
        browser.safari = true;
    }
    jQuery.browser = browser;
}());
