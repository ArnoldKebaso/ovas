0) Master brief (paste once at the top of your first Copilot chat)
==================================================================

You are a senior PHP engineer. We’re upgrading an existing PHP project at C:\\xampp\\htdocs\\ovas into a production-grade Veterinary Management & Appointment System using **plain PHP** (no framework) with clean MVC-ish organization, secure auth, M-Pesa STK Push (KES 500), Google Calendar, and Mail/SMS reminders.**Timezone:** Africa/Nairobi. **DB:** MySQL 8, schema already created (see database\\ovas\_db.sql).**Env:** we will load from .env (see keys at the bottom of this message).Use **PDO + prepared statements**, password\_hash()/password\_verify(), **CSRF tokens** on all POSTs, and login rate limiting.Reuse our existing UI files:

Plain textANTLR4BashCC#CSSCoffeeScriptCMakeDartDjangoDockerEJSErlangGitGoGraphQLGroovyHTMLJavaJavaScriptJSONJSXKotlinLaTeXLessLuaMakefileMarkdownMATLABMarkupObjective-CPerlPHPPowerShell.propertiesProtocol BuffersPythonRRubySass (Sass)Sass (Scss)SchemeSQLShellSwiftSVGTSXTypeScriptWebAssemblyYAMLXML`   /inc/topBarNav.php  /inc/footer.php  /home.php /index.php /services.php /contact_us.php /about_us.php   `

We will add services & APIs in an incremental way, **without** breaking the current front-end.

1) Bootstrap config + PDO + CSRF (safe foundation)
==================================================

**Prompt:**Create or update the following to centralize config, env, DB, auth, and CSRF, keeping our current layout:

**Files to create/update**

1.  /.env.example (copy our keys below and add MPESA\_SHORTCODE, MPESA\_PASSKEY, BASE\_URL).
    
2.  /config.php
    
    *   Load .env using vlucas/phpdotenv (add composer if missing).
        
    *   Define a config($key,$default=null) helper that reads env with sensible defaults.
        
    *   Set date\_default\_timezone\_set('Africa/Nairobi').
        
    *   Start secure session (session\_set\_cookie\_params(\['httponly'=>true,'samesite'=>'Lax'\])), session\_start().
        
3.  /initialize.php
    
    *   require\_once \_\_DIR\_\_.'/config.php';
        
    *   Add function db(): PDO returning a singleton PDO from env (DB\_\*) with ERRMODE\_EXCEPTION, ATTR\_DEFAULT\_FETCH\_MODE=>FETCH\_ASSOC.
        
4.  /inc/sess\_auth.php
    
    *   Add helpers: auth\_user(), auth\_id(), is\_admin(), require\_login(), csrf\_token(), csrf\_verify($token).
        
    *   On login, session\_regenerate\_id(true) and store a simple UA hash to mitigate hijacking.
        
5.  /composer.json
    
    *   Ensure: "vlucas/phpdotenv": "^5.6", "guzzlehttp/guzzle": "^7.8", "phpmailer/phpmailer": "^6.9", "google/apiclient": "^2.17"
        
    *   composer install
        
6.  Add **global error page** for 404/500: /404.html already exists; create /500.html simple fallback.
    

**Acceptance**

*   test\_db.php connects via PDO and fetches services successfully.
    
*   csrf\_token() prints a token in a dummy form; csrf\_verify() rejects wrong tokens.
    

2) Models (PDO) for core tables
===============================

**Prompt:**Create lightweight models with only the queries we need now. Use functions or small classes under /classes to match your structure.

**Files**

*   /classes/Users.php: findByEmail($email), create($data), find($id), update($id,$data), allOwners().
    
*   /classes/Pets.php: allByUser($userId), create($data), find($id), update($id,$data), delete($id).
    
*   /classes/Services.php: active(), find($id), create/update/toggle.
    
*   /classes/TimeSlots.php: active(), find($id).
    
*   /classes/Appointments.php:
    
    *   capacityUsed($date,$slotId) (count where status!='cancelled')
        
    *   create($data) (code = OVAS-YYYYMMDD- unique retry)
        
    *   byUser($userId), find($id), updateStatus($id,$status), reschedule($id,$date,$slotId).
        
*   /classes/Payments.php: createInitiated($data), markSuccess($id,$ref,$raw), markFailed($id,$raw), attachAppointment($paymentId,$appointmentId).
    

**Acceptance**

*   Minimal unit test script queries services/time slots and inserts a dummy pet.
    

3) Auth endpoints (register/login/logout) using your existing pages
===================================================================

