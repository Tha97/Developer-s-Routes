<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Privacy Policy';

$LEGAL_NAME = legal_value('LEGAL_NAME', 'your full name or registered business name');
$LEGAL_ADDR = legal_value('LEGAL_ADDRESS', 'your physical address');
$EMAIL      = legal_value('CONTACT_EMAIL', 'your contact email address');
$PHONE      = legal_value('CONTACT_PHONE', 'your contact phone number');
$LAST_UPDATED = '19 October 2026';

include __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <article class="legal">
            <?= legal_tabs('privacy') ?>
            <h1>Privacy Policy</h1>
            <p class="legal-meta">Last updated: <?= e($LAST_UPDATED) ?></p>

            <p>This Privacy Policy explains how <?= $LEGAL_NAME ?>, trading as BeautySpot (<strong>"BeautySpot"</strong>, <strong>"we"</strong>, <strong>"us"</strong>), collects, uses, shares and protects your personal information when you use the BeautySpot website (the <strong>"Site"</strong>). We respect your privacy and handle personal information in line with the Protection of Personal Information Act 4 of 2013 (<strong>"POPIA"</strong>). Under POPIA, we are the <strong>"responsible party"</strong> for the information described here.</p>
            <p>This Policy should be read together with our <a href="<?= SITE_URL ?>/terms.php">Terms and Conditions</a>.</p>

            <div class="legal-summary">
                <h2>The short version</h2>
                <ul>
                    <li>We collect only what we need to run the directory: your name, email, phone number and password, plus the profile content, photos and reviews you choose to add.</li>
                    <li><strong>A professional's profile, photos, registered phone number and email are public.</strong> Everything else (your password, favourites) is private.</li>
                    <li>We <strong>do not sell</strong> your information and we do not send marketing emails or show advertising. We only email you about your account and your booking requests.</li>
                    <li>You can ask us to see, correct or delete your information at any time.</li>
                </ul>
                <p class="legal-note">This summary is only a guide. The full Policy below is what applies.</p>
            </div>

            <nav class="legal-toc" aria-label="Contents">
                <h2>Contents</h2>
                <ol>
                    <li><a href="#p1">Who we are and how to reach us</a></li>
                    <li><a href="#p2">Information we collect</a></li>
                    <li><a href="#p3">How we collect it</a></li>
                    <li><a href="#p4">Why we use it</a></li>
                    <li><a href="#p5">What is public and what is private</a></li>
                    <li><a href="#p6">Is giving us information compulsory?</a></li>
                    <li><a href="#p7">Who we share it with</a></li>
                    <li><a href="#p8">Transfers outside South Africa</a></li>
                    <li><a href="#p9">Cookies</a></li>
                    <li><a href="#p10">How long we keep it</a></li>
                    <li><a href="#p11">How we protect it</a></li>
                    <li><a href="#p12">Your rights</a></li>
                    <li><a href="#p13">Children</a></li>
                    <li><a href="#p14">Other people's information</a></li>
                    <li><a href="#p15">Links to other sites</a></li>
                    <li><a href="#p16">Changes to this Policy</a></li>
                    <li><a href="#p17">Contact us</a></li>
                </ol>
            </nav>

            <h2 id="p1">1. Who we are and how to reach us</h2>
            <p>The Site is a directory that helps clients in South Africa find hair, make-up and nail professionals. It is operated by <?= $LEGAL_NAME ?>, trading as BeautySpot. We are the responsible party under POPIA and also act as our own Information Officer. For any privacy question or request, contact us at <?= $EMAIL ?> (full contact details are in section 17).</p>

            <h2 id="p2">2. Information we collect</h2>
            <p><strong>From everyone with an account:</strong></p>
            <ul>
                <li>your name, email address and phone number (the phone number is optional for clients);</li>
                <li>your password, which we store only in scrambled (hashed) form and can never read;</li>
                <li>whether you registered as a client or a professional, the date you registered, and the date you accepted our Terms and Conditions.</li>
            </ul>
            <p><strong>From professionals, in addition:</strong></p>
            <ul>
                <li>your business name, tagline, description, categories, specialties and price range;</li>
                <li>your address, suburb, city and the map location (latitude and longitude) you choose;</li>
                <li>your profile photo, cover photo, and portfolio photos with any captions. These photos may show other people, such as your clients.</li>
            </ul>
            <p><strong>From clients, in addition:</strong></p>
            <ul>
                <li>the professionals you save as favourites;</li>
                <li>reviews you write (a star rating and an optional comment).</li>
            </ul>
            <p><strong>Other information:</strong></p>
            <ul>
                <li>booking requests you send through the Site: the date and time you ask for, your note and the service you choose, together with your name, phone number and email address so the professional can reply, and the professional's answer (confirmed or declined);</li>
                <li>a temporary code, linked to your email address, if you use "Forgot password";</li>
                <li>the places or addresses you type into the Site's search and location boxes;</li>
                <li>technical information such as your IP address, browser type, the pages you request and the date and time, which our web hosting provider records in server logs;</li>
                <li>anything you choose to send us when you contact us.</li>
            </ul>
            <p>We do not ask for your ID number, financial or banking details, health information or your device's GPS location.</p>

            <h2 id="p3">3. How we collect it</h2>
            <ul>
                <li><strong>Directly from you</strong> when you register, fill in or update your profile, upload photos, write a review, save a favourite or contact us.</li>
                <li><strong>Automatically</strong> through the technical records described above when you use the Site.</li>
                <li><strong>From other users.</strong> For example, a client's review about a professional, or a professional's photo that shows a client.</li>
            </ul>

            <h2 id="p4">4. Why we use it</h2>
            <p>We use personal information to:</p>
            <ul>
                <li>create and manage your account and sign you in;</li>
                <li>show professionals' profiles in the directory and let clients and professionals contact each other;</li>
                <li>show reviews and let clients save favourites;</li>
                <li>pass your booking requests to the professional you chose and tell you their answer;</li>
                <li>send you emails that are needed to run your account, such as password resets and updates about booking requests;</li>
                <li>keep the Site safe, prevent fraud and abuse, and enforce our Terms and Conditions;</li>
                <li>meet our legal obligations and handle complaints and requests; and</li>
                <li>keep a record that you accepted our Terms and Conditions.</li>
            </ul>
            <p>We process your information because it is necessary to provide the Site you signed up for, because we have a legitimate interest in running a safe and working directory, because the law requires it, or because you have given your consent (for example, by choosing to publish your profile). We do not use your information for direct marketing and we do not make automated decisions about you.</p>

            <h2 id="p5">5. What is public and what is private</h2>
            <p><strong>Public</strong> (visible to anyone who visits the Site, without logging in):</p>
            <ul>
                <li>a professional's profile, including business name, tagline, description, categories, specialties, price range, address and map pin, photos, the <strong>phone number and email address they registered with</strong>, and reviews about them;</li>
                <li>the name of a client next to a review they wrote, together with the rating and comment.</li>
            </ul>
            <p><strong>Private:</strong> your password (not even we can read it), the professionals you have saved as favourites (a professional only sees how many people saved them), and your own account details other than as described above. A booking request is visible only to you and to the professional you send it to.</p>
            <p>Please remember that public information can be copied, saved or indexed by search engines and other websites. Once that happens, we cannot always remove every copy.</p>

            <h2 id="p6">6. Is giving us information compulsory?</h2>
            <p>Your name, email address and password are needed to create an account. Professionals must also give a phone number, because clients use it to contact them. To send a booking request, you must be logged in as a client and give a phone number. Everything else is optional, although a more complete profile helps clients find and trust you. If you do not give us the information we need, we will not be able to create your account or profile.</p>

            <h2 id="p7">7. Who we share it with</h2>
            <p>We do not sell your personal information. We share it only as follows:</p>
            <ul>
                <li><strong>The professional you send a booking request to.</strong> They receive the request, your name, phone number and email address, in their dashboard and by email. You receive their answer in your dashboard and by email.</li>
                <li><strong>Our web hosting provider</strong>, which stores the Site's database and files and sends the Site's emails.</li>
                <li><strong>OpenStreetMap.</strong> Map tiles are loaded from OpenStreetMap, which receives your IP address and the area of the map you view. When you search for or set an address, the text you type is sent through our server to OpenStreetMap's Nominatim search service.</li>
                <li><strong>Google Fonts and a content delivery network</strong> (Cloudflare's cdnjs) that supply the fonts and the map software the Site uses. They receive your IP address when your browser loads them.</li>
                <li><strong>WhatsApp, your phone or your email app</strong>, if you tap a contact button on a professional's profile. You choose to do this, and their own privacy policies then apply.</li>
                <li><strong>The authorities or other parties</strong> where the law requires us to disclose information, or where it is needed to protect our rights or the safety of others.</li>
                <li><strong>A successor</strong>, if the Site is transferred to a person or company that takes over running it, including a company we set up to operate the Site. They would be bound to treat your information in line with this Policy.</li>
            </ul>
            <p>These providers process information only as needed to provide their service to us or to you.</p>

            <h2 id="p8">8. Transfers outside South Africa</h2>
            <p>Some of the services above, such as Google Fonts, Cloudflare and OpenStreetMap, run servers outside South Africa, and our hosting provider may as well. This means some information, such as your IP address, may be processed in other countries. We take reasonable steps to use providers that protect information, but those countries' laws may differ from South Africa's. By using the Site you acknowledge this.</p>

            <h2 id="p9">9. Cookies</h2>
            <p>We use only the essential cookie that keeps you signed in and the security token that protects our forms from forgery. Without them the Site would not work properly. We do not use advertising, tracking or analytics cookies. You can block cookies in your browser, but you will then be unable to sign in.</p>

            <h2 id="p10">10. How long we keep it</h2>
            <ul>
                <li><strong>Account and profile information:</strong> for as long as your account is active.</li>
                <li><strong>After you ask us to delete your account:</strong> we delete or anonymise your information within about 30 days, except what we must or reasonably need to keep, such as a record that you accepted our Terms, security records, and information that remains in backups until they are overwritten.</li>
                <li><strong>Photos:</strong> removed from the Site when you delete them from your portfolio or profile.</li>
                <li><strong>Booking requests:</strong> for as long as your account (and the professional's account) is active.</li>
                <li><strong>Password reset codes:</strong> expire after one hour and are removed once used.</li>
                <li><strong>Server logs:</strong> kept by our hosting provider for as long as their policies allow.</li>
            </ul>

            <h2 id="p11">11. How we protect it</h2>
            <p>We take reasonable technical and organisational steps to protect personal information against loss, misuse and unauthorised access. These include storing passwords only in hashed form, protecting forms against forgery, limiting who can change a profile to the person who owns it, and using an encrypted (HTTPS) connection. No system is completely secure, and we cannot guarantee absolute security. If there is a security compromise that affects your personal information, we will notify the Information Regulator and the people affected as POPIA requires.</p>

            <h2 id="p12">12. Your rights</h2>
            <p>Under POPIA you have the right to:</p>
            <ul>
                <li>ask us to confirm whether we hold personal information about you, and to see it, including who has had access to it;</li>
                <li>ask us to correct information that is inaccurate, out of date or misleading;</li>
                <li>ask us to delete or destroy information we no longer need or are no longer allowed to keep;</li>
                <li>object to us processing your information, or withdraw consent you gave (this does not affect what we did before you withdrew it, and we may still need to keep some information, as explained in section 10); and</li>
                <li>complain to the Information Regulator, and take legal action if your rights are infringed.</li>
            </ul>
            <p>To use any of these rights, email us at <?= $EMAIL ?>. We may need to confirm your identity first, and we will respond within a reasonable time, normally within 30 days. You can also edit most of your information yourself from your dashboard.</p>
            <p><strong>Information Regulator (South Africa):</strong> website <a href="https://inforegulator.org.za" target="_blank" rel="noopener">inforegulator.org.za</a> (complaints and forms are on the site), email enquiries@inforegulator.org.za, telephone 010 023 5200 or toll-free 0800 017 160. Address: Woodmead North Office Park, 54 Maxwell Drive, Woodmead, Johannesburg, 2191. We would appreciate the chance to put things right first, but you are free to complain to the Regulator at any time.</p>

            <h2 id="p13">13. Children</h2>
            <p>The Site is not intended for anyone under 18, and we do not knowingly collect personal information from children. Professionals must not upload photos of children without the consent of a parent or guardian. If you believe a child's information is on the Site, contact us and we will remove it.</p>

            <h2 id="p14">14. Other people's information</h2>
            <p>Do not post personal information about other people, including photos of them, unless you have their consent. If you are in a photo or mentioned in content on the Site and you want it removed, contact us at <?= $EMAIL ?> and we will review it promptly.</p>

            <h2 id="p15">15. Links to other sites</h2>
            <p>The Site may link to other websites and services. We are not responsible for their privacy practices, so please read their policies.</p>

            <h2 id="p16">16. Changes to this Policy</h2>
            <p>We may update this Policy from time to time, for example if we add new features such as payments or analytics. The new version will be posted here with a new "last updated" date. If a change is significant, we will try to tell you, for example with a notice on the Site.</p>

            <h2 id="p17">17. Contact us</h2>
            <address class="legal-contact">
                <strong><?= $LEGAL_NAME ?></strong>, trading as BeautySpot<br>
                Physical address: <?= $LEGAL_ADDR ?><br>
                Email: <?= $EMAIL ?><br>
                Phone: <?= $PHONE ?><br>
                Information Officer: <?= $LEGAL_NAME ?>
            </address>
        </article>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
