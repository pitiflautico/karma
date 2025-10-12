/**
 * Main Application JavaScript
 */

// Import native app communication module
import './utils/nativeApp.js';

// Initialize auto-detection on page load
document.addEventListener('DOMContentLoaded', () => {
    if (window.NativeAppBridge && window.NativeAppBridge.isRunningInNativeApp()) {
        console.log('[App] Running in React Native WebView');
        // Auto-detect authentication state if needed
        window.NativeAppBridge.autoDetectAndNotify();
    }
});
