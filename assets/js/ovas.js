/**
 * OVAS - Main JavaScript File
 * Handles scroll-spy, animations, form validation, and interactions
 */

(function() {
  'use strict';

  // ============================================
  // Initialize AOS (Animate On Scroll)
  // ============================================
  if (typeof AOS !== 'undefined') {
    AOS.init({
      duration: 700,
      easing: 'ease-out-cubic',
      once: true,
      offset: 50,
      delay: 0,
    });
  }

  // ============================================
  // Initialize Swiper Hero Carousel
  // ============================================
  if (typeof Swiper !== 'undefined' && document.querySelector('.hero-swiper')) {
    const heroSwiper = new Swiper('.hero-swiper', {
      loop: true,
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
      },
      speed: 1000,
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
        dynamicBullets: false,
      },
      keyboard: {
        enabled: true,
        onlyInViewport: true,
      },
      a11y: {
        enabled: true,
        prevSlideMessage: 'Previous slide',
        nextSlideMessage: 'Next slide',
        paginationBulletMessage: 'Go to slide {{index}}',
      },
    });
  }

  // ============================================
  // Navbar Scroll Effects & ScrollSpy
  // ============================================
  const navbar = document.getElementById('mainNav');
  const navLinks = document.querySelectorAll('#mainNav .nav-link[href^="#"]');
  const sections = document.querySelectorAll('section[id]');

  // Add scrolled class to navbar
  function handleNavbarScroll() {
    if (window.scrollY > 10) {
      navbar?.classList.add('scrolled');
    } else {
      navbar?.classList.remove('scrolled');
    }
  }

  // IntersectionObserver for section highlighting
  const observerOptions = {
    root: null,
    rootMargin: '-50% 0px -50% 0px',
    threshold: 0,
  };

  const observerCallback = (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const sectionId = entry.target.getAttribute('id');
        
        // Remove active class from all links
        navLinks.forEach((link) => link.classList.remove('active'));
        
        // Add active class to current section link
        const activeLink = document.querySelector(`#mainNav .nav-link[href="#${sectionId}"]`);
        if (activeLink) {
          activeLink.classList.add('active');
        }
      }
    });
  };

  const sectionObserver = new IntersectionObserver(observerCallback, observerOptions);

  sections.forEach((section) => {
    sectionObserver.observe(section);
  });

  // Smooth scroll on nav link click
  navLinks.forEach((link) => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = link.getAttribute('href');
      const targetSection = document.querySelector(targetId);
      
      if (targetSection) {
        const offsetTop = targetSection.offsetTop - 80; // Account for fixed navbar
        window.scrollTo({
          top: offsetTop,
          behavior: 'smooth',
        });
      }
    });
  });

  // Handle scroll events
  window.addEventListener('scroll', handleNavbarScroll, { passive: true });
  handleNavbarScroll(); // Initial check

  // ============================================
  // Counter Animation (KPIs)
  // ============================================
  const counters = document.querySelectorAll('.counter');
  const counterSpeed = 200; // Lower is faster

  const countUp = (counter) => {
    const target = +counter.getAttribute('data-target');
    const count = +counter.innerText;
    const increment = target / counterSpeed;

    if (count < target) {
      counter.innerText = Math.ceil(count + increment);
      setTimeout(() => countUp(counter), 1);
    } else {
      // Handle decimals for ratings
      if (target % 1 !== 0) {
        counter.innerText = target.toFixed(1);
      } else {
        counter.innerText = target;
      }
    }
  };

  const counterObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && !entry.target.classList.contains('counted')) {
          entry.target.classList.add('counted');
          countUp(entry.target);
        }
      });
    },
    { threshold: 0.5 }
  );

  counters.forEach((counter) => {
    counterObserver.observe(counter);
  });

  // ============================================
  // Form Validation & Submission
  // ============================================
  
  // Bootstrap form validation
  const forms = document.querySelectorAll('.needs-validation');

  forms.forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      event.stopPropagation();

      if (form.checkValidity()) {
        // Form is valid, proceed with submission
        await handleFormSubmit(form);
      } else {
        // Show validation errors
        form.classList.add('was-validated');
      }
    }, false);
  });

  // Handle form submission
  async function handleFormSubmit(form) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const submitText = submitBtn.querySelector('.submit-text');
    const spinner = submitBtn.querySelector('.spinner-border');

    // Show loading state
    submitBtn.disabled = true;
    form.classList.add('loading');
    if (submitText) submitText.classList.add('d-none');
    if (spinner) spinner.classList.remove('d-none');

    try {
      const formData = new FormData(form);
      const action = form.getAttribute('action');
      
      const response = await fetch(action, {
        method: 'POST',
        body: formData,
      });

      const result = await response.json();

      if (result.status === 'success') {
        showToast('success', result.msg || 'Form submitted successfully!');
        form.reset();
        form.classList.remove('was-validated');
        
        // Redirect if specified
        if (result.redirect) {
          setTimeout(() => {
            window.location.href = result.redirect;
          }, 2000);
        }
      } else {
        showToast('error', result.msg || 'An error occurred. Please try again.');
      }
    } catch (error) {
      console.error('Form submission error:', error);
      showToast('error', 'An error occurred. Please try again.');
    } finally {
      // Reset loading state
      submitBtn.disabled = false;
      form.classList.remove('loading');
      if (submitText) submitText.classList.remove('d-none');
      if (spinner) spinner.classList.add('d-none');
    }
  }

  // ============================================
  // Toast Notifications
  // ============================================
  function showToast(type, message) {
    const toastElement = type === 'success' 
      ? document.getElementById('successToast')
      : document.getElementById('errorToast');
      
    const messageElement = type === 'success'
      ? document.getElementById('successMessage')
      : document.getElementById('errorMessage');

    if (toastElement && messageElement) {
      messageElement.textContent = message;
      
      if (typeof bootstrap !== 'undefined') {
        const toast = new bootstrap.Toast(toastElement, {
          autohide: true,
          delay: 5000,
        });
        toast.show();
      }
    }
  }

  // ============================================
  // Lazy Load Images Enhancement
  // ============================================
  function enhanceLazyImages() {
    const images = document.querySelectorAll('img:not([loading])');
    images.forEach((img) => {
      img.setAttribute('loading', 'lazy');
      img.setAttribute('decoding', 'async');
    });
  }

  // ============================================
  // Date Picker Minimum Date
  // ============================================
  const dateInput = document.getElementById('schedule');
  if (dateInput) {
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
  }

  // ============================================
  // Mobile Menu Toggle (if needed)
  // ============================================
  const navbarToggler = document.querySelector('.navbar-toggler');
  const navbarCollapse = document.querySelector('.navbar-collapse');

  if (navbarToggler && navbarCollapse) {
    navbarToggler.addEventListener('click', () => {
      navbarCollapse.classList.toggle('show');
    });

    // Close mobile menu when clicking on a link
    navLinks.forEach((link) => {
      link.addEventListener('click', () => {
        if (navbarCollapse.classList.contains('show')) {
          navbarCollapse.classList.remove('show');
        }
      });
    });
  }

  // ============================================
  // Service Card Click to Expand (Optional)
  // ============================================
  const serviceCards = document.querySelectorAll('.service-card');
  serviceCards.forEach((card) => {
    card.addEventListener('click', function() {
      // Optional: Add modal or expansion functionality
      console.log('Service card clicked:', this);
    });
  });

  // ============================================
  // Prefill Form Data (if user is logged in)
  // ============================================
  function setupFormPrefill() {
    const editToggles = document.querySelectorAll('.edit-toggle');
    
    editToggles.forEach((toggle) => {
      toggle.addEventListener('click', function() {
        const targetInput = document.getElementById(this.dataset.target);
        if (targetInput) {
          targetInput.removeAttribute('readonly');
          targetInput.focus();
          this.remove();
        }
      });
    });
  }

  // ============================================
  // Category/Service Dynamic Loading
  // ============================================
  const categorySelect = document.getElementById('category_id');
  const serviceSelect = document.getElementById('service_id');

  if (categorySelect && serviceSelect) {
    categorySelect.addEventListener('change', async function() {
      const categoryId = this.value;
      
      if (!categoryId) {
        serviceSelect.innerHTML = '<option value="" selected disabled>Choose...</option>';
        return;
      }

      try {
        // Fetch services for selected category
        const response = await fetch(`/get_services.php?category_id=${categoryId}`);
        const services = await response.json();

        if (services && services.length > 0) {
          serviceSelect.innerHTML = '<option value="" selected disabled>Choose...</option>';
          services.forEach((service) => {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = `${service.name} - ₱${parseFloat(service.fee).toFixed(2)}`;
            serviceSelect.appendChild(option);
          });
        } else {
          serviceSelect.innerHTML = '<option value="" selected disabled>No services available</option>';
        }
      } catch (error) {
        console.error('Error fetching services:', error);
        showToast('error', 'Failed to load services');
      }
    });
  }

  // ============================================
  // Check Appointment Availability
  // ============================================
  const scheduleInput = document.getElementById('schedule');
  
  if (scheduleInput) {
    scheduleInput.addEventListener('change', async function() {
      const selectedDate = this.value;
      
      if (!selectedDate) return;

      try {
        const response = await fetch(`/check_availability.php?date=${selectedDate}`);
        const data = await response.json();

        if (data.available_slots !== undefined && data.available_slots <= 0) {
          showToast('error', 'This date is fully booked. Please select another date.');
          this.value = '';
        } else if (data.available_slots < 5) {
          showToast('info', `Only ${data.available_slots} slots remaining for this date.`);
        }
      } catch (error) {
        console.error('Error checking availability:', error);
      }
    });
  }

  // ============================================
  // Handle Flash Messages from PHP
  // ============================================
  const urlParams = new URLSearchParams(window.location.search);
  const successMsg = urlParams.get('success');
  const errorMsg = urlParams.get('error');

  if (successMsg) {
    showToast('success', decodeURIComponent(successMsg));
    // Clean URL
    const cleanUrl = window.location.pathname;
    window.history.replaceState({}, document.title, cleanUrl);
  }

  if (errorMsg) {
    showToast('error', decodeURIComponent(errorMsg));
    // Clean URL
    const cleanUrl = window.location.pathname;
    window.history.replaceState({}, document.title, cleanUrl);
  }

  // ============================================
  // Initialize Everything on DOM Ready
  // ============================================
  document.addEventListener('DOMContentLoaded', () => {
    enhanceLazyImages();
    setupFormPrefill();
    
    console.log('🐾 OVAS - Veterinary Appointment System Loaded');
  });

  // ============================================
  // Service Worker Registration (Optional PWA)
  // ============================================
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      // navigator.serviceWorker.register('/service-worker.js')
      //   .then(registration => console.log('SW registered:', registration))
      //   .catch(error => console.log('SW registration failed:', error));
    });
  }

  // ============================================
  // Performance Monitoring (Optional)
  // ============================================
  window.addEventListener('load', () => {
    if ('performance' in window) {
      const perfData = window.performance.timing;
      const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
      console.log(`⚡ Page load time: ${pageLoadTime}ms`);
    }
  });

})();

// ============================================
// Utility Functions (Global Scope)
// ============================================

/**
 * Format currency
 */
function formatCurrency(amount) {
  return new Intl.NumberFormat('en-PH', {
    style: 'currency',
    currency: 'PHP',
  }).format(amount);
}

/**
 * Format date
 */
function formatDate(dateString) {
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return new Date(dateString).toLocaleDateString('en-US', options);
}

/**
 * Debounce function for performance
 */
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

/**
 * Throttle function for scroll events
 */
function throttle(func, limit) {
  let inThrottle;
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args);
      inThrottle = true;
      setTimeout(() => inThrottle = false, limit);
    }
  };
}
