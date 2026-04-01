/* DrFuxx - Main JavaScript */

document.addEventListener('DOMContentLoaded', () => {

  // ===== Sticky header shadow on scroll =====
  const header = document.getElementById('header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 10);
    });
  }

  // ===== Mobile nav =====
  const menuBtn = document.getElementById('menuBtn');
  const mobileNav = document.getElementById('mobileNav');
  const closeNav = document.getElementById('closeNav');
  const overlay = document.getElementById('overlay');

  function openNav() {
    if (mobileNav) mobileNav.classList.add('open');
    if (overlay) overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeNavFn() {
    if (mobileNav) mobileNav.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (menuBtn) menuBtn.addEventListener('click', openNav);
  if (closeNav) closeNav.addEventListener('click', closeNavFn);
  if (overlay) overlay.addEventListener('click', closeNavFn);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeNavFn();
  });

  // ===== Hero keyword rotation =====
  const keywords = (function() {
    var el = document.getElementById('drfuxx-keywords-data');
    if (el && el.dataset.keywords) {
      return el.dataset.keywords.split(',').map(function(k){ return k.trim(); });
    }
    return ['Testosteron', 'Med. Cannabis', 'Haarausfall', 'Erektionshilfe'];
  })();

  const heroKeyword = document.getElementById('heroKeyword');
  let kwIndex = 0;

  if (heroKeyword) {
    function rotateKeyword() {
      heroKeyword.style.opacity = '0';
      heroKeyword.style.transform = 'translateY(10px)';
      setTimeout(() => {
        kwIndex = (kwIndex + 1) % keywords.length;
        heroKeyword.textContent = keywords[kwIndex];
        heroKeyword.style.opacity = '1';
        heroKeyword.style.transform = 'translateY(0)';
      }, 400);
    }

    heroKeyword.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    setInterval(rotateKeyword, 3000);
  }

  // ===== Smooth scroll for anchor links =====
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
      const href = anchor.getAttribute('href');
      if (href === '#') return;
      const target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        closeNavFn();
      }
    });
  });

  // ===== Scroll reveal animations =====
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  });

  const animElements = document.querySelectorAll(
    '.cat-card, .step-card, .stat-card, .feature-card, .rev-card, .t-card'
  );

  animElements.forEach((el, i) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(24px)';
    el.style.transition = 'opacity 0.6s ease ' + (i % 5) * 0.08 + 's, transform 0.6s ease ' + (i % 5) * 0.08 + 's';
    observer.observe(el);
  });

  // ===== Section title reveals =====
  const titleObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        titleObserver.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.section-title, .section-sub, .stats-intro, .trust-banner h2').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.7s ease, transform 0.7s ease';
    titleObserver.observe(el);
  });

  // ===== CONVERSION OPTIMIZATION =====

  // --- Countdown Timer ---
  (function() {
    var hEl = document.getElementById('cdHours');
    var mEl = document.getElementById('cdMins');
    var sEl = document.getElementById('cdSecs');
    if (!hEl || !mEl || !sEl) return;

    // End of today
    function getEndOfDay() {
      var now = new Date();
      var end = new Date(now);
      end.setHours(23, 59, 59, 999);
      return end;
    }

    function updateCountdown() {
      var now = new Date();
      var end = getEndOfDay();
      var diff = end - now;
      if (diff <= 0) { diff = 0; }
      var h = Math.floor(diff / 3600000);
      var m = Math.floor((diff % 3600000) / 60000);
      var s = Math.floor((diff % 60000) / 1000);
      hEl.textContent = h < 10 ? '0' + h : h;
      mEl.textContent = m < 10 ? '0' + m : m;
      sEl.textContent = s < 10 ? '0' + s : s;
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
  })();

  // --- Sticky Bottom CTA Bar (appears after scrolling 600px) ---
  (function() {
    var bar = document.getElementById('stickyCta');
    if (!bar) return;
    var shown = false;
    window.addEventListener('scroll', function() {
      if (window.scrollY > 600 && !shown) {
        bar.classList.add('show');
        shown = true;
      } else if (window.scrollY <= 600 && shown) {
        bar.classList.remove('show');
        shown = false;
      }
    });
  })();

  // --- Social Proof Toast Notifications ---
  (function() {
    var toast = document.getElementById('socialProofToast');
    var nameEl = document.getElementById('sptName');
    var actionEl = document.getElementById('sptAction');
    var timeEl = document.getElementById('sptTime');
    if (!toast || !nameEl || !actionEl || !timeEl) return;

    var el = document.getElementById('drfuxx-social-proof-data');
    var proofs = (el && el.dataset.proofs) ? JSON.parse(el.dataset.proofs) : [
      { name: 'Thomas aus Berlin', action: 'hat gerade eine Behandlung gestartet', time: 'vor 2 Minuten' },
      { name: 'Markus aus M\u00fcnchen', action: 'hat Med. Cannabis bestellt', time: 'vor 4 Minuten' },
      { name: 'Julia aus Hamburg', action: 'hat ein Rezept erhalten', time: 'vor 5 Minuten' },
      { name: 'Stefan aus K\u00f6ln', action: 'hat eine Beratung abgeschlossen', time: 'vor 7 Minuten' },
      { name: 'Sandra aus Wien', action: 'hat eine Behandlung gestartet', time: 'vor 8 Minuten' },
      { name: 'Michael aus Z\u00fcrich', action: 'hat Haarausfall-Behandlung bestellt', time: 'vor 10 Minuten' },
      { name: 'Anna aus Frankfurt', action: 'hat ein Rezept erhalten', time: 'vor 12 Minuten' },
      { name: 'David aus Stuttgart', action: 'hat gerade eine Beratung gestartet', time: 'vor 3 Minuten' }
    ];
    var idx = 0;

    function showProof() {
      var p = proofs[idx % proofs.length];
      nameEl.textContent = p.name;
      actionEl.textContent = p.action;
      timeEl.textContent = p.time;
      toast.classList.add('show');
      setTimeout(function() {
        toast.classList.remove('show');
      }, 4500);
      idx++;
    }

    // First toast after 8 seconds, then every 25 seconds
    setTimeout(function() {
      showProof();
      setInterval(showProof, 25000);
    }, 8000);
  })();

  // --- Live Users Counter (random fluctuation) ---
  (function() {
    var el = document.querySelector('.hero-live-users strong');
    if (!el) return;
    var base = (function() {
      var el = document.getElementById('drfuxx-keywords-data');
      return el && el.dataset.liveUsers ? parseInt(el.dataset.liveUsers) : 127;
    })();
    setInterval(function() {
      var change = Math.floor(Math.random() * 7) - 3;
      base = Math.max(95, Math.min(180, base + change));
      el.textContent = base + ' Personen';
    }, 5000);
  })();

});
