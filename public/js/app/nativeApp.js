/**
 * Native App Communication Module
 *
 * This module handles communication between the web app running in a React Native WebView
 * and the native mobile application.
 *
 * This is a standalone version that doesn't require a build process.
 */

(function() {
    'use strict';

    /**
     * Detects if the web app is running inside a React Native WebView
     * @returns {boolean} True if running in React Native WebView, false otherwise
     */
    function isRunningInNativeApp() {
        return typeof window !== 'undefined' && (
            typeof window.ReactNativeWebView !== 'undefined' ||
            window.__RUNNING_IN_NATIVE_APP__ === true
        );
    }

    /**
     * Sends a message to the React Native app
     * @param {Object} message - The message object to send
     * @returns {boolean} True if message was sent successfully, false otherwise
     */
    function sendMessageToNativeApp(message) {
        if (!isRunningInNativeApp()) {
            console.log('[NativeApp] Not running in WebView, skipping message:', message);
            return false;
        }

        try {
            var messageString = JSON.stringify(message);
            window.ReactNativeWebView.postMessage(messageString);
            console.log('[NativeApp] Message sent successfully:', message);
            return true;
        } catch (error) {
            console.error('[NativeApp] Error sending message to native app:', error);
            return false;
        }
    }

    /**
     * Notifies the native app of a successful login
     * @param {string} userId - The user's ID
     * @param {string} userToken - The authentication token
     * @param {string} pushTokenEndpoint - (Optional) The endpoint URL for registering push tokens
     * @returns {boolean} True if notification was sent successfully
     */
    function notifyLoginSuccess(userId, userToken, pushTokenEndpoint) {
        console.log('[NativeApp] Notifying login success');

        if (!userId || !userToken) {
            console.error('[NativeApp] Missing userId or userToken');
            return false;
        }

        var message = {
            action: 'loginSuccess',
            userId: String(userId),
            userToken: String(userToken)
        };

        // Add pushTokenEndpoint if provided
        if (pushTokenEndpoint) {
            message.pushTokenEndpoint = String(pushTokenEndpoint);
        }

        return sendMessageToNativeApp(message);
    }

    /**
     * Notifies the native app of a logout
     * @returns {boolean} True if notification was sent successfully
     */
    function notifyLogout() {
        console.log('[NativeApp] Notifying logout');

        return sendMessageToNativeApp({
            action: 'logout'
        });
    }

    /**
     * Auto-detects authentication state from page data and notifies native app
     * This function looks for authentication data in common places (meta tags, data attributes, etc.)
     */
    function autoDetectAndNotify() {
        if (!isRunningInNativeApp()) {
            return;
        }

        // Check for auth data in meta tags
        var userIdMeta = document.querySelector('meta[name="user-id"]');
        var userTokenMeta = document.querySelector('meta[name="user-token"]');

        if (userIdMeta && userTokenMeta) {
            var userId = userIdMeta.getAttribute('content');
            var userToken = userTokenMeta.getAttribute('content');

            if (userId && userToken) {
                notifyLoginSuccess(userId, userToken);
            }
        }
    }

    // Expose functions globally
    window.NativeAppBridge = {
        isRunningInNativeApp: isRunningInNativeApp,
        notifyLoginSuccess: notifyLoginSuccess,
        notifyLogout: notifyLogout,
        autoDetectAndNotify: autoDetectAndNotify
    };

    // Log when script loads
    if (isRunningInNativeApp()) {
        console.log('[NativeApp] Bridge initialized - Running in React Native WebView');
    } else {
        console.log('[NativeApp] Bridge initialized - Running in regular browser');
    }

})();
