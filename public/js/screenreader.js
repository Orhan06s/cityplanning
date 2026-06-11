// ============================================
// Metropolis — Ingebouwde screenreader
// Gebruikt de Web Speech API die in alle
// moderne browsers ingebouwd zit.
// ============================================

(function () {
    'use strict';

    // Controleer of de browser spraak ondersteunt
    if (!window.speechSynthesis) {
        console.warn('Web Speech API niet ondersteund in deze browser.');
        var knop = document.getElementById('sr-knop');
        if (knop) knop.style.display = 'none';
        return;
    }

    var knop       = document.getElementById('sr-knop');
    var actief     = localStorage.getItem('sr-actief') === 'true';
    var taal       = document.documentElement.lang || 'nl';

    // ── Spraak helpers ──────────────────────────────────

    function spreek(tekst) {
        if (!actief || !tekst || tekst.trim() === '') return;
        window.speechSynthesis.cancel();
        var utt = new SpeechSynthesisUtterance(tekst.trim());
        utt.lang = taal === 'en' ? 'en-US' : 'nl-NL';
        utt.rate = 1;
        utt.pitch = 1;
        window.speechSynthesis.speak(utt);
    }

    // Haal leesbare tekst op uit een element
    function leesbaar(el) {
        if (!el) return '';

        // Gebruik aria-label als die er is
        var label = el.getAttribute('aria-label');
        if (label) return label;

        // Gebruik alt tekst van afbeeldingen
        if (el.tagName === 'IMG') return el.getAttribute('alt') || '';

        // Sla elementen over met aria-hidden
        if (el.getAttribute('aria-hidden') === 'true') return '';

        // Gebruik de zichtbare tekst
        return el.innerText || el.textContent || '';
    }

    // ── Knop-state beheren ──────────────────────────────

    function updateKnop() {
        if (!knop) return;
        knop.setAttribute('aria-pressed', String(actief));
        knop.classList.toggle('is-actief', actief);
        knop.setAttribute('aria-label', actief
            ? (knop.dataset.aanAria || 'Screenreader uitschakelen')
            : (knop.dataset.uitAria || 'Screenreader inschakelen')
        );
        var label = knop.querySelector('.sr-knop-label');
        if (label) label.textContent = actief
            ? (knop.dataset.aanLabel || 'Voorlezen aan')
            : (knop.dataset.uitLabel || 'Voorlezen');
    }

    function toggle() {
        actief = !actief;
        localStorage.setItem('sr-actief', String(actief));
        updateKnop();
        if (actief) {
            spreek('Screenreader ingeschakeld. Beweeg over elementen of gebruik Tab om ze te horen.');
        } else {
            window.speechSynthesis.cancel();
        }
    }

    if (knop) {
        knop.addEventListener('click', toggle);
    }

    updateKnop();


    // ── Lees voor bij focus (Tab-navigatie) ─────────────

    document.addEventListener('focusin', function (e) {
        if (!actief) return;
        var tekst = leesbaar(e.target);
        var type  = e.target.tagName.toLowerCase();

        // Voeg rol toe aan de tekst
        if (type === 'button')   tekst += ', knop';
        if (type === 'a')        tekst += ', link';
        if (type === 'input')    tekst = (e.target.getAttribute('aria-label') || tekst || e.target.getAttribute('placeholder') || '');
        if (type === 'select')   tekst = leesbaar(e.target) + ', keuzelijst';
        if (type === 'textarea') tekst = leesbaar(e.target) + ', tekstveld';

        // Cel-specifieke beschrijving
        if (e.target.classList.contains('cel')) {
            tekst = e.target.getAttribute('aria-label') || tekst;
        }

        spreek(tekst);
    });

    // ── Lees voor bij hover (muis-navigatie) ─────────────

    var hoverTimer = null;
    document.addEventListener('mouseover', function (e) {
        if (!actief) return;
        clearTimeout(hoverTimer);
        hoverTimer = setTimeout(function () {
            var el    = e.target.closest('[aria-label], button, a, input, select, .cel, .library-item, .nav-link');
            if (!el) return;
            var tekst = leesbaar(el);
            if (tekst) spreek(tekst);
        }, 300);
    });

    // ── Lees live-regions voor ───────────────────────────
    // MutationObserver: als aria-live content verandert, lees het voor

    var observer = new MutationObserver(function (mutaties) {
        if (!actief) return;
        mutaties.forEach(function (m) {
            var el = m.target;
            var live = el.getAttribute('aria-live');
            if (live && el.textContent.trim()) {
                spreek(el.textContent.trim());
            }
        });
    });

    // Observeer alle aria-live elementen
    document.querySelectorAll('[aria-live]').forEach(function (el) {
        observer.observe(el, { childList: true, subtree: true, characterData: true });
    });

    // Observeer meldingen die later geladen worden
    var mainEl = document.getElementById('main-content');
    if (mainEl) {
        observer.observe(mainEl, { childList: true, subtree: true });
    }

    // ── Keyboard shortcut: Alt + S = toggle ─────────────

    document.addEventListener('keydown', function (e) {
        if (e.altKey && e.key === 's') {
            e.preventDefault();
            toggle();
        }
    });

    // ── Exporteer spreek() zodat metropolis.js het kan aanroepen ──
    window.srSpreek = spreek;

})();
