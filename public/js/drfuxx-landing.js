// Disable the image editor overlay from main.js
document.addEventListener('DOMContentLoaded', function() {
  // Remove any img-edit-wrap overlays after main.js runs
  setTimeout(function() {
    document.querySelectorAll('.img-edit-overlay, .img-edit-badge').forEach(function(el) {
      el.remove();
    });
    document.querySelectorAll('.img-edit-wrap').forEach(function(wrap) {
      var img = wrap.querySelector('img');
      if (img) {
        wrap.parentNode.insertBefore(img, wrap);
        wrap.remove();
      }
    });
  }, 100);
});

// Inject background images for mobile cat cards
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.mhn-cat-item').forEach(function(card) {
    var bg = card.getAttribute('data-bg');
    if (bg) {
      var bgDiv = card.querySelector('.mhn-cat-bg');
      if (bgDiv) bgDiv.style.backgroundImage = "url('" + bg + "')";
    }
  });
});

// Hero cards — auto-rotate + tap effect (both mobile & desktop)
document.addEventListener('DOMContentLoaded', function() {
  // === MOBILE list cards ===
  var mCards = document.querySelectorAll('.mhn-cat-item');
  var mKeyword = document.getElementById('mhnKeyword');
  // === DESKTOP tcat cards ===
  var dCards = document.querySelectorAll('.desktop-only-cats .tcat');
  var dKeyword = document.getElementById('heroKeyword');

  var keywords = ['MED. CANNABIS', 'EREKTIONSSTÖRUNGEN', 'TESTOSTERON'];
  var idx = 0;

  function activate(i) {
    idx = i;
    // Mobile
    if (mCards.length) {
      mCards.forEach(function(c) { c.classList.remove('active'); });
      mCards[i].classList.add('active');
    }
    if (mKeyword) {
      mKeyword.style.opacity = '0';
      setTimeout(function() { mKeyword.textContent = keywords[i]; mKeyword.style.opacity = '1'; }, 150);
    }
    // Desktop
    if (dCards.length) {
      dCards.forEach(function(c) { c.classList.remove('tcat-active'); });
      dCards[i].classList.add('tcat-active');
    }
    if (dKeyword) {
      dKeyword.style.transition = 'opacity 0.2s';
      dKeyword.style.opacity = '0';
      setTimeout(function() { dKeyword.textContent = keywords[i]; dKeyword.style.opacity = '1'; }, 150);
    }
  }

  activate(0);

  // Mobile tap
  mCards.forEach(function(card, i) {
    card.addEventListener('click', function(e) {
      e.preventDefault();
      activate(i);
      var link = card.getAttribute('href');
      // Update CTA button link
      var cta = document.getElementById('mhnCta');
      if (cta) cta.href = link;
      setTimeout(function() { window.location.href = link; }, 400);
    });
  });

  // Desktop tap
  dCards.forEach(function(card, i) {
    card.addEventListener('mouseenter', function() {
      activate(i);
    });
  });

});

// Category card tap/click toggle (works on both mobile & desktop)
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.cat-card-v2').forEach(function(card) {
    card.addEventListener('click', function() {
      document.querySelectorAll('.cat-card-v2').forEach(function(c) {
        if (c !== card) c.classList.remove('tapped');
      });
      card.classList.toggle('tapped');
    });
  });
});

// tcat: init background + tap toggle
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.tcat').forEach(function(card) {
    // Inject bg div from data-bg attribute
    var bg = card.getAttribute('data-bg');
    if (bg) {
      var bgDiv = document.createElement('div');
      bgDiv.className = 'tcat-bg';
      bgDiv.style.backgroundImage = "url('" + bg + "')";
      card.insertBefore(bgDiv, card.firstChild);
    }
    // Tap toggle
    card.addEventListener('click', function() {
      document.querySelectorAll('.tcat').forEach(function(c) {
        if (c !== card) c.classList.remove('tapped');
      });
      card.classList.toggle('tapped');
    });
  });
});

