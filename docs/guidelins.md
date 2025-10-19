2) Styling approach (scalable & safe for PHP/XAMPP)

Keep Bootstrap (already in libs/css/bootstrap.min.css) for grid/utilities + add a lightweight custom stylesheet:

Create libs/css/ovas.css with CSS variables (--brand, --accent, spacing scale).

Use AOS (Animate On Scroll) for section reveals (no build step).

Use Swiper for the hero carousel (touch-friendly).

Use IntersectionObserver + Bootstrap ScrollSpy to update active nav on scroll.

Convert images to WebP and lazy-load; use <picture> for fallbacks.

You can later migrate to Tailwind/SCSS, but this path is zero-build and won’t break legacy PHP.

3) COPILOT PROMPTS — paste these one by one in VS Code
Prompt 1 — Create the one-page homepage scaffolding

“Create a new index.php homepage that replaces the old layout with a single scrolling page. Keep PHP includes for header/footer to stay DRY.

Files to touch:

inc/header.php → HTML <head> links + global CSS/JS

inc/navigation.php → sticky navbar with anchors: #home #services #appointment #about #contact

index.php → sections in this order: Home(hero), Services, Appointment, About, Contact, Footer

inc/footer.php → multi-column footer

Sections: use IDs and semantic tags. Example:

<?php include 'inc/header.php'; ?>
<?php include 'inc/navigation.php'; ?>

<main id="home" class="section-home"> ... </main>
<section id="services" class="section-services"> ... </section>
<section id="appointment" class="section-appointment"> ... </section>
<section id="about" class="section-about"> ... </section>
<section id="contact" class="section-contact"> ... </section>

<?php include 'inc/footer.php'; ?>


Add data-bs-spy="scroll" + data-bs-target="#mainNav" to <body> for ScrollSpy.

Ensure each section has tabindex="-1" and enough height/padding for smooth scroll offsets.”

Prompt 2 — Update header includes (CSS/JS/CDNs)

“Edit inc/header.php to add:

<link rel="preconnect" href="https://fonts.gstatic.com"> and a Google Font (Inter/Roboto).

Keep libs/css/bootstrap.min.css.

Add libs/css/ovas.css (create empty file now).

Add AOS and Swiper via CDN:

<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">
<link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
<script defer src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
<script defer src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


Add small assets/js/ovas.js (create file) to init AOS, Swiper, and IntersectionObserver.

Set <meta name="viewport" content="width=device-width, initial-scale=1">.”

Prompt 3 — Sticky navbar + ScrollSpy + smooth scroll

“Edit inc/navigation.php:

Sticky top navbar with brand SVG logo (from manifest) and anchors: Home, Services, Appointment, About, Contact.

Add id="mainNav"; set data-bs-theme="light".

Smooth scroll behavior in CSS: html { scroll-behavior: smooth; }.

Add an underline/active state that updates when sections enter view (use Bootstrap scrollspy + JS fallback with IntersectionObserver).”

Prompt 4 — Hero section with image carousel + CTA

“In index.php Home section:

A Swiper carousel with 3 slides referencing hero_01.webp, hero_02.webp, hero_03.webp from the manifest. Use <picture> and loading="lazy".

