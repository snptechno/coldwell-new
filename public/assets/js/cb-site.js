/*
 * Coldwell Banker India redesign: nav dropdown + drawer, hero slider, scroll-snap carousels,
 * expertise photo stack, reveal on scroll. Vanilla JS, no dependencies.
 * Respects prefers-reduced-motion (no autoplay, no smooth scroll).
 */
(function () {
  'use strict';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var pad = function (n) { return (n < 10 ? '0' : '') + n; };

  /* ---------- nav dropdowns ---------- */
  document.querySelectorAll('.cb-menu__btn').forEach(function (btn) {
    var sub = document.getElementById(btn.getAttribute('aria-controls'));
    var set = function (open) { btn.setAttribute('aria-expanded', open ? 'true' : 'false'); sub.classList.toggle('is-open', open); };
    btn.addEventListener('click', function (e) { e.stopPropagation(); set(btn.getAttribute('aria-expanded') !== 'true'); });
    sub.addEventListener('keydown', function (e) { if (e.key === 'Escape') { set(false); btn.focus(); } });
    btn.addEventListener('keydown', function (e) { if (e.key === 'Escape') set(false); });
    document.addEventListener('click', function (e) { if (!sub.contains(e.target)) set(false); });
    sub.addEventListener('focusout', function (e) { if (!sub.contains(e.relatedTarget) && e.relatedTarget !== btn) set(false); });
  });

  /* ---------- mobile drawer ---------- */
  var drawer = document.getElementById('cb-drawer');
  var burger = document.querySelector('.cb-burger');
  if (drawer && burger) {
    var lastFocus = null;
    var focusables = function () { return drawer.querySelectorAll('a[href],button:not([disabled])'); };
    var open = function () {
      lastFocus = document.activeElement;
      drawer.hidden = false;
      burger.setAttribute('aria-expanded', 'true');
      document.body.classList.add('cb-lock');
      void drawer.offsetWidth; // commit the closed state so the open transition runs (no rAF: it can be paused)
      drawer.classList.add('is-open');
      var f = focusables(); if (f.length) f[f.length > 1 ? 1 : 0].focus();
    };
    var close = function () {
      drawer.classList.remove('is-open');
      burger.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('cb-lock');
      setTimeout(function () { drawer.hidden = true; }, reduce ? 0 : 260);
      if (lastFocus) lastFocus.focus();
    };
    burger.addEventListener('click', open);
    drawer.querySelector('[data-drawer-close]').addEventListener('click', close);
    drawer.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') { close(); return; }
      if (e.key !== 'Tab') return;
      var f = focusables(), first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });
    window.addEventListener('resize', function () { if (window.innerWidth >= 1200 && !drawer.hidden) close(); });
  }

  /* ---------- hero slider (fade, 6s autoplay, pausable) ---------- */
  var hero = document.querySelector('[data-hero]');
  if (hero) {
    var bgs = hero.querySelectorAll('.cb-slide');
    var texts = hero.querySelectorAll('.cb-hero__text');
    var bars = hero.querySelectorAll('.cb-dots__bar');
    var num = hero.querySelector('.cb-dots__num');
    var toggle = hero.querySelector('[data-hero-toggle]');
    var n = texts.length, cur = 0, timer = null;
    var userPaused = reduce, hoverPaused = false;
    var show = function (i) {
      cur = (i + n) % n;
      for (var k = 0; k < n; k++) {
        var on = k === cur;
        bgs[k] && bgs[k].classList.toggle('is-active', on);
        texts[k].classList.toggle('is-active', on);
        texts[k].setAttribute('aria-hidden', on ? 'false' : 'true');
        texts[k].querySelectorAll('a,button').forEach(function (el) { el.tabIndex = on ? 0 : -1; });
        if (bars[k]) { if (on) bars[k].setAttribute('aria-current', 'true'); else bars[k].removeAttribute('aria-current'); }
      }
      if (num) num.textContent = pad(cur + 1);
    };
    var stop = function () { clearInterval(timer); timer = null; };
    var start = function () { stop(); if (n > 1 && !userPaused && !hoverPaused && !document.hidden) timer = setInterval(function () { show(cur + 1); }, 6000); };
    var setToggle = function () {
      if (!toggle) return;
      toggle.setAttribute('aria-pressed', userPaused ? 'true' : 'false');
      toggle.setAttribute('aria-label', userPaused ? 'Play slideshow' : 'Pause slideshow');
      toggle.innerHTML = userPaused
        ? '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M5 3.2v9.6L13 8z" fill="currentColor"/></svg>'
        : '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><rect x="4" y="3" width="2.6" height="10" rx="1" fill="currentColor"/><rect x="9.4" y="3" width="2.6" height="10" rx="1" fill="currentColor"/></svg>';
    };
    if (n > 1) {
      show(0);
      bars.forEach(function (b) { b.addEventListener('click', function () { show(+b.dataset.go); start(); }); });
      if (toggle) toggle.addEventListener('click', function () { userPaused = !userPaused; setToggle(); start(); });
      hero.addEventListener('mouseenter', function () { hoverPaused = true; stop(); });
      hero.addEventListener('mouseleave', function () { hoverPaused = false; start(); });
      hero.addEventListener('focusin', function () { hoverPaused = true; stop(); });
      hero.addEventListener('focusout', function (e) { if (!hero.contains(e.relatedTarget)) { hoverPaused = false; start(); } });
      document.addEventListener('visibilitychange', start);
      // swipe on touch screens
      var x0 = null;
      hero.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; }, { passive: true });
      hero.addEventListener('touchend', function (e) {
        if (x0 === null) return; var dx = e.changedTouches[0].clientX - x0; x0 = null;
        if (Math.abs(dx) > 50) { show(cur + (dx < 0 ? 1 : -1)); start(); }
      });
      setToggle(); start();
    }
  }

  /* ---------- scroll-snap carousels ---------- */
  document.querySelectorAll('[data-carousel]').forEach(function (root) {
    var track = root.querySelector('[data-track]');
    var ctl = root.querySelector('[data-controls]');
    if (!track) return;
    var cards = Array.prototype.slice.call(track.children);
    var prev = ctl && ctl.querySelector('[data-prev]');
    var next = ctl && ctl.querySelector('[data-next]');
    var curEl = ctl && ctl.querySelector('[data-current]');
    var bar = ctl && ctl.querySelector('[data-bar]');
    var live = ctl && ctl.querySelector('[data-live]');
    var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
    var label = (track.getAttribute('aria-label') || '').toLowerCase();
    var last = -1;
    var picked = null; // tab chosen by click/keyboard; wins while the track is pinned at its end

    var step = function () { return cards.length > 1 ? cards[1].offsetLeft - cards[0].offsetLeft : track.clientWidth; };
    var index = function () { return Math.max(0, Math.min(cards.length - 1, Math.round(track.scrollLeft / step()))); };
    var visible = function () { return Math.max(1, Math.floor((track.clientWidth + 8) / step())); };
    var goTo = function (i) {
      i = Math.max(0, Math.min(cards.length - 1, i));
      track.scrollTo({ left: cards[i].offsetLeft - cards[0].offsetLeft, behavior: reduce ? 'auto' : 'smooth' });
    };
    var update = function () {
      var max = track.scrollWidth - track.clientWidth;
      var fits = max <= 4;
      if (ctl) ctl.hidden = fits;
      var i = index(), atEnd = track.scrollLeft >= max - 4;
      if (atEnd) i = Math.max(i, cards.length - visible());
      if (curEl) curEl.textContent = pad(i + 1);
      if (bar) {
        var w = 100 * Math.min(1, visible() / cards.length);
        bar.style.width = w + '%';
        bar.style.transform = 'translateX(' + (max > 0 ? (track.scrollLeft / max) * (100 / w * 100 - 100) : 0) + '%)';
      }
      if (prev) prev.setAttribute('aria-disabled', track.scrollLeft <= 4 ? 'true' : 'false');
      if (next) next.setAttribute('aria-disabled', atEnd ? 'true' : 'false');
      cards.forEach(function (c) {
        var inView = c.offsetLeft - track.scrollLeft - cards[0].offsetLeft > -c.offsetWidth / 2 &&
          c.offsetLeft - track.scrollLeft - cards[0].offsetLeft < track.clientWidth - c.offsetWidth / 2;
        if ('inert' in c) c.inert = !inView && !fits;
      });
      var t = picked !== null ? picked : (atEnd && !fits ? i : index());
      if (tabs.length) {
        tabs.forEach(function (tab, k) {
          var on = k === t;
          tab.setAttribute('aria-selected', on ? 'true' : 'false');
          tab.tabIndex = on ? 0 : -1;
        });
      }
      var shown = tabs.length ? t : i;
      if (live && shown !== last && last !== -1) {
        var name = tabs[shown] ? tabs[shown].textContent.trim() + ', ' : '';
        live.textContent = name + (shown + 1) + ' of ' + cards.length + (label ? ' ' + label : '');
      }
      last = shown;
    };
    var unpick = function () { picked = null; };
    ['pointerdown', 'wheel', 'touchstart'].forEach(function (ev) { track.addEventListener(ev, unpick, { passive: true }); });
    if (prev) prev.addEventListener('click', function () { picked = null; if (prev.getAttribute('aria-disabled') !== 'true') goTo(index() - 1); });
    if (next) next.addEventListener('click', function () { picked = null; if (next.getAttribute('aria-disabled') !== 'true') goTo(index() + 1); });
    tabs.forEach(function (tab, k) {
      tab.addEventListener('click', function () { picked = +tab.dataset.go; goTo(picked); update(); });
      tab.addEventListener('keydown', function (e) {
        var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : e.key === 'Home' ? -k : e.key === 'End' ? tabs.length - 1 - k : 0;
        if (!d) return; e.preventDefault();
        var t = tabs[(k + d + tabs.length) % tabs.length]; t.focus(); t.click();
      });
    });
    var raf = 0;
    track.addEventListener('scroll', function () { cancelAnimationFrame(raf); raf = requestAnimationFrame(update); }, { passive: true });
    window.addEventListener('resize', update);
    update();
  });

  /* ---------- expertise stack: bring a photo to the front ---------- */
  document.querySelectorAll('[data-stack]').forEach(function (stack) {
    var imgs = stack.querySelectorAll('.cb-stack__img');
    var dots = stack.querySelectorAll('.cb-stack__dots button');
    dots.forEach(function (d) {
      d.addEventListener('click', function () {
        var i = +d.dataset.go;
        imgs.forEach(function (im, k) { im.style.zIndex = k === i ? 3 : (k < i ? 1 : 2); });
        dots.forEach(function (o) { if (o === d) o.setAttribute('aria-current', 'true'); else o.removeAttribute('aria-current'); });
      });
    });
  });

  /* ---------- franchise office finder (tabs + city/area selects) ---------- */
  document.querySelectorAll('[data-finder]').forEach(function (root) {
    var items = Array.prototype.slice.call(root.querySelectorAll('[data-finder-list] > li'));
    var tabs = Array.prototype.slice.call(root.querySelectorAll('.cb-tab[data-city]'));
    var citySel = root.querySelector('[data-finder-city]');
    var areaSel = root.querySelector('[data-finder-area]');
    var form = root.querySelector('[data-finder-form]');
    var count = root.querySelector('[data-finder-count]');
    if (!items.length) return;
    var areaOpts = areaSel ? Array.prototype.slice.call(areaSel.options).slice(1) : [];
    var apply = function (city, id) {
      var n = 0;
      items.forEach(function (li) {
        var show = id ? li.getAttribute('data-id') === id : (!city || li.getAttribute('data-city') === city);
        li.hidden = !show; if (show) n++;
      });
      tabs.forEach(function (t) {
        var on = t.getAttribute('data-city') === (city || '');
        t.setAttribute('aria-selected', on ? 'true' : 'false'); t.tabIndex = on ? 0 : -1;
      });
      if (citySel) citySel.value = city || '';
      areaOpts.forEach(function (o) { o.hidden = !!city && o.getAttribute('data-city') !== city; });
      if (count) count.textContent = n + (n === 1 ? ' office' : ' offices') + (city ? ' in ' + city : '');
    };
    tabs.forEach(function (t, k) {
      t.addEventListener('click', function () { if (areaSel) areaSel.value = ''; apply(t.getAttribute('data-city')); });
      t.addEventListener('keydown', function (e) {
        var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0; if (!d) return;
        e.preventDefault(); var n = tabs[(k + d + tabs.length) % tabs.length]; n.focus(); n.click();
      });
    });
    if (citySel) citySel.addEventListener('change', function () { if (areaSel) areaSel.value = ''; apply(citySel.value); });
    if (form) form.addEventListener('submit', function (e) {
      e.preventDefault();
      var id = areaSel && areaSel.value;
      if (id) {
        var opt = areaSel.options[areaSel.selectedIndex];
        apply(opt.getAttribute('data-city'), id);
        var hit = root.querySelector('[data-finder-list] > li[data-id="' + id + '"] a');
        if (hit) hit.focus({ preventScroll: true });
      } else { apply(citySel ? citySel.value : ''); }
    });
    apply(root.getAttribute('data-default') || '');
  });

  /* ---------- news feed: filter tabs + Load More ---------- */
  document.querySelectorAll('[data-feed]').forEach(function (root) {
    var page = parseInt(root.getAttribute('data-page'), 10) || 6;
    var items = Array.prototype.slice.call(root.querySelectorAll('[data-feed-list] > li'));
    var tabs = Array.prototype.slice.call(root.querySelectorAll('.cb-tab[data-cat]'));
    var more = root.querySelector('[data-feed-more]');
    var cat = '', shown = page;
    var render = function () {
      var match = items.filter(function (li) { return !cat || li.getAttribute('data-cat') === cat; });
      items.forEach(function (li) { li.hidden = true; li.classList.remove('is-more'); });
      match.forEach(function (li, i) { li.hidden = i >= shown; });
      if (more) more.parentNode.hidden = match.length <= shown;
    };
    tabs.forEach(function (t, k) {
      t.addEventListener('click', function () {
        cat = t.getAttribute('data-cat'); shown = page;
        tabs.forEach(function (o) { var on = o === t; o.setAttribute('aria-selected', on ? 'true' : 'false'); o.tabIndex = on ? 0 : -1; });
        render();
      });
      t.addEventListener('keydown', function (e) {
        var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0; if (!d) return;
        e.preventDefault(); var n = tabs[(k + d + tabs.length) % tabs.length]; n.focus(); n.click();
      });
    });
    if (more) more.addEventListener('click', function () {
      var firstNew = shown; shown += page; render();
      var match = items.filter(function (li) { return !cat || li.getAttribute('data-cat') === cat; });
      var a = match[firstNew] && match[firstNew].querySelector('a'); if (a) a.focus({ preventScroll: true });
    });
    render();
  });

  /* ---------- tab panels (video gallery) ---------- */
  document.querySelectorAll('[data-vtabs]').forEach(function (root) {
    var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));
    tabs.forEach(function (t, k) {
      t.addEventListener('click', function () {
        tabs.forEach(function (o) {
          var on = o === t, p = document.getElementById(o.getAttribute('aria-controls'));
          o.setAttribute('aria-selected', on ? 'true' : 'false'); o.tabIndex = on ? 0 : -1; if (p) p.hidden = !on;
        });
      });
      t.addEventListener('keydown', function (e) {
        var d = e.key === 'ArrowRight' ? 1 : e.key === 'ArrowLeft' ? -1 : 0; if (!d) return;
        e.preventDefault(); var n = tabs[(k + d + tabs.length) % tabs.length]; n.focus(); n.click();
      });
    });
  });

  /* ---------- reveal on scroll ---------- */
  var reveals = document.querySelectorAll('.cb-reveal');
  if (!reduce && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); } });
    }, { rootMargin: '0px 0px -8% 0px' });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('is-in'); });
  }
})();