// Step card image toggle on mobile tap
document.addEventListener('DOMContentLoaded', function() {
  var stepCards = document.querySelectorAll('.step-card-tilted');
  stepCards.forEach(function(card) {
    card.addEventListener('click', function() {
      var def = card.querySelector('.step-img-default');
      var hov = card.querySelector('.step-img-hover');
      if (!def || !hov) return;
      var isSwapped = card.classList.contains('swapped');
      if (isSwapped) {
        def.style.opacity = '1';
        def.style.transform = 'scale(1)';
        hov.style.opacity = '0';
        hov.style.transform = 'scale(1.06)';
        card.classList.remove('swapped');
      } else {
        def.style.opacity = '0';
        def.style.transform = 'scale(0.95)';
        hov.style.opacity = '1';
        hov.style.transform = 'scale(1)';
        card.classList.add('swapped');
      }
    });
  });
});

// Steps swipe carousel
document.addEventListener('DOMContentLoaded', function() {
  var grid = document.querySelector('.steps-grid-tilted');
  var dots = document.querySelectorAll('.steps-dot');
  if (!grid) return;

  // Force reset to step 1 — immediate + after render
  function resetScroll() {
    grid.style.scrollBehavior = 'auto';
    grid.scrollLeft = 0;
  }
  resetScroll();
  setTimeout(resetScroll, 50);
  setTimeout(resetScroll, 200);

  if (!dots.length) return;
  grid.addEventListener('scroll', function() {
    var cards = grid.querySelectorAll('.step-card-tilted');
    if (!cards.length) return;
    var cardWidth = cards[0].offsetWidth + 14;
    var index = Math.min(Math.round(grid.scrollLeft / cardWidth), dots.length - 1);
    dots.forEach(function(d, i) { d.classList.toggle('active', i === index); });
  }, { passive: true });
});

// FAQ Accordion
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.faq-q').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var item = btn.closest('.faq-item');
      var isOpen = item.classList.contains('active');
      document.querySelectorAll('.faq-item').forEach(function(i) {
        i.classList.remove('active');
        i.querySelector('.faq-icon').innerHTML = '&#8964;';
      });
      if (!isOpen) {
        item.classList.add('active');
        btn.querySelector('.faq-icon').innerHTML = '&#8963;';
      }
    });
  });
});

// ── Step Book — Page-Turn Swiper (fixed state machine) ──
document.addEventListener('DOMContentLoaded', function() {
  var book = document.getElementById('stepBook');
  if (!book) return;

  var pages   = Array.from(book.querySelectorAll('.sbook-page'));
  var dots    = Array.from(document.querySelectorAll('#sbookDots .sbook-dot'));
  var hint    = document.getElementById('sbookHint');
  var current = 0;
  var busy    = false;

  /*
   * turned[i] = true  →  page i has been flipped away (hidden, stays rotated)
   * turned[i] = false →  page i is visible (either active or stacked below)
   */
  var turned = pages.map(function() { return false; });

  /* Apply correct styles for every page based on state */
  function applyLayout() {
    /* Remove animation classes first */
    pages.forEach(function(p) {
      p.classList.remove('turning-out', 'turning-back', 'rising-in');
    });

    pages.forEach(function(p, i) {
      if (turned[i]) {
        /* Permanently flipped — invisible behind active card */
        p.style.transition = 'none';
        p.style.transform  = 'rotateY(-100deg)';
        p.style.zIndex     = '1';
      } else if (i === current) {
        /* Active card — on top, no rotation */
        p.style.transition = '';
        p.style.transform  = '';
        p.style.zIndex     = '30';
      } else {
        /* Waiting below — stack peek effect */
        var depth = i - current;          /* 1, 2, … cards behind */
        p.style.transition = 'none';
        p.style.transform  = 'translateX(' + (depth * 5) + 'px) translateY(' + (depth * 5) + 'px) scale(' + (1 - depth * 0.025) + ')';
        p.style.zIndex     = String(20 - depth);
      }
    });
  }

  applyLayout();

  function updateDots() {
    dots.forEach(function(d, i) { d.classList.toggle('active', i === current); });
  }

  function goTo(next) {
    if (busy || next === current || next < 0 || next >= pages.length) return;
    busy = true;

    var prev    = current;
    var forward = next > prev;

    if (forward) {
      /* Flip current card away */
      pages[prev].style.transition = '';
      pages[prev].style.zIndex     = '50';
      pages[prev].classList.add('turning-out');

      /* Rise the next card into position */
      pages[next].style.transition = '';
      pages[next].style.zIndex     = '40';
      pages[next].classList.add('rising-in');

      turned[prev] = true;   /* mark as permanently turned */

    } else {
      /* Bring back the previously-turned card */
      /* Start it from turned position without transition, then animate back */
      pages[next].style.transition = 'none';
      pages[next].style.transform  = 'rotateY(-100deg)';
      pages[next].style.zIndex     = '50';
      void pages[next].offsetWidth; /* force reflow so starting state sticks */
      pages[next].style.transition = '';
      pages[next].classList.add('turning-back');

      turned[next] = false;  /* un-turn it */
    }

    current = next;
    updateDots();
    if (hint) hint.classList.add('hidden');

    /* After animation finishes, lock in final state */
    setTimeout(function() {
      applyLayout();
      busy = false;
    }, 660);
  }

  /* ── Touch swipe ── */
  var tx = 0, ty = 0;
  book.addEventListener('touchstart', function(e) {
    tx = e.touches[0].clientX;
    ty = e.touches[0].clientY;
  }, { passive: true });

  book.addEventListener('touchend', function(e) {
    var dx = e.changedTouches[0].clientX - tx;
    var dy = e.changedTouches[0].clientY - ty;
    /* Ignore if mostly vertical (scrolling) or too short */
    if (Math.abs(dx) < 35 || Math.abs(dy) > Math.abs(dx)) return;
    if (dx < 0) goTo(current + 1);
    else        goTo(current - 1);
  }, { passive: true });

  /* ── Tap left / right half ── */
  book.addEventListener('click', function(e) {
    var rect = book.getBoundingClientRect();
    if (e.clientX > rect.left + rect.width / 2) goTo(current + 1);
    else goTo(current - 1);
  });
});