Left-aligned headline + subhead + primary CTA ‘Book Appointment’ (anchors to #appointment).

Add a small icon in CTA from Lucide (calendar-plus).

Add subtle floating SVG accent from hero_accent.svg.

Provide accessible text contrast (overlay gradient).”

Prompt 5 — Services grid (cards with icons)

“In #services:

6–8 service cards in a responsive grid (2/3/4 across).

Each card: circular icon (SVG from manifest), title, one-sentence description, chip for typical duration/fee.

Add entrance animations with AOS (data-aos="fade-up" staggered).”

Prompt 6 — Appointment section (prefill-aware UI)

“In #appointment:

Two-column layout: left illustration (appointment_header.svg), right booking form.

Form fields (for logged-out users show all fields): Name, Email, Contact, Address, Pet Name, Species (select), Service (select), Date, Time Slot (select), Notes.

If $_SESSION['user'] exists (name/email/phone/address), prefill and lock non-editable or mark as filled.

Include CSRF hidden token placeholder (server-side later).

Submit posts to add_appointment.php for now; show a success panel with illustration appointment_success.svg after redirect to success_msg.php.

Use Bootstrap validation styles, show help text, and loading spinner on submit.”

Prompt 7 — About section (team + values)

“In #about:

Banner photo (about_clinic.webp) with overlay title.

3 staff cards using team portraits from manifest, each with role and short bio.

KPI counters: ‘Years in Service’, ‘Happy Pets’, ‘5-Star Reviews’ with Lucide icons (award/shield/heart) and AOS count-up animation (JS).”

Prompt 8 — Contact section (form + channels + map)

“In #contact:

Left: contact form (Name, Email, Phone, Message) → POST to contact_us.php.

Right: illustration contact_art.svg; below it, a map embed OR contact_map_placeholder.svg.

Channel icons row (phone/mail/WhatsApp/Instagram from SimpleIcons).

Add success/error toasts (icon_check_circle / icon_x_circle).”

Prompt 9 — Footer (React-style componentization in PHP)

“Edit inc/footer.php:

4 columns: About, Quick Links (anchors to sections), Services (top 6), Contact.

Small image strip: footer_clinic.webp or subtle bg_paw_pattern.svg.

Secondary bar with copyright + social icons.

Structure footer as partials so future pages can reuse it.”

Prompt 10 — Create libs/css/ovas.css with tokens & utilities

“Create libs/css/ovas.css with:

CSS variables:

:root{
  --brand:#2563eb; --accent:#10b981; --text:#0f172a; --muted:#64748b;
  --bg:#f8fafc; --card:#ffffff; --radius:1rem; --shadow:0 10px 25px rgba(2,8,23,.08);
}


Utility classes: .btn-brand, .badge-soft, .card-hover, .section-pad (min 96px top/bottom), .heading-xl, .muted.

.navbar.scrolled style (add drop shadow + backdrop).

.swiper height rules for hero; gradient overlays.

Responsive typography (clamp).”

Prompt 11 — Create assets/js/ovas.js (init scripts)

“Add assets/js/ovas.js:

Initialize AOS.

Initialize Swiper with autoplay, pagination, and keyboard.

IntersectionObserver to toggle .active class on nav links based on section in view.

Optional: counter animation for KPIs.

Lazy-loaded images: add loading="lazy" and decoding="async" to all non-hero images.”

Prompt 12 — Wire images using the CSV manifest

“Write a small PHP helper in inc/packages.php:

Load /ovas_image_manifest_homepage.csv (if present) and map Section ID + Example Filename to source URLs for Copilot to insert correct <img src> or <use> for SVGs.

Provide a asset($filename) function that returns /uploads/assets/$filename (we’ll upload actual files later), otherwise returns the Suggested Source URL as a fallback for <a href> or background image references.”

Prompt 13 — Accessibility + performance

“Add aria-labels, proper heading order (h1 only once), color contrast ≥4.5:1, focus outlines, and prefers-reduced-motion CSS to disable heavy animations. Ensure CLS wins: set explicit width/height on images.”

4) Where each image goes (from the manifest)

Use the CSV you downloaded; here’s the mapping summary:

Navbar: logo_ovas.svg + nav icons.

Hero/Swiper: hero_01.webp, hero_02.webp, hero_03.webp, plus hero_accent.svg.

Services: per-card icons (svc_icon_*) + optional square thumbnails.

Appointment: appointment_header.svg; success state uses appointment_success.svg.

About: about_clinic.webp, team_vet1/2/3.webp, KPI icons.

Contact: contact_art.svg, map placeholder, channel icons.

Footer: bg_paw_pattern.svg, footer_clinic.webp.

UI: Lottie loading.json, toast icons.

5) Optional Copilot follow-ups you can run later

“Refactor appointment form to prefill from $_SESSION['user'] if set (name, email, phone, address), with fields locked and an ‘Edit’ toggle.”

“Add CSRF token issuance in initialize.php and verify on POST in add_appointment.php & contact_us.php.”

“Create inc/sections/*.php partials (home/services/appointment/about/contact) and include them from index.php to keep the file clean.”