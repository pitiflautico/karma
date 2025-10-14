<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-8 text-gray-900">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Privacy Policy</h1>

                <div class="text-sm text-gray-600 mb-8">
                    <p>Last updated: {{ now()->format('F d, Y') }}</p>
                </div>

                <div class="prose max-w-none">
                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">1. Introduction</h2>
                    <p class="text-gray-700 mb-4">
                        Welcome to Feelith ("we," "our," or "us"). We respect your privacy and are committed to protecting your personal data. This privacy policy will inform you about how we look after your personal data when you use our application and tell you about your privacy rights.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">2. Information We Collect</h2>
                    <p class="text-gray-700 mb-4">
                        We collect and process the following types of information:
                    </p>

                    <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">2.1 Account Information</h3>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li>Name and email address (from your Google account)</li>
                        <li>Profile picture (from your Google account)</li>
                        <li>Google account ID for authentication</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">2.2 Mood and Emotional Data</h3>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li>Mood entries (ratings, emotions, notes)</li>
                        <li>Timestamps of mood entries</li>
                        <li>Emotional selfies (if you choose to take them)</li>
                        <li>Patterns and insights derived from your entries</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">2.3 Calendar Data</h3>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li>Google Calendar events (if you enable calendar integration)</li>
                        <li>Event names, times, and basic metadata</li>
                        <li>Sync tokens to maintain calendar synchronization</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">2.4 Technical Data</h3>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li>Device information (type, operating system)</li>
                        <li>Push notification tokens (for sending reminders)</li>
                        <li>IP address and browser information</li>
                        <li>Usage data and application interactions</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">3. How We Use Your Information</h2>
                    <p class="text-gray-700 mb-4">
                        We use your information to:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li>Provide and maintain the Feelith service</li>
                        <li>Allow you to track your emotional patterns over time</li>
                        <li>Send you mood check-in reminders (if enabled)</li>
                        <li>Correlate calendar events with your emotional state</li>
                        <li>Generate insights and reports about your emotional wellbeing</li>
                        <li>Improve and optimize our service</li>
                        <li>Communicate with you about the service</li>
                        <li>Ensure security and prevent fraud</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">4. Google OAuth and Calendar Integration</h2>
                    <p class="text-gray-700 mb-4">
                        When you sign in with Google, we request access to your:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li><strong>Basic profile information</strong>: To create and authenticate your account</li>
                        <li><strong>Google Calendar</strong> (optional): To sync calendar events and correlate them with your mood entries</li>
                    </ul>

                    <h3 class="text-xl font-semibold text-gray-900 mt-6 mb-3">Calendar Access (Read-Only)</h3>
                    <p class="text-gray-700 mb-4">
                        When you enable Google Calendar integration, Feelith:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li><strong>ONLY READS</strong> your calendar events (event names, dates, times)</li>
                        <li><strong>NEVER modifies, creates, or deletes</strong> any calendar events</li>
                        <li><strong>NEVER shares</strong> your calendar data with third parties</li>
                        <li>Uses calendar data solely to correlate events with your mood patterns</li>
                        <li>Stores event data locally only for synchronization purposes</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        Calendar integration is completely optional. You can disconnect it at any time from the Settings page, and we will immediately stop accessing your calendar and delete all stored calendar data.
                    </p>

                    <p class="text-gray-700 mb-4">
                        We only access the specific data necessary to provide these features. You can revoke our access to your Google account at any time through your Google Account settings.
                    </p>
                    <p class="text-gray-700 mb-4">
                        <strong>Feelith's use and transfer of information received from Google APIs will adhere to <a href="https://developers.google.com/terms/api-services-user-data-policy" class="text-purple-600 hover:text-purple-700 underline" target="_blank" rel="noopener">Google API Services User Data Policy</a>, including the Limited Use requirements.</strong>
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">5. Data Sharing and Disclosure</h2>
                    <p class="text-gray-700 mb-4">
                        We do not sell your personal information. We may share your information only in the following circumstances:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li><strong>With your consent</strong>: When you choose to share mood data with groups or individuals</li>
                        <li><strong>Service providers</strong>: With trusted third parties who help us provide the service (e.g., hosting, analytics)</li>
                        <li><strong>Legal requirements</strong>: If required by law or to protect rights and safety</li>
                        <li><strong>Business transfers</strong>: In connection with a merger, sale, or acquisition</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">6. Data Security</h2>
                    <p class="text-gray-700 mb-4">
                        We implement appropriate technical and organizational measures to protect your personal data:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li>Encryption in transit (HTTPS/TLS)</li>
                        <li>Secure authentication via Google OAuth</li>
                        <li>Regular security updates and monitoring</li>
                        <li>Access controls and authentication</li>
                        <li>Encrypted storage for sensitive data</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">7. Data Retention</h2>
                    <p class="text-gray-700 mb-4">
                        We retain your personal data for as long as necessary to provide the service and comply with legal obligations:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li>Active account data: Retained while your account is active</li>
                        <li>Mood entries: Retained until you delete them or close your account</li>
                        <li>Calendar sync data: Updated regularly, old tokens removed</li>
                        <li>Deleted accounts: Data permanently deleted within 30 days</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">8. Your Rights</h2>
                    <p class="text-gray-700 mb-4">
                        You have the following rights regarding your personal data:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li><strong>Access</strong>: Request access to your personal data</li>
                        <li><strong>Correction</strong>: Request correction of inaccurate data</li>
                        <li><strong>Deletion</strong>: Request deletion of your personal data</li>
                        <li><strong>Export</strong>: Request a copy of your data in a portable format</li>
                        <li><strong>Restriction</strong>: Request restriction of processing</li>
                        <li><strong>Objection</strong>: Object to processing of your data</li>
                        <li><strong>Withdraw consent</strong>: Withdraw consent at any time</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        To exercise these rights, please contact us using the information below.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">9. Cookies and Tracking</h2>
                    <p class="text-gray-700 mb-4">
                        We use cookies and similar technologies to:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li>Maintain your session and keep you logged in</li>
                        <li>Remember your preferences</li>
                        <li>Understand how you use our service</li>
                        <li>Improve service performance</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        You can control cookies through your browser settings.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">10. Third-Party Services</h2>
                    <p class="text-gray-700 mb-4">
                        Our service integrates with:
                    </p>
                    <ul class="list-disc ml-6 mb-4 text-gray-700">
                        <li><strong>Google OAuth</strong>: For authentication</li>
                        <li><strong>Google Calendar API</strong>: For calendar integration</li>
                        <li><strong>Expo Push Notifications</strong>: For sending reminders (mobile app)</li>
                    </ul>
                    <p class="text-gray-700 mb-4">
                        These services have their own privacy policies, which we encourage you to review.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">11. Children's Privacy</h2>
                    <p class="text-gray-700 mb-4">
                        Feelith is not intended for users under the age of 13. We do not knowingly collect personal information from children under 13. If you believe we have collected information from a child under 13, please contact us immediately.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">12. International Data Transfers</h2>
                    <p class="text-gray-700 mb-4">
                        Your information may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place to protect your data in accordance with this privacy policy.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">13. Changes to This Policy</h2>
                    <p class="text-gray-700 mb-4">
                        We may update this privacy policy from time to time. We will notify you of any significant changes by posting the new policy on this page and updating the "Last updated" date.
                    </p>

                    <h2 class="text-2xl font-semibold text-gray-900 mt-8 mb-4">14. Contact Us</h2>
                    <p class="text-gray-700 mb-4">
                        If you have any questions about this Privacy Policy or our data practices, please contact us:
                    </p>
                    <p class="text-gray-700 mb-4">
                        Email: <a href="mailto:privacy@feelith.com" class="text-purple-600 hover:text-purple-700 underline">privacy@feelith.com</a><br>
                        Support: <a href="mailto:support@feelith.com" class="text-purple-600 hover:text-purple-700 underline">support@feelith.com</a>
                    </p>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600">
                        By using Feelith, you acknowledge that you have read and understood this Privacy Policy.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
