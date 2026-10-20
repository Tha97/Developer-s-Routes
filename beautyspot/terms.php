<?php
require_once __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'Terms and Conditions';

$LEGAL_NAME = legal_value('LEGAL_NAME', 'your registered business name or full name');
$LEGAL_REG  = (defined('LEGAL_REG_NO') && trim((string) LEGAL_REG_NO) !== '') ? e(LEGAL_REG_NO) : '';   // optional: line is hidden if empty
$LEGAL_ADDR = legal_value('LEGAL_ADDRESS', 'your physical address');
$EMAIL      = legal_value('CONTACT_EMAIL', 'your contact email address');
$PHONE      = legal_value('CONTACT_PHONE', 'your contact phone number');
$LAST_UPDATED = '19 October 2026';

include __DIR__ . '/includes/header.php';
?>
<section class="section">
    <div class="container">
        <article class="legal">
            <?= legal_tabs('terms') ?>
            <h1>Terms and Conditions</h1>
            <p class="legal-meta">Last updated: <?= e($LAST_UPDATED) ?></p>

            <p>These Terms and Conditions (the <strong>"Terms"</strong>) govern your access to and use of the BeautySpot website (the <strong>"Site"</strong>). The Site is operated by <?= $LEGAL_NAME ?>, trading as BeautySpot (<strong>"BeautySpot"</strong>, <strong>"we"</strong>, <strong>"us"</strong>). Our <a href="<?= SITE_URL ?>/privacy.php">Privacy Policy</a> forms part of these Terms. By creating an account or using the Site, you agree to these Terms. If you do not agree, please do not use the Site.</p>

            <div class="legal-summary">
                <h2>The short version</h2>
                <ul>
                    <li>BeautySpot is a <strong>directory</strong>. We help clients find hair, make-up and nail professionals. We are not a salon, an employer or a booking or payment service.</li>
                    <li>Clients can send a <strong>booking request</strong> through the site, but the booking itself is agreed <strong>directly between the client and the professional</strong>. Any price, payment or cancellation is between the two of you. We don't take payments.</li>
                    <li><strong>Professionals:</strong> the phone number and email address you register with, and everything on your profile, are shown publicly so clients can contact you.</li>
                    <li>Only upload photos you own or have permission to use, and only write honest reviews.</li>
                    <li>We may remove content or accounts that break these Terms.</li>
                    <li>How we handle your personal information is explained in our <a href="<?= SITE_URL ?>/privacy.php">Privacy Policy</a>.</li>
                </ul>
                <p class="legal-note">This summary is only a guide. The full Terms below are what apply.</p>
            </div>

            <nav class="legal-toc" aria-label="Contents">
                <h2>Contents</h2>
                <ol>
                    <li><a href="#s1">About BeautySpot</a></li>
                    <li><a href="#s2">Definitions</a></li>
                    <li><a href="#s3">Eligibility and your account</a></li>
                    <li><a href="#s4">Terms for Professionals</a></li>
                    <li><a href="#s5">Terms for Clients</a></li>
                    <li><a href="#s6">Bookings, payments and dealings between users</a></li>
                    <li><a href="#s7">Reviews and ratings</a></li>
                    <li><a href="#s8">Content you upload</a></li>
                    <li><a href="#s9">Acceptable use</a></li>
                    <li><a href="#s10">Our intellectual property</a></li>
                    <li><a href="#s11">Privacy and personal information</a></li>
                    <li><a href="#s12">Third-party services and links</a></li>
                    <li><a href="#s13">Availability and changes to the Site</a></li>
                    <li><a href="#s14">Disclaimers</a></li>
                    <li><a href="#s15">Limitation of liability</a></li>
                    <li><a href="#s16">Indemnity</a></li>
                    <li><a href="#s17">Suspension and termination</a></li>
                    <li><a href="#s18">Reporting content and complaints</a></li>
                    <li><a href="#s19">Changes to these Terms</a></li>
                    <li><a href="#s20">General</a></li>
                    <li><a href="#s21">Governing law and disputes</a></li>
                    <li><a href="#s22">Contact us</a></li>
                </ol>
            </nav>

            <h2 id="s1">1. About BeautySpot</h2>
            <p>BeautySpot is an online directory that lets clients in South Africa discover hairstylists, barbers, make-up artists and nail technicians, view their portfolios, prices and locations, and contact them directly. We provide the platform only. We do not provide beauty services, we do not employ or supervise the professionals listed, and we are not a party to any arrangement between a client and a professional.</p>

            <h2 id="s2">2. Definitions</h2>
            <ul>
                <li><strong>"Client"</strong> means a person who has registered as a client or who browses the Site to find a professional.</li>
                <li><strong>"Professional"</strong> means a person or business that has registered a profile to offer hair, make-up or nail services.</li>
                <li><strong>"Profile"</strong> means a Professional's public page, including their name, tagline, description, categories, specialties, prices, location, contact details and photos.</li>
                <li><strong>"Content"</strong> means anything you submit to the Site, including profile information, photos, captions and reviews.</li>
                <li><strong>"User"</strong> means any Client or Professional.</li>
            </ul>

            <h2 id="s3">3. Eligibility and your account</h2>
            <ol class="legal-sub">
                <li>You must be at least 18 years old to create an account. If you are younger, you may only use the Site with the knowledge and consent of a parent or legal guardian, who is responsible for your use of it.</li>
                <li>The information you give when you register must be true, accurate and current. Professionals must provide a valid phone number.</li>
                <li>Keep your password confidential. You are responsible for everything that happens under your account. Tell us promptly if you think someone else has used it.</li>
                <li>You may not create an account in someone else's name, create accounts to mislead others, or have more than one Professional profile for the same business.</li>
            </ol>

            <h2 id="s4">4. Terms for Professionals</h2>
            <ol class="legal-sub">
                <li><strong>Accurate profile.</strong> Your Profile must be honest and not misleading. Prices shown on the Site are a guide only. You are responsible for keeping them and the rest of your Profile up to date.</li>
                <li><strong>Qualifications and compliance.</strong> You are solely responsible for holding any qualifications, licences, permits or insurance your work requires, for meeting health, hygiene and safety standards, and for complying with all laws that apply to your business. We do not check or verify your qualifications or standards unless we expressly say so.</li>
                <li><strong>Public contact details.</strong> You agree that the phone number and email address you registered with, your business name, your location and your Profile Content will be shown publicly to all visitors, and used to power the "WhatsApp", "Call" and "Email" buttons on your Profile. When a Client sends you a booking request, we show it in your dashboard and email it to the email address you registered with, together with the Client's name, phone number and email address. If you want to change or remove these, contact us using the details in section 22.</li>
                <li><strong>Photos and other people.</strong> Only upload photos that you took or have permission to use. If a photo shows a client or any other identifiable person, you must have their consent to publish it. Do not publish photos of children without the consent of a parent or guardian. Remove any photo promptly if the person shown asks you to.</li>
                <li><strong>Honest dealings.</strong> You must not post fake reviews, pay for or pressure others to post false reviews, or interfere with other Professionals' Profiles or reviews. You must treat clients lawfully and fairly, including in line with the Consumer Protection Act 68 of 2008 where it applies to you.</li>
                <li><strong>Your own terms.</strong> You decide your own prices, availability, deposits and cancellation rules, and you are responsible for explaining them to clients.</li>
            </ol>

            <h2 id="s5">5. Terms for Clients</h2>
            <ol class="legal-sub">
                <li>You are responsible for deciding whether a Professional is right for you. Look at their portfolio, reviews and the way they run their business before you book.</li>
                <li>Tell the Professional about any allergies, skin or medical conditions, or sensitivities before a service, and ask about hygiene and patch tests where relevant. We are not responsible for reactions, injuries or dissatisfaction arising from a service.</li>
                <li>A booking request is only a request. It is not a confirmed booking until the Professional accepts it. Send requests in good faith, and tell the Professional as soon as you can if you can no longer attend.</li>
                <li>When you send a booking request, you agree that we share your name, phone number and email address with that Professional so they can respond. Keep your phone number up to date.</li>
                <li>You may use a Professional's contact details only to enquire about or arrange their services. You may not use them for spam, marketing, harassment, or to pass on to others.</li>
            </ol>

            <h2 id="s6">6. Bookings, payments and dealings between users</h2>
            <ol class="legal-sub">
                <li>The "Book now" button opens a form that lets a Client send a booking request to a Professional. We pass the request on and show the Professional's answer, but we do not make or guarantee any booking. We do not collect deposits or process payments. The "WhatsApp", "Call" and "Email" buttons simply open those apps.</li>
                <li>A Professional may accept or decline any request, and we do not promise that a Professional will be available or will respond.</li>
                <li>Any agreement for services, including price, date, deposit, cancellation and refund, is made directly between the Client and the Professional. We are not a party to it.</li>
                <li>We are not responsible for no-shows, late arrivals, cancellations, the quality, safety or legality of any service, or any dispute between Users. Please resolve disputes with each other directly. Nothing in this section limits any rights you have against a Professional under the law.</li>
                <li>Take sensible precautions when meeting or sharing personal details with someone you have found online.</li>
            </ol>

            <h2 id="s7">7. Reviews and ratings</h2>
            <ol class="legal-sub">
                <li>Reviews must be genuine and based on your own experience. Do not review your own business or a competitor's.</li>
                <li>Reviews must not be defamatory, abusive, discriminatory or misleading, and must not contain other people's personal information, phone numbers or addresses.</li>
                <li>A Client may leave one review per Professional. Posting a new one updates the earlier one.</li>
                <li>Reviews are the opinions of the people who write them and not ours. We do not check that a review reflects a real visit. We may remove or edit reviews that break these Terms, but we are not obliged to monitor them.</li>
            </ol>

            <h2 id="s8">8. Content you upload</h2>
            <ol class="legal-sub">
                <li><strong>You keep ownership.</strong> You own your Content. By uploading it you give BeautySpot a non-exclusive, worldwide, royalty-free licence to host, store, display, reproduce, resize and distribute it on and in connection with the Site. This includes showing your cover photo or portfolio photos in search results, on the home page and in other places on the Site where Profiles are featured, and in reasonable promotion of the Site.</li>
                <li>This licence lasts while your Content is on the Site and for a short period afterwards while it is removed from backups and caches.</li>
                <li>You promise that you have all the rights and permissions needed to upload your Content and to give us this licence, and that your Content does not break the law or anyone else's rights.</li>
                <li>You can delete your portfolio photos at any time from your dashboard. We may remove any Content at our discretion, including Content that breaks these Terms.</li>
                <li>Keep your own copies of your photos. We do not promise to back up your Content.</li>
            </ol>

            <h2 id="s9">9. Acceptable use</h2>
            <p>You agree not to:</p>
            <ul>
                <li>break any law or encourage anyone else to;</li>
                <li>post Content that is unlawful, fraudulent, defamatory, hateful, harassing, threatening, sexually explicit or otherwise offensive;</li>
                <li>pretend to be another person or business, or misrepresent your qualifications or experience;</li>
                <li>post spam, advertise unrelated or competing services, or send unsolicited marketing to other Users;</li>
                <li>copy, scrape, harvest or collect Profiles, contact details or other information from the Site by automated means or otherwise, without our written permission;</li>
                <li>upload viruses or harmful code, or try to hack, overload, disrupt or bypass the security of the Site;</li>
                <li>reverse engineer or copy the Site's code or design; or</li>
                <li>use the Site for anything other than its intended purpose of finding and promoting beauty professionals.</li>
            </ul>

            <h2 id="s10">10. Our intellectual property</h2>
            <p>The Site, including its design, text, graphics, logo, name and software, belongs to us or our licensors and is protected by law. We give you a limited, personal, non-transferable right to use the Site as intended. You may not copy, sell or exploit any part of it without our written permission, except for your own Content.</p>

            <h2 id="s11">11. Privacy and personal information</h2>
            <p>Our <a href="<?= SITE_URL ?>/privacy.php">Privacy Policy</a> explains what personal information we collect, why, who we share it with, how long we keep it, and your rights under the Protection of Personal Information Act 4 of 2013 ("POPIA"). It forms part of these Terms. In particular, please remember that:</p>
            <ul>
                <li>a Professional's Profile, business name, location, registered phone number and email address, and photos are <strong>public</strong>;</li>
                <li>a Client's name is shown next to any review they write;</li>
                <li>you must not post other people's personal information, including photos of them, without their consent; and</li>
                <li>you can ask us to access, correct or delete your information at any time using the details in section 22.</li>
            </ul>

            <h2 id="s12">12. Third-party services and links</h2>
            <p>The Site uses and links to services we do not control, such as WhatsApp, email and phone services, OpenStreetMap and Google Fonts, and may link to Professionals' own pages. We are not responsible for their content, availability or privacy practices, and you use them at your own risk and subject to their own terms.</p>

            <h2 id="s13">13. Availability and changes to the Site</h2>
            <p>We try to keep the Site running smoothly, but we do not promise that it will always be available, secure or free of errors. We may change, suspend or discontinue any part of the Site at any time, for example for maintenance or improvements.</p>

            <h2 id="s14">14. Disclaimers</h2>
            <p>To the extent the law allows, the Site and everything on it is provided "as is" and "as available". We do not guarantee the accuracy, quality, safety or legality of any Profile, Content, review or service, and we do not endorse any Professional. Listing on the Site is not a recommendation or a guarantee of any standard.</p>

            <h2 id="s15">15. Limitation of liability</h2>
            <ol class="legal-sub">
                <li>To the maximum extent permitted by law, BeautySpot will not be liable for any indirect, incidental or consequential loss, or for loss of profit, income, data, goodwill or business opportunity, arising from your use of the Site.</li>
                <li>We are not liable for the acts or omissions of any User, or for any injury, allergic reaction, damage to hair, skin, nails or property, or other harm arising from a service arranged through the Site.</li>
                <li>Where we cannot lawfully exclude our liability, our total liability to you for all claims relating to the Site is limited to R1 000 (one thousand Rand) or the amount you have paid us in the previous 12 months, whichever is greater.</li>
                <li>Nothing in these Terms excludes or limits any liability that cannot be excluded or limited under South African law, including liability for gross negligence or intentional misconduct and rights you have under the Consumer Protection Act 68 of 2008 where it applies.</li>
            </ol>

            <h2 id="s16">16. Indemnity</h2>
            <p>You agree to indemnify and hold harmless BeautySpot and its owners, directors, employees and agents against any claim, loss, damage or cost (including reasonable legal fees) arising from your Content, your breach of these Terms or the law, or your dealings with other Users.</p>

            <h2 id="s17">17. Suspension and termination</h2>
            <ol class="legal-sub">
                <li>We may remove Content, suspend or close your account, or restrict access to the Site at any time, with or without notice, if we believe you have broken these Terms or the law, or that your use puts the Site or other people at risk.</li>
                <li>You may stop using the Site at any time. To have your account and Profile deleted, contact us using the details in section 22. Once deleted, your Profile will no longer appear in the directory.</li>
                <li>We may keep some information after deletion where we need it for legal, security or accounting reasons. Reviews you posted may stay on a Professional's Profile or be removed, at our discretion.</li>
                <li>Sections that by their nature should continue after termination, such as those on Content, liability, indemnity and governing law, will do so.</li>
            </ol>

            <h2 id="s18">18. Reporting content and complaints</h2>
            <p>If you see Content that is unlawful, infringes your rights or breaks these Terms, email us at <?= $EMAIL ?> and tell us where it is and why it is a problem. We will review it and may remove it. If you believe Content infringes your rights, your notice should include the information required by section 77 of the Electronic Communications and Transactions Act 25 of 2002: your full name and address, a signature (which may be electronic), identification of the right infringed, a description of the material and where it appears, your contact details, a statement that you are acting in good faith and that the information is accurate, and a statement that you are the rights owner or authorised to act for them.</p>

            <h2 id="s19">19. Changes to these Terms</h2>
            <p>We may update these Terms from time to time. The updated version will be posted on this page with a new "last updated" date. If you keep using the Site after a change, you accept the updated Terms. If a change is significant, we will try to tell you, for example with a notice on the Site.</p>

            <h2 id="s20">20. General</h2>
            <ul>
                <li><strong>Whole agreement.</strong> These Terms are the whole agreement between you and us about your use of the Site.</li>
                <li><strong>If part is invalid.</strong> If any part of these Terms is found to be unenforceable, the rest stays in force.</li>
                <li><strong>No waiver.</strong> If we do not enforce a right straight away, we have not given it up.</li>
                <li><strong>No partnership.</strong> Nothing in these Terms makes you our partner, employee or agent.</li>
                <li><strong>Transfer.</strong> We may transfer our rights and duties under these Terms to a person or company that takes over the Site, including a company we set up to run it. You may not transfer yours without our written consent.</li>
                <li><strong>Electronic communication.</strong> You agree that we may communicate with you and give you notices by email or on the Site, and that these have the same effect as written notices.</li>
            </ul>

            <h2 id="s21">21. Governing law and disputes</h2>
            <p>These Terms are governed by the laws of the Republic of South Africa. If you have a problem, please contact us first so we can try to sort it out. If we cannot, the South African courts have jurisdiction over any dispute. Nothing in this section limits your right to approach the National Consumer Commission, the Information Regulator or any other body or forum you are entitled to use.</p>

            <h2 id="s22">22. Contact us</h2>
            <address class="legal-contact">
                <strong><?= $LEGAL_NAME ?></strong><br>
                <?php if ($LEGAL_REG !== ''): ?>Registration number: <?= $LEGAL_REG ?><br><?php endif; ?>
                Physical address: <?= $LEGAL_ADDR ?><br>
                Email: <?= $EMAIL ?><br>
                Phone: <?= $PHONE ?>
            </address>
        </article>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
