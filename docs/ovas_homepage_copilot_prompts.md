# OVAS — One‑Page Homepage Redesign: Copilot Prompt Pack

**Purpose**: Copy‑paste each prompt into Copilot (Claude Sonnet 4.5) in VS Code to generate and wire the new one‑page homepage for OVAS. Prompts assume the legacy PHP project structure under `C:\xampp\htdocs\ovas` and keep Bootstrap while adding a modern UX layer (AOS, Swiper, custom CSS, light JS).

**Outcomes**:
- A single‑page homepage with sections: **Home (Hero)**, **Services**, **Appointment**, **About**, **Contact**, **Footer**.
- Scroll‑activated navbar highlighting, smooth navigation, responsive layout.
- Image‑rich UI using only free assets as mapped in the CSV manifest(s).

---

## Prerequisites (say this to Copilot once)
**Prompt A — Project prep and conventions**
> You are working on a PHP project under `C:\xampp\htdocs\ovas`. Keep existing includes where possible. Use Bootstrap already found in `libs/css/bootstrap.min.css`. Add two new files: `libs/css/ovas.css` and `assets/js/ovas.js` (create folders if missing). Use semantic HTML. Use accessible patterns (labels, aria, roles), lazy‑load images, and preserve PHP include patterns. Do not remove existing `inc/*` includes; extend them.

**Prompt B — Asset Manifests**
> The project uses two CSV manifests for images and icons: `/ovas_image_manifest_homepage.csv` and `/ovas_image_manifest.csv`. Treat `Example Filename` as the intended local filename under `/uploads/assets/` and `Suggested Source URL` as a reference link. For now, reference local filenames in `<img src="/uploads/assets/...">` with `loading="lazy"` and add alt text. Where SVG icons are from Lucide/Tabler/Heroicons, inline them or link to the CDN. Ensure every image has width/height or aspect‑ratio to prevent CLS.

---

## Prompt 1 — Create the one‑page scaffolding
> Create a new `index.php` homepage that replaces the old landing experience with a single scrolling page. Keep: `<?php include 'inc/header.php'; ?>`, `inc/navigation.php`, and `inc/footer.php`. Inside `index.php`, output the following section skeleton in this exact order and with these IDs: `#home` (hero), `#services`, `#appointment`, `#about`, `#contact`. Each section must use `.section-pad` spacing classes and include a `<div class="container">` with Bootstrap grid. Add comments `<!-- SECTION: Home -->`, `<!-- SECTION: Services -->`, etc., above each section for maintainability. End the file with `<?php include 'inc/footer.php'; ?>`.

**Acceptance**: The new index shows 5 sections stacked vertically, no broken PHP includes, and temporary placeholder text in each container.

---

## Prompt 2 — Update header includes (fonts, CSS, JS)
> Edit `inc/header.php` to include: viewport meta, Inter or Roboto from Google Fonts, the existing `libs/css/bootstrap.min.css`, the new `libs/css/ovas.css`, and two libraries over CDN—AOS (Animate On Scroll) and Swiper. Add `assets/js/ovas.js` with `defer`. Verify the `<head>` has a descriptive `<title>` and `<meta name="description">`. Do not remove existing includes. Also add `<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>` and set `<meta http-equiv="x-ua-compatible" content="ie=edge">`.

**Acceptance**: Page loads without console errors; `AOS.init()` and `new Swiper()` will be available in `assets/js/ovas.js`.

---

## Prompt 3 — Sticky navbar with ScrollSpy & smooth scroll
> Edit `inc/navigation.php` to implement a sticky top Bootstrap navbar with id `mainNav`. Include brand SVG logo from `/uploads/assets/logo_ovas.svg` (placeholder if missing). Add nav anchors that link to `#home`, `#services`, `#appointment`, `#about`, and `#contact`. Use Bootstrap ScrollSpy by placing `data-bs-spy="scroll" data-bs-target="#mainNav"` on the `<body>` (put in `inc/header.php` if more convenient), and offset for navbar height. Add a JS fallback using IntersectionObserver in `assets/js/ovas.js` to toggle the `.active` class on the corresponding nav link when the section is in view. Also enable smooth scrolling with `html { scroll-behavior: smooth; }` in `ovas.css`.

**Acceptance**: Scrolling highlights the proper nav link; clicking any nav item smoothly scrolls to the section.

---

