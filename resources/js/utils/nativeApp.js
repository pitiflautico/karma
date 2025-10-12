/**
 * Native App Communication Module
 *
 * This module handles communication between the web app running in a React Native WebView
 * and the native mobile application.
 */

/**
 * Detects if the web app is running inside a React Native WebView
 * @returns {boolean} True if running in React Native WebView, false otherwise
 */
export function isRunningInNativeApp() {
    return typeof window !== 'undefined' && typeof window.ReactNativeWebView !== 'undefined';
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
        const messageString = JSON.stringify(message);
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
 * @returns {boolean} True if notification was sent successfully
 */
export function notifyLoginSuccess(userId, userToken) {
    console.log('[NativeApp] Notifying login success');

    if (!userId || !userToken) {
        console.error('[NativeApp] Missing userId or userToken');
        return false;
    }

    return sendMessageToNativeApp({
        action: 'loginSuccess',
        userId: userId,
        userToken: userToken
    });
}

/**
 * Notifies the native app of a logout
 * @returns {boolean} True if notification was sent successfully
 */
export function notifyLogout() {
    console.log('[NativeApp] Notifying logout');

    return sendMessageToNativeApp({
        action: 'logout'
    });
}

/**
 * Auto-detects authentication state from page data and notifies native app
 * This function looks for authentication data in common places (meta tags, data attributes, etc.)
 */
export function autoDetectAndNotify() {
    if (!isRunningInNativeApp()) {
        return;
    }

    // Check for auth data in meta tags
    const userIdMeta = document.querySelector('meta[name="user-id"]');
    const userTokenMeta = document.querySelector('meta[name="user-token"]');

    if (userIdMeta && userTokenMeta) {
        const userId = userIdMeta.getAttribute('content');
        const userToken = userTokenMeta.getAttribute('content');

        if (userId && userToken) {
            notifyLoginSuccess(userId, userToken);
        }
    }
}

// Make functions available globally for use in inline scripts
if (typeof window !== 'undefined') {
    window.NativeAppBridge = {
        isRunningInNativeApp,
        notifyLoginSuccess,
        notifyLogout,
        autoDetectAndNotify
    };
}
