# OVAS Homepage - Testing Guide

## 🧪 Quick Testing Checklist

### Pre-Testing Setup
1. ✅ Ensure XAMPP Apache and MySQL are running
2. ✅ Database `ovas_db` is populated with sample data
3. ✅ All new files are in place:
   - `index_new.php`
   - `libs/css/ovas.css`
   - `assets/js/ovas.js`
   - `inc/topBarNav_new.php`
   - `get_services.php`
   - `check_availability.php`
   - `submit_appointment.php`

---

## 📝 Test Scenarios

### 1. Homepage Load
**URL**: `http://localhost/ovas/index_new.php`

**Expected Results**:
- ✅ Page loads without errors (check browser console)
- ✅ Google Fonts (Inter/Roboto) are loaded
- ✅ Hero carousel displays with 3 slides
- ✅ Services grid shows 8 cards
- ✅ Appointment form is visible
- ✅ About section with team members
- ✅ Contact form at bottom

**Check Console for**:
```
🐾 OVAS - Veterinary Appointment System Loaded
⚡ Page load time: [X]ms
```

---

### 2. Navigation & ScrollSpy
**Actions**:
1. Click "Services" in navbar
2. Click "Book Appointment" in navbar
3. Click "About Us" in navbar
4. Click "Contact" in navbar