**Prompt:**Wire auth in **plain PHP** using the new users table.

**Files**

*   /auth.php
    
    *   POST /auth.php?action=register: validate (name,email,phone,password), hash with password\_hash(), insert user, auto-login, redirect with flash.
        
    *   POST /auth.php?action=login: rate-limit (max 5 attempts/5min using login\_attempts), verify with password\_verify(), set session, redirect.
        
    *   GET /auth.php?action=logout: destroy session and redirect to ./?page=home.
        
*   Update forms in your existing UI to post to /auth.php and include csrf\_token().
    

**Acceptance**

*   Can register and log in; the navbar shows “Hi, {name}” and an “Admin” link if is\_admin=1.
    

4) Services: replace hardcoded with DB data
===========================================

**Prompt:**Replace mock services on /services.php and service cards in home with dynamic data.

**Tasks**

*   In services.php and the services section on home.php, call Services::active() and loop.
    
*   Link “Book” to /appointment.php?service\_id={id}.
    

**Acceptance**

*   Services page lists currently active services from DB.
    

5) Appointment API: availability + booking payload
==================================================

**Prompt:**Create two endpoints and integrate your existing front-end JS (/assets/js/ovas.js):

**Files**

*   /check\_availability.php (POST, CSRF)
    
    *   Inputs: service\_id, date (YYYY-MM-DD).
        
    *   Return JSON of **active** time slots with an available boolean usingTimeSlots::active() and Appointments::capacityUsed(date, slot\_id) < time\_slots.max\_appointments.
        
*   /get\_service\_details.php (GET)
    
    *   Returns fee, duration for selected service\_id.
        

Update /appointment.php wizard to:

*   Step 1: Choose service (from DB).
    
*   Step 2: If not logged in, show inline login/register; else show pets list (+ “Add Pet” modal).
    
*   Step 3: Pick date → AJAX check\_availability.php → show only available slots.
    
*   Step 4: Notes → **Proceed to Payment** (posts to /submit\_appointment.php to start M-Pesa initiation).
    

**Acceptance**

*   Availability responses correct even with multiple bookings.
    

6) M-Pesa STK Push (Daraja sandbox) — Initiate + Callback
=========================================================

**Prompt:**Create a service class and two endpoints. Use **env** keys:

*   CONSUMER\_KEY, CONSUMER\_SECRET, MPESA\_SHORTCODE, MPESA\_PASSKEY, CALLBACK\_URL, BOOKING\_FEE=500, BASE\_URL.
    

**Files**

*   /services/MpesaService.php
    
    *   Methods:
        
        *   accessToken() (cache for 50 min in $\_SESSION or a temp file).
            
        *   stkPush($phone, $amount, $accountRef='OVAS', $desc='Booking Fee')
            
            *   Timestamp YmdHis; Password = base64\_encode(SHORTCODE.PASSKEY.TIMESTAMP).
                
            *   Endpoint: sandbox URL (don’t hardcode prod).
                
            *   Return the API raw response (JSON) for logging.
                
*   /submit\_appointment.php (POST, CSRF)
    
    *   Validate logged in, validate capacity **again** for the chosen date + time\_slot\_id.
        
    *   Create a payments row with status='initiated', amount BOOKING\_FEE.
        
    *   Call MpesaService::stkPush($phone, 500, 'OVAS', 'Booking Fee').
        
    *   Store pending booking payload in $\_SESSION\['pending\_booking'\] (service\_id, pet\_id, date, slot, notes, payment\_id).
        
    *   Redirect to a “Awaiting Payment” page with simple instructions.
        
*   /payments/mpesa/callback.php (public, no CSRF)
    
    *   Parse callback JSON. On **success**:
        
        *   Payments::markSuccess($paymentId,$mpesaReceipt,$rawPayload).
            
        *   Start DB transaction:
            
            1.  Create appointments row: payment\_status='paid', status = confirmed if appointments\_auto\_confirm=1 (from system\_info) else pending. Generate unique code.
                
            2.  Payments::attachAppointment($paymentId,$appointmentId).
                
        *   Commit.
            
        *   Trigger **email + SMS** confirmation (see next prompt).
            
        *   Optionally redirect user to /success\_msg.php?code=....
            
    *   On failure: mark failed, show retry.
        

**Acceptance**

*   Initiating STK shows prompt on sandbox phone; success webhook inserts a confirmed appointment and paid payment.
    

7) Email (PHPMailer) + SMS (TextSMS provider) + Notification logs
=================================================================

**Prompt:**Add Mail & SMS services and log every send to notification\_logs.

**Files**