## Prompt 4 — Hero section with Swiper carousel and CTA
> In `index.php` under `#home`, create a full‑width Swiper carousel with three slides. Use images `hero_01.webp`, `hero_02.webp`, `hero_03.webp` from `/uploads/assets/` and provide `<picture>` fallbacks if WebP is absent. Overlay a headline, subhead, and a primary CTA button linking to `#appointment`, with an inline Lucide `calendar-plus` icon. Add a subtle gradient overlay for text contrast and a decorative SVG `hero_accent.svg` positioned absolutely in a corner. Swiper config: autoplay (5s), pagination bullets, keyboard nav, pause on hover, and proper aria roles. Ensure slides are `min-height: 70vh` and content is vertically centered.

**Acceptance**: The hero rotates smoothly, text remains legible, the CTA scrolls to `#appointment`, and the component is responsive.

---

## Prompt 5 — Services grid (icons + optional photos)
> In `#services`, build a responsive grid (2 cols on xs, 3 on md, 4 on lg). Each card shows: a circular icon (SVG from the manifest: vaccination/tooth/grooming/microscope/etc.), a service title, a one‑sentence description, and an optional small square thumbnail (`svc_photo_generic.webp`). Apply `data-aos="fade-up"` on each card with staggered delays (100ms increments). Use `.card-hover` style (add in `ovas.css`) for subtle elevation on hover. Include a `View more services` anchor to `/services.php`.

**Acceptance**: Cards are consistent, icons crisp, grid is responsive, and animations trigger on scroll.

---

## Prompt 6 — Appointment section (prefill‑aware form)
> In `#appointment`, create a two‑column layout: left column contains `appointment_header.svg` illustration; right column contains a booking form. Fields: Name, Email, Contact, Address, Pet Name, Species (select), Service (select), Date (date picker), Time Slot (select), Notes (textarea). If `$_SESSION['user']` has `name`, `email`, `phone`, and `address`, prefill and mark them as filled; allow an "Edit" toggle to unlock. Add a hidden `csrf_token` input (we will implement token issuance later). On submit, POST to `add_appointment.php` and show a loading spinner. After success redirect, `success_msg.php` must display a success panel using `appointment_success.svg` and a summary of the appointment. Use Bootstrap validation and aria‑live regions for errors.

**Acceptance**: Form validates client‑side, prefills when logged in, and posts to existing PHP endpoint without breaking.

---

## Prompt 7 — About section (team + KPI counters)
> In `#about`, add a banner image `about_clinic.webp` with a soft overlay and section title. Below, render three team cards using `team_vet1.webp`, `team_vet2.webp`, `team_vet3.webp` with names/roles. Add a KPI row with 3 counters (e.g., Years in Service, Happy Pets, 5‑Star Reviews). Use Lucide icons `award`, `shield-check`, and `heart`. Implement a simple count‑up animation in `assets/js/ovas.js` (starts when counters enter viewport). Ensure all images are `loading="lazy"` and have width/height attributes.

**Acceptance**: Team cards and KPIs render nicely on mobile and desktop; counting animation is smooth and accessible.

---

## Prompt 8 — Contact section (form + channels + map)
> In `#contact`, create a contact form with Name, Email, Phone, Message → POST to `contact_us.php`. Use server‑friendly names for inputs. On the right, include `contact_art.svg` and below it a map embed (optional) or `contact_map_placeholder.svg`. Add a row of channel icons (phone/mail/WhatsApp/Instagram) from SimpleIcons with accessible labels. Implement success/error toasts using two SVGs: `icon_check_circle.svg` and `icon_x_circle.svg`. Provide basic spam protection placeholder (honeypot input hidden with CSS).

**Acceptance**: The form posts to the existing endpoint, and visible feedback is shown for success/failure.

---

## Prompt 9 — Footer with multi‑column layout & sublinks
> Edit `inc/footer.php` to create a four‑column footer: About (short text), Quick Links (anchors to each homepage section), Services (top 6 links to `/services.php#<slug>`), and Contact (address, phone, email). Add a subtle patterned background using `bg_paw_pattern.svg` and an optional small photo strip `footer_clinic.webp`. Include a bottom bar with copyright and social icons. Factor any repeated bits into small PHP partials (e.g., `inc/footer-links.php`) to encourage reuse.

**Acceptance**: Footer appears across pages, looks consistent, and has working anchor links.

---

