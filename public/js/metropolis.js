var draggedId   = null;
var geselecteerd = null; // voor toetsenbord

// Library items sleepbaar + toetsenbord selectie
document.querySelectorAll('.library-item').forEach(function(item) {
    item.draggable = true;

    // Drag
    item.addEventListener('dragstart', function() {
        draggedId = item.getAttribute('data-id');
        item.style.opacity = '0.4';
    });

    item.addEventListener('dragend', function() {
        item.style.opacity = '1';
    });

    // Toetsenbord: Enter of Spatie = selecteer item
    item.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            document.querySelectorAll('.library-item').forEach(function(i) {
                i.style.outline = '';
            });
            geselecteerd = item.getAttribute('data-id');
            item.style.outline = '2px solid #2d4a22';
        }
    });
});

// Grid cellen als drop zone + toetsenbord plaatsen
document.querySelectorAll('.cel').forEach(function(cel) {

    cel.addEventListener('dragover', function(e) {
        e.preventDefault();
        cel.classList.add('drag-over');
    });

    cel.addEventListener('dragleave', function(e) {
        if (!cel.contains(e.relatedTarget)) {
            cel.classList.remove('drag-over');
        }
    });

    cel.addEventListener('drop', function(e) {
        e.preventDefault();
        cel.classList.remove('drag-over');
        if (!draggedId) return;
        plaatsOpCel(cel.getAttribute('data-cell-id'), draggedId);
    });

    // Toetsenbord: Enter op cel = plaatsen met geselecteerd item
    cel.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && geselecteerd) {
            plaatsOpCel(cel.getAttribute('data-cell-id'), geselecteerd);
            geselecteerd = null;
            document.querySelectorAll('.library-item').forEach(function(i) {
                i.style.outline = '';
            });
        }
    });

    // Rechtermuisklik = verwijderen
    cel.addEventListener('contextmenu', function(e) {
        e.preventDefault();
        verwijderCel(cel.getAttribute('data-cell-id'));
    });
});

// Functie plaatsen
function plaatsOpCel(cellId, destId) {
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/place';

    var inputToken = document.createElement('input');
    inputToken.name  = '_token';
    inputToken.value = csrf;

    var inputCell = document.createElement('input');
    inputCell.name  = 'cell_id';
    inputCell.value = cellId;

    var inputDest = document.createElement('input');
    inputDest.name  = 'destination_id';
    inputDest.value = destId;

    form.appendChild(inputToken);
    form.appendChild(inputCell);
    form.appendChild(inputDest);
    document.body.appendChild(form);
    form.submit();
}

// Functie verwijderen
function verwijderCel(cellId) {
    var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/remove';

    var inputToken = document.createElement('input');
    inputToken.name  = '_token';
    inputToken.value = csrf;

    var inputCell = document.createElement('input');
    inputCell.name  = 'cell_id';
    inputCell.value = cellId;

    form.appendChild(inputToken);
    form.appendChild(inputCell);
    document.body.appendChild(form);
    form.submit();
}

// Zoeken
document.getElementById('zoekterm').addEventListener('input', function() {
    var zoekterm = this.value.toLowerCase();
    var zichtbaar = 0;

    document.querySelectorAll('.library-item').forEach(function(item) {
        var naam = item.getAttribute('data-name').toLowerCase();
        if (naam.includes(zoekterm)) {
            item.style.display = '';
            zichtbaar++;
        } else {
            item.style.display = 'none';
        }
    });

    document.getElementById('geen-resultaten').style.display = zichtbaar === 0 ? 'block' : 'none';
});

// Effectenmatrix openen/sluiten via hover en klik
(function() {
    'use strict';

    var trigger = document.getElementById('effecten-trigger');
    var panel = document.getElementById('effecten-panel');
    var sluitBtn = document.getElementById('effecten-sluit');

    if (!trigger || !panel) return;

    var hoverTimer = null;
    var sluitTimer = null;
    var lockedOpen = false;

    function openPanel() {
        clearTimeout(sluitTimer);
        panel.hidden = false;
        panel.classList.add('is-open');
        panel.setAttribute('aria-hidden', 'false');
        trigger.setAttribute('aria-expanded', 'true');
    }

    function sluitPanel() {
        clearTimeout(hoverTimer);
        clearTimeout(sluitTimer);
        lockedOpen = false;
        panel.classList.remove('is-open');
        panel.setAttribute('aria-hidden', 'true');
        trigger.setAttribute('aria-expanded', 'false');
        panel.hidden = true;
    }

    function togglePanel() {
        if (panel.classList.contains('is-open')) {
            lockedOpen = false;
            sluitPanel();
        } else {
            lockedOpen = true;
            openPanel();
        }
    }

    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        togglePanel();
    });

    if (sluitBtn) {
        sluitBtn.addEventListener('click', function() {
            sluitPanel();
            trigger.focus();
        });
    }

    trigger.addEventListener('mouseenter', function() {
        clearTimeout(sluitTimer);
        hoverTimer = setTimeout(function() {
            if (!lockedOpen) openPanel();
        }, 200);
    });

    trigger.addEventListener('mouseleave', function() {
        clearTimeout(hoverTimer);
        if (!lockedOpen) {
            sluitTimer = setTimeout(sluitPanel, 350);
        }
    });

    panel.addEventListener('mouseenter', function() {
        clearTimeout(sluitTimer);
    });

    panel.addEventListener('mouseleave', function() {
        if (!lockedOpen) {
            sluitTimer = setTimeout(sluitPanel, 350);
        }
    });

    document.addEventListener('pointerdown', function(e) {
        if (!panel.contains(e.target) && !trigger.contains(e.target)) {
            if (panel.classList.contains('is-open')) {
                sluitPanel();
            }
        }
    }, true);

    document.querySelectorAll('.library-item').forEach(function(item) {
        item.addEventListener('dragstart', sluitPanel);
        item.addEventListener('mousedown', sluitPanel);
        item.addEventListener('touchstart', sluitPanel, { passive: true });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && panel.classList.contains('is-open')) {
            sluitPanel();
            trigger.focus();
        }
    });
})();