*   /services/Mailer.php (PHPMailer)
    
    *   Configure from env: MAIL\_HOST, MAIL\_PORT, MAIL\_USERNAME, MAIL\_PASSWORD, MAIL\_FROM.
        
    *   send($toEmail,$toName,$subject,$html,$text='').
        
*   /services/SmsService.php
    
    *   Use your existing **TEXTSMS** creds from env: TEXTSMS\_API\_KEY, TEXTSMS\_PARTNER\_ID, TEXTSMS\_SHORTCODE (and any SMS\_API\_KEY, SMS\_USERNAME if needed).
        
    *   send($phone, $message) via provider’s HTTP endpoint (use GuzzleHttp\\Client; endpoint and params from env).
        
*   /views/emails/confirmed.php, /views/emails/reminder.php, /views/emails/cancelled.php
    
    *   Simple HTML templates with clinic name, appointment code, service, pet, date/time, and a “Manage” link (cancel/reschedule).
        
*   /classes/Notifications.php
    
    *   log($userId,$channel,$templateKey,$payload,$status='sent',$error=null).
        

**Hook points**

*   After a successful payment → send **confirmation email + SMS**; log both rows to notification\_logs.
    

**Acceptance**

*   After a sandbox “payment success”, an email (SMTP) and a dummy SMS request are sent (verify logs).
    

8) Google Calendar integration (service account or OAuth)
=========================================================

**Prompt:**Add a Calendar service to create/update/delete events for **confirmed** appointments.

**Files**

*   /services/CalendarService.php
    
    *   Env: GOOGLE\_CALENDAR\_ID, GOOGLE\_CREDENTIALS\_JSON\_PATH (place the JSON in /storage/google/credentials.json).
        
    *   Methods:
        
        *   createEvent($appointment) → return eventId
            
        *   updateEvent($appointment) → by google\_event\_id
            
        *   deleteEvent($eventId)
            
    *   Event fields:
        
        *   summary: ${service\_name} – ${pet\_name} (${owner\_name})
            
        *   description: include notes and appointment code
            
        *   start: schedule\_date + time\_slot.start\_time (TZ Africa/Nairobi)
            
        *   end: schedule\_date + time\_slot.end\_time
            
        *   attendees: optional owner email.
            
*   After creating the appointment (payment success), call CalendarService::createEvent() and save google\_event\_id back onto the appointment.
    
*   On reschedule → updateEvent().
    
*   On cancel → deleteEvent().
    

**Acceptance**

*   A new event appears in the configured Calendar with correct times; moving an appointment updates the event.
    

9) Owner actions: reschedule + cancel (with cutoff)
===================================================

**Prompt:**Create endpoints to allow owners (and admins) to manage their bookings.

**Files**

*   /appointments/reschedule.php (POST, CSRF)
    
    *   Inputs: appointment\_id, new\_date, new\_time\_slot\_id.
        
    *   Checks:
        
        *   User owns it **or** admin.
            
        *   Current time is earlier than schedule\_start - APPOINTMENTS\_CUTOFF\_HOURS.
            
        *   Capacity for new slot is available.
            
    *   Update DB; if appointment was confirmed and has a google\_event\_id, call CalendarService::updateEvent().
        
    *   Send **reschedule** email+SMS; log to notification\_logs.
        
*   /appointments/cancel.php (POST, CSRF)
    
    *   Checks as above; set status='cancelled'.
        
    *   If google\_event\_id present → deleteEvent().
        
    *   Send **cancellation** email+SMS; log.
        

**Acceptance**

*   Owner can successfully move or cancel their future appointment with proper notifications.
    

10) Admin pages (reuse your /admin folder)
==========================================

**Prompt:**Back your existing admin pages with the new schema:

**Tasks**

*   admin/services/\* → CRUD against services.
    
*   admin/user/\* → list/search owners, view pets, deactivate (users.status=0).
    
*   admin/appointments/\* → table with filters by date/service/status. Actions: confirm/pending/cancel/reschedule (using the endpoints from step 9).
    
*   admin/system\_info/index.php → manage system\_info keys (clinic info, booking\_fee, appointments\_auto\_confirm, appointments\_cutoff\_hours, email/SMS creds, Google IDs).
    

**Acceptance**

*   Admin can manage core entities and settings without SQL.
    

11) Reminders (cron) T-48h / T-3h / T-1h
========================================

**Prompt:**Add a CLI script run by Windows Task Scheduler or cron (if on Linux).

**Files**