## Prompt 10 — Create `libs/css/ovas.css` (design tokens + utilities)
> Create `libs/css/ovas.css` and add:
> 1) CSS variables for color, spacing, radii, and shadow:
```css
:root{
  --brand:#2563eb; --accent:#10b981; --text:#0f172a; --muted:#64748b;
  --bg:#f8fafc; --card:#ffffff; --radius:1rem;
  --shadow:0 10px 25px rgba(2,8,23,.08);
}
html{scroll-behavior:smooth}
.section-pad{padding-block:96px}
.heading-xl{font-size:clamp(1.75rem,2.5vw,2.75rem);font-weight:700;color:var(--text)}
.muted{color:var(--muted)}
.card-hover{box-shadow:var(--shadow);transition:transform .2s ease, box-shadow .2s ease}
.card-hover:hover{transform:translateY(-3px);box-shadow:0 16px 30px rgba(2,8,23,.12)}
.navbar.scrolled{backdrop-filter:saturate(120%) blur(6px);box-shadow:0 8px 20px rgba(2,8,23,.08)}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.35),rgba(0,0,0,.15))}
.swiper,.swiper-slide{min-height:70vh}
```
> 2) Image rules to reduce CLS: set `.ratio-16x9` helper or explicit `width/height` on `<img>`.
> 3) Media queries for comfortable spacing on mobile.

**Acceptance**: Visual consistency improves without touching Bootstrap core files.

---

## Prompt 11 — Create `assets/js/ovas.js` (init AOS, Swiper, IO)
> Create `assets/js/ovas.js` with the following behaviors:
> - `AOS.init({ duration: 700, once: true });`
> - Initialize Swiper for `.hero-swiper` with autoplay 5000ms, pagination, and keyboard.
> - IntersectionObserver that adds `.active` to the correct navbar link when a section is ≥50% in view; also toggles `.navbar.scrolled` when pageY > 10.
> - Count‑up function for KPI numbers, triggered once when they intersect.
> - Utility: add `loading="lazy" decoding="async"` to images that lack it (runtime enhancement, optional).

**Acceptance**: No console errors; nav highlights update; KPIs count up; hero slides autoplay.

---

## Prompt 12 — PHP asset helper (optional but recommended)
> In `inc/packages.php`, add a helper `function asset($filename){ return '/uploads/assets/'.$filename; }`. Add a safe `svg()` helper that can inline a small SVG from `/uploads/assets/` if present; otherwise, returns an `<img>` tag fallback. Update sections to call `asset('hero_01.webp')` etc., so we can switch between local and CDN assets later.

**Acceptance**: All image paths go through a single helper, making swaps easy.

---

## Prompt 13 — Accessibility & performance pass
> Ensure each section has a unique `h2` under the single page `h1`. Provide alt text for all images (describe the content, not file names). Enforce color contrast ≥ 4.5:1 for text on backgrounds; if not, darken overlays. Add `role="navigation"` on nav, `role="contentinfo"` on footer, and aria labels on icons. Add fixed width/height or `aspect-ratio` to hero and card images. Confirm Core Web Vitals: CLS < 0.1, LCP under 2.5s on a 3G throttle using devtools.

**Acceptance**: Lighthouse ≥ 90 for Accessibility and Best Practices on the homepage.

---

## Prompt 14 — Commit + Milestones
> Create git commits after each successful section: `feat(home): one‑page scaffolding`, `feat(hero): swiper hero with CTA`, `feat(services): grid with icons`, `feat(appointment): prefill‑aware form`, `feat(about): team + KPIs`, `feat(contact): form + channels`, `feat(footer): multi‑column footer`, `feat(ui): tokens, AOS, swiper, IO`. Push to remote.

---

## Section‑to‑Asset Map (quick reference)
- **nav**: `logo_ovas.svg`, nav icons
- **home**: `hero_01.webp`, `hero_02.webp`, `hero_03.webp`, `hero_accent.svg`
- **services**: `svc_icon_*` + optional `svc_photo_generic.webp`
- **appointment**: `appointment_header.svg`, `appointment_success.svg`
- **about**: `about_clinic.webp`, `team_vet1.webp`, `team_vet2.webp`, `team_vet3.webp`, icons `award`, `shield-check`, `heart`
- **contact**: `contact_art.svg`, `contact_map_placeholder.svg`, channel icons
- **footer**: `bg_paw_pattern.svg`, `footer_clinic.webp`
- **ui**: `loading.json`, `icon_check_circle.svg`, `icon_x_circle.svg`

> When you ask for code, Copilot should write **only the changed files**, preserve PHP includes, and reference assets via `asset()`.

---

## Done‑Definition Checklist (for each prompt)
- No PHP notices/warnings in logs
- No console errors
- Mobile view ≤ 375px works
- Images lazy‑load and have alt text + sizes
- Nav highlights update on scroll
- Hero carousel accessible
- Forms validate and post to PHP endpoints

---

**Manifests**: Ensure `ovas_image_manifest_homepage.csv` and `ovas_image_manifest.csv` live at project root (or `/uploads/assets/`). Keep filenames consistent with the prompts. Use only free sources listed in the CSVs.