// Doctor grid swipe dots
document.addEventListener('DOMContentLoaded', function() {
  var grid = document.querySelector('.doc-grid');
  var dots = document.querySelectorAll('#docDots .doc-dot');
  if (!grid || !dots.length) return;
  grid.addEventListener('scroll', function() {
    var cards = grid.querySelectorAll('.doc-card');
    if (!cards.length) return;
    var cardW = cards[0].offsetWidth + 12;
    var idx = Math.min(Math.round(grid.scrollLeft / cardW), dots.length - 1);
    dots.forEach(function(d, i) { d.classList.toggle('active', i === idx); });
  }, { passive: true });
});

// Advisory Board Slider — featured + peek effect
document.addEventListener('DOMContentLoaded', function() {
  var slider = document.getElementById('advisorySlider');
  var prevBtn = document.getElementById('advPrev');
  var nextBtn = document.getElementById('advNext');
  if (!slider || !prevBtn || !nextBtn) return;
  var cards = Array.from(slider.children);
  var activeIndex = 0;
  var peekCount = 2;

  function updateCards() {
    cards.forEach(function(card, i) {
      card.classList.remove('adv-active', 'adv-peek');
      if (i === activeIndex) {
        card.classList.add('adv-active');
      } else if (i > activeIndex && i <= activeIndex + peekCount) {
        card.classList.add('adv-peek');
      }
    });
    prevBtn.style.opacity = activeIndex === 0 ? '0.35' : '1';
    nextBtn.style.opacity = activeIndex >= cards.length - 1 ? '0.35' : '1';
  }

  nextBtn.addEventListener('click', function() {
    if (activeIndex < cards.length - 1) { activeIndex++; updateCards(); }
  });
  prevBtn.addEventListener('click', function() {
    if (activeIndex > 0) { activeIndex--; updateCards(); }
  });

  updateCards();
});

// Also neutralise background-image edit buttons added after load
var _origMutObs = window.MutationObserver;
document.addEventListener('DOMContentLoaded', function() {
  var observer = new MutationObserver(function() {
    document.querySelectorAll('.img-edit-overlay, .img-edit-badge').forEach(function(el) { el.remove(); });
  });
  observer.observe(document.body, { childList: true, subtree: true });
});