*   /bin/reminders.php
    
    *   Query appointments with status IN ('pending','confirmed') where NOW is within a small window around **48h**, **3h**, and **1h** before schedule\_date + start\_time (from time\_slots).
        
    *   For each due reminder → send Mail+SMS using templates; insert notification\_logs.
        
*   Document scheduling: e.g., Windows Task Scheduler run php C:\\xampp\\htdocs\\ovas\\bin\\reminders.php every 5 minutes.
    

**Acceptance**

*   Reminders appear in logs and arrive (email/SMS) at approximate offsets.
    

12) Security pass + Hardening
=============================

**Prompt:**Add/verify the following across the app:

*   CSRF token check on **every** POST endpoint (check\_availability.php, submit\_appointment.php, reschedule.php, cancel.php, auth.php, admin forms).
    
*   Escape all output with htmlspecialchars.
    
*   On login: store UA hash; on each request compare; if mismatch, logout.
    
*   Add a minimal Content-Security-Policy header in header.php (default-src 'self' data: https:; img-src 'self' data: https:).
    
*   Wrap **payment→appointment** creation with DB transactions (beginTransaction/commit/rollBack).
    
*   Enforce capacity rule server-side **even if** JS says available.
    

**Acceptance**

*   Manual smoke tests pass; no obvious injection points; form posts fail if CSRF missing.
    

Your .env.example to paste (tweak names to match your running MySQL)
--------------------------------------------------------------------

Plain textANTLR4BashCC#CSSCoffeeScriptCMakeDartDjangoDockerEJSErlangGitGoGraphQLGroovyHTMLJavaJavaScriptJSONJSXKotlinLaTeXLessLuaMakefileMarkdownMATLABMarkupObjective-CPerlPHPPowerShell.propertiesProtocol BuffersPythonRRubySass (Sass)Sass (Scss)SchemeSQLShellSwiftSVGTSXTypeScriptWebAssemblyYAMLXML`   # DB  DB_HOST=127.0.0.1  DB_PORT=3306  DB_NAME=ovas_db  DB_USER=root  DB_PASS=  # App  BASE_URL=http://localhost/ovas  JWT_SECRET=dev-only-secret  TIMEZONE=Africa/Nairobi  # Mail (PHPMailer)  MAIL_HOST=smtp.gmail.com  MAIL_PORT=587  MAIL_USERNAME=your_email_user  MAIL_PASSWORD=your_email_password  MAIL_FROM=clinic@example.com  # SMS (TextSMS or compatible)  TEXTSMS_API_KEY=your_textsms_api_key  TEXTSMS_PARTNER_ID=your_textsms_partner_id  TEXTSMS_SHORTCODE=your_textsms_shortcode  SMS_API_KEY=  SMS_USERNAME=  # Google Calendar  GOOGLE_CALENDAR_ID=  GOOGLE_CREDENTIALS_JSON_PATH=storage/google/credentials.json  # M-Pesa (Daraja sandbox)  CONSUMER_KEY=  CONSUMER_SECRET=  MPESA_SHORTCODE=  MPESA_PASSKEY=  CALLBACK_URL=http://localhost/ovas/payments/mpesa/callback.php  BOOKING_FEE=500  # Optional  WEATHER_API_KEY=  GMAIL_USER=  GMAIL_PASS=  GOOGLE_CLIENT_ID=  GOOGLE_CLIENT_SECRET=   `

Tiny vanilla JS hook (optional, for your existing /assets/js/ovas.js)
---------------------------------------------------------------------

If you want Copilot to wire AJAX:

**Prompt:**Add two functions to /assets/js/ovas.js:

Plain textANTLR4BashCC#CSSCoffeeScriptCMakeDartDjangoDockerEJSErlangGitGoGraphQLGroovyHTMLJavaJavaScriptJSONJSXKotlinLaTeXLessLuaMakefileMarkdownMATLABMarkupObjective-CPerlPHPPowerShell.propertiesProtocol BuffersPythonRRubySass (Sass)Sass (Scss)SchemeSQLShellSwiftSVGTSXTypeScriptWebAssemblyYAMLXML`   export async function loadAvailability(serviceId, date){    const form = new FormData();    form.append('service_id', serviceId);    form.append('date', date);    form.append('csrf', window.CSRF_TOKEN);    const res = await fetch('./check_availability.php', { method: 'POST', body: form, credentials: 'same-origin' });    return res.json();  }  export async function getServiceDetails(serviceId){    const res = await fetch('./get_service_details.php?service_id='+encodeURIComponent(serviceId), { credentials:'same-origin' });    return res.json();  }   `

Then call loadAvailability() on date change, and getServiceDetails() on service select to update fee/duration UI.