**Expected Results**:
- ✅ Smooth scroll to each section
- ✅ Nav link highlights as active
- ✅ Navbar becomes sticky with backdrop blur
- ✅ URL hash updates (#services, #appointment, etc.)

---

### 3. Hero Carousel
**Actions**:
1. Wait 5 seconds (autoplay interval)
2. Click pagination dots
3. Hover over carousel (autoplay should pause)

**Expected Results**:
- ✅ Slides auto-advance every 5 seconds
- ✅ Fade transition between slides
- ✅ Pagination dots are clickable
- ✅ Keyboard arrows work (left/right)
- ✅ Autoplay pauses on hover

---

### 4. Services Grid
**Actions**:
1. Scroll down to services section
2. Hover over service cards

**Expected Results**:
- ✅ Cards fade in with AOS animation
- ✅ Card lifts on hover (translateY -8px)
- ✅ Shadow increases on hover
- ✅ Service icons are visible
- ✅ Prices are formatted correctly

---

### 5. Appointment Form - Category/Service Loading
**Actions**:
1. Select a category from dropdown
2. Observe service dropdown

**Expected Results**:
- ✅ Services load dynamically via AJAX
- ✅ Service dropdown populates with matching services
- ✅ Service prices display correctly (₱X.XX format)
- ✅ "No services available" shows if category is empty

**Test in Browser Console**:
```javascript
// Check AJAX endpoint
fetch('http://localhost/ovas/get_services.php?category_id=1')
  .then(r => r.json())
  .then(data => console.log(data));
```

---

### 6. Appointment Form - Date Availability
**Actions**:
1. Select a past date
2. Select today's date
3. Select a Sunday
4. Select a future weekday

**Expected Results**:
- ✅ Past date shows error toast: "Cannot book for past dates"
- ✅ Today's date checks availability
- ✅ Sunday shows warning: "Clinic is closed on Sundays"
- ✅ Future weekday shows available slots (if < 5, warning toast)

**Test in Browser Console**:
```javascript
// Check availability endpoint
fetch('http://localhost/ovas/check_availability.php?date=2024-01-15')
  .then(r => r.json())
  .then(data => console.log(data));
```

---

### 7. Appointment Form - Validation
**Actions**:
1. Submit empty form
2. Enter invalid email
3. Fill all fields and submit

**Expected Results**:
- ✅ Required fields show validation errors (red border)
- ✅ Email validation triggers on invalid format
- ✅ Form shows "was-validated" class
- ✅ Error messages display below fields

---

### 8. Appointment Form - AJAX Submission
**Actions**:
1. Fill all required fields
2. Click "Book Appointment" button
3. Observe loading state

**Expected Results**:
- ✅ Button shows loading spinner
- ✅ Button text changes to "Booking..."
- ✅ Form is disabled during submission
- ✅ Success toast appears: "Appointment booked successfully!"
- ✅ Form resets after success
- ✅ Error toast appears if submission fails

**Test Endpoint Directly**:
```bash
curl -X POST http://localhost/ovas/submit_appointment.php \
  -d "owner_name=Test User" \
  -d "contact=1234567890" \
  -d "email=test@example.com" \
  -d "pet_name=Fluffy" \
  -d "category_id=1" \
  -d "service_id=1" \
  -d "schedule=2024-12-25" \
  -d "csrf_token=test"
```

---

### 9. Counter Animation (KPIs)
**Actions**:
1. Scroll to About section
2. Watch the counter numbers

**Expected Results**:
- ✅ Numbers animate from 0 to target value
- ✅ Animation triggers when section is 50% visible
- ✅ Animation only happens once
- ✅ Decimal values display correctly (e.g., 4.9 for rating)

---

### 10. Mobile Responsiveness
**Actions**:
1. Open DevTools (F12)
2. Toggle device toolbar (Ctrl+Shift+M)
3. Test on iPhone 12 Pro, iPad, Pixel 5

**Expected Results**:
- ✅ Hamburger menu appears on mobile
- ✅ Navigation collapses properly
- ✅ Cards stack vertically
- ✅ Text is readable (no overflow)
- ✅ Forms are usable
- ✅ Buttons are touch-friendly (min 44px height)

---

### 11. User Authentication States
**Test A: Logged Out**:
- ✅ Navbar shows "Admin Login" and "Book Now" buttons
- ✅ Form fields are empty (no prefill)

**Test B: Logged In as Client**:
1. Log in at `/admin/login.php`
2. Return to homepage

**Expected Results**:
- ✅ Navbar shows user avatar and name
- ✅ Dropdown menu appears with "My Profile", "My Appointments", "Logout"
- ✅ Form fields prefill with user data
- ✅ Edit icons appear next to prefilled fields

---

### 12. Accessibility Testing
**Keyboard Navigation**:
1. Press Tab key repeatedly
2. Use Enter/Space on focusable elements

**Expected Results**:
- ✅ All interactive elements are reachable
- ✅ Focus outline is visible (blue glow)
- ✅ Skip links work (if implemented)
- ✅ Forms are keyboard-submittable

**Screen Reader**:
1. Open NVDA or JAWS
2. Navigate page with screen reader

**Expected Results**:
- ✅ Sections have proper headings
- ✅ Form labels are announced
- ✅ Images have alt text
- ✅ Buttons describe their action

---

### 13. Browser Compatibility
**Test on**:
- Chrome (latest)
- Firefox (latest)
- Edge (latest)
- Safari (if available)

**Check**:
- ✅ Layout renders correctly
- ✅ Animations work smoothly
- ✅ Backdrop filter works (Safari needs -webkit-)
- ✅ No console errors
- ✅ Forms submit successfully

---

### 14. Performance Testing
**Lighthouse Audit**:
1. Open DevTools > Lighthouse tab
2. Run audit for Desktop/Mobile

**Target Scores**:
- 🎯 Performance: > 90
- 🎯 Accessibility: > 95
- 🎯 Best Practices: > 90
- 🎯 SEO: > 90

**Check Network Tab**:
- ✅ Total page size < 3MB
- ✅ No blocking resources
- ✅ Images are lazy-loaded
- ✅ Fonts load with swap

---

## 🐛 Common Issues & Fixes

### Issue: "AOS is not defined"
**Solution**: Check if AOS CDN loaded correctly in `inc/header.php`
```html
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
```

### Issue: "Swiper is not defined"
**Solution**: Verify Swiper CDN in header
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
```

### Issue: Services dropdown not loading
**Solution**: Check `get_services.php` endpoint
1. Test URL directly: `http://localhost/ovas/get_services.php?category_id=1`
2. Check if database has services with `category_id = 1`
3. Verify `delete_flag = 0` and `status = 1` in database

### Issue: Form submission fails
**Solution**: Check PHP error log
1. Enable error reporting in `config.php`:
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```
2. Check database connection in `submit_appointment.php`
3. Verify CSRF token generation in session

### Issue: Navbar not sticky
**Solution**: Check Bootstrap version
1. Ensure Bootstrap 5 is loaded (not Bootstrap 4)
2. Verify `#mainNav` ID exists in `inc/topBarNav_new.php`
3. Check CSS for `position: fixed` in `ovas.css`

---

## 📊 Testing Report Template

```markdown
## Test Report - OVAS Homepage

**Date**: [DATE]
**Tester**: [NAME]
**Browser**: [Chrome/Firefox/Safari] v[VERSION]
**Device**: [Desktop/Mobile/Tablet]

### Results:
- [ ] Homepage loads successfully
- [ ] Navigation & ScrollSpy working
- [ ] Hero carousel functional
- [ ] Services grid displays
- [ ] Appointment form validation
- [ ] AJAX submission successful
- [ ] Counter animation triggers
- [ ] Mobile responsive
- [ ] No console errors
- [ ] Accessibility compliant

### Issues Found:
1. [Description]
2. [Description]

### Screenshots:
[Attach screenshots of any issues]
```

---

## 🚀 Deployment Checklist

Before going live:
- [ ] Replace `index.php` with `index_new.php`
- [ ] Replace `inc/topBarNav.php` with `inc/topBarNav_new.php`
- [ ] Upload all image assets to `uploads/assets/`
- [ ] Minify CSS (`libs/css/ovas.css`)
- [ ] Minify JavaScript (`assets/js/ovas.js`)
- [ ] Test on production server
- [ ] Enable HTTPS
- [ ] Configure CSP headers
- [ ] Set up email notifications
- [ ] Test with real appointment data
- [ ] Create database backup

---

**Testing Priority**: 🔥 HIGH
**Estimated Testing Time**: 2-3 hours
**Status**: Ready for QA
