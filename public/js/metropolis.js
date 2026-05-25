var draggedId   = null;
var geselecteerd = null; // voor toetsenbord

// Library items sleepbaar + toetsenbord selectie + touch
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

    // Touch slepen voor mobiel
    var touchDragGhost = null;
    var laatsteCel = null;

    item.addEventListener('touchstart', function(e) {
        draggedId = item.getAttribute('data-id');
        item.style.opacity = '0.4';

        // Maak een visueel "spook" element dat de vinger volgt
        var rect = item.getBoundingClientRect();
        touchDragGhost = item.cloneNode(true);
        touchDragGhost.style.position = 'fixed';
        touchDragGhost.style.top = rect.top + 'px';
        touchDragGhost.style.left = rect.left + 'px';
        touchDragGhost.style.width = rect.width + 'px';
        touchDragGhost.style.pointerEvents = 'none';
        touchDragGhost.style.opacity = '0.85';
        touchDragGhost.style.zIndex = '9999';
        touchDragGhost.style.transform = 'scale(1.05)';
        touchDragGhost.style.boxShadow = '0 4px 12px rgba(0,0,0,0.25)';
        document.body.appendChild(touchDragGhost);
    }, { passive: true });

    item.addEventListener('touchmove', function(e) {
        if (!touchDragGhost) return;
        e.preventDefault();
        var touch = e.touches[0];

        // Volg de vinger
        var rect = touchDragGhost.getBoundingClientRect();
        touchDragGhost.style.top = (touch.clientY - rect.height / 2) + 'px';
        touchDragGhost.style.left = (touch.clientX - rect.width / 2) + 'px';

        // Vind cel onder de vinger
        touchDragGhost.style.display = 'none';
        var elementOnder = document.elementFromPoint(touch.clientX, touch.clientY);
        touchDragGhost.style.display = '';

        var celOnder = elementOnder ? elementOnder.closest('.cel') : null;

        if (celOnder !== laatsteCel) {
            if (laatsteCel) laatsteCel.classList.remove('drag-over');
            if (celOnder) celOnder.classList.add('drag-over');
            laatsteCel = celOnder;
        }
    }, { passive: false });

    item.addEventListener('touchend', function(e) {
        item.style.opacity = '1';
        if (touchDragGhost) {
            touchDragGhost.remove();
            touchDragGhost = null;
        }
        if (laatsteCel) {
            laatsteCel.classList.remove('drag-over');
            if (draggedId) {
                plaatsOpCel(laatsteCel.getAttribute('data-cell-id'), draggedId);
            }
            laatsteCel = null;
        }
        draggedId = null;
    });

    item.addEventListener('touchcancel', function() {
        item.style.opacity = '1';
        if (touchDragGhost) {
            touchDragGhost.remove();
            touchDragGhost = null;
        }
        if (laatsteCel) {
            laatsteCel.classList.remove('drag-over');
            laatsteCel = null;
        }
        draggedId = null;
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

// Zoeken in functiebibliotheek (alleen op hoofdpagina)
var zoektermInput = document.getElementById('zoekterm');
if (zoektermInput) {
    zoektermInput.addEventListener('input', function() {
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

        var geenResultaten = document.getElementById('geen-resultaten');
        if (geenResultaten) {
            geenResultaten.style.display = zichtbaar === 0 ? 'block' : 'none';
        }
    });
}

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

// SIM.10 — Hover over een geplaatste cel toont een tooltip met de
// effect-waarden van die functie. Groen = positief, rood = negatief.
(function() {
    'use strict';

    var matrix = window.metroEffectsMatrix;
    if (!matrix) return;

    var cellen = document.querySelectorAll('.cel.bezet');
    if (!cellen.length) return;

    function toonTooltip(cel) {
        var fn = cel.getAttribute('data-function-name');
        if (!fn || !matrix[fn]) return;

        wisTooltips();

        var tt = document.createElement('div');
        tt.className = 'cel-tooltip';

        var effecten = matrix[fn];
        Object.keys(effecten).forEach(function(cat) {
            var v = effecten[cat];
            var teken  = v > 0 ? '+' + v : String(v);
            var kleur  = v > 0 ? 'positief' : (v < 0 ? 'negatief' : 'neutraal');

            var rij = document.createElement('div');
            rij.className = 'tooltip-rij';

            var catEl = document.createElement('span');
            catEl.className = 'tooltip-cat';
            catEl.textContent = cat;

            var waardeEl = document.createElement('span');
            waardeEl.className = 'tooltip-waarde is-' + kleur;
            waardeEl.textContent = teken;

            rij.appendChild(catEl);
            rij.appendChild(waardeEl);
            tt.appendChild(rij);
        });

        cel.appendChild(tt);
    }

    function wisTooltips() {
        document.querySelectorAll('.cel-tooltip').forEach(function(t) { t.remove(); });
    }

    cellen.forEach(function(cel) {
        cel.addEventListener('mouseenter', function() { toonTooltip(cel); });
        cel.addEventListener('mouseleave', wisTooltips);
        cel.addEventListener('focus',      function() { toonTooltip(cel); });
        cel.addEventListener('blur',       wisTooltips);
    });
})();

// Effects Manager — alleen actief op /effecten-beheren pagina
(function() {
    'use strict';

    var tbody = document.getElementById('effects-tbody');
    if (!tbody) return; // niet op de manager pagina

    var i18n = window.metroI18n || {};

    var addFunctionBtn   = document.getElementById('add-function-btn');
    var stats            = document.getElementById('effects-manager-stats');
    var zoekInput        = document.getElementById('effects-manager-zoek-input');
    var totalenGrid      = document.getElementById('totalen-grid');
    var addModal         = document.getElementById('add-function-modal');
    var addForm          = document.getElementById('add-function-form');
    var addInput         = document.getElementById('add-function-input');
    var addFout          = document.getElementById('add-function-fout');
    var deleteModal      = document.getElementById('delete-function-modal');
    var deleteNaamSpan   = document.getElementById('delete-function-naam');
    var deleteBevestig   = document.getElementById('delete-function-bevestig');
    var resetBtn         = document.getElementById('reset-defaults-btn');
    var resetModal       = document.getElementById('reset-defaults-modal');
    var resetBevestig    = document.getElementById('reset-defaults-bevestig');
    var toastContainer   = document.getElementById('toast-container');

    var deleteTargetFunction = null;
    var saveTimers = {};
    var savedStatusTimers = {};

    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key !== 'Escape') return;
        if (addModal && !addModal.hidden) { closeSubmodal(addModal); return; }
        if (deleteModal && !deleteModal.hidden) { closeSubmodal(deleteModal); return; }
        if (resetModal && !resetModal.hidden) { closeSubmodal(resetModal); return; }
    });

    // ============ Submodal helpers ============
    function openSubmodal(modal, focusEl) {
        modal.hidden = false;
        void modal.offsetWidth;
        modal.setAttribute('aria-hidden', 'false');
        if (focusEl) setTimeout(function() { focusEl.focus(); }, 50);
    }

    function closeSubmodal(modal) {
        if (!modal || modal.hidden) return;
        modal.setAttribute('aria-hidden', 'true');
        setTimeout(function() { modal.hidden = true; }, 180);
    }

    document.querySelectorAll('[data-close-submodal]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var modal = btn.closest('.submodal');
            closeSubmodal(modal);
        });
    });

    // klik buiten submodal-inner sluit modal
    [addModal, deleteModal, resetModal].forEach(function(modal) {
        if (!modal) return;
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeSubmodal(modal);
        });
    });

    // ============ Reset naar defaults ============
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            openSubmodal(resetModal, resetBevestig);
        });
    }

    if (resetBevestig) {
        resetBevestig.addEventListener('click', function() {
            resetBevestig.disabled = true;
            fetch('/api/effects/reset-defaults', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken()
                }
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.success) throw data;
                showToast(i18n.resetDone || 'Reset gedaan, herladen...', 'success');
                closeSubmodal(resetModal);
                setTimeout(function() { location.reload(); }, 700);
            })
            .catch(function(err) {
                console.error('Reset error:', err);
                showToast(i18n.resetError || 'Fout bij reset', 'error');
                resetBevestig.disabled = false;
            });
        });
    }

    // ============ Inline edit (debounced save) ============
    if (tbody) {
        tbody.addEventListener('input', function(e) {
            if (!e.target.classList.contains('effect-input')) return;
            handleInputChange(e.target);
        });
    }

    function handleInputChange(input) {
        var fnName   = input.getAttribute('data-function');
        var category = input.getAttribute('data-category');
        var key      = fnName + '|' + category;
        var raw      = input.value;

        // negeer halverwege typen (leeg of alleen min-teken)
        if (raw === '' || raw === '-') {
            clearInputError(input);
            return;
        }

        var points = parseInt(raw, 10);

        if (isNaN(points)) {
            showInputError(input, i18n.invalidNumber || 'Ongeldig getal');
            clearTimeout(saveTimers[key]);
            return;
        }

        if (points < -5 || points > 5) {
            showInputError(input, (i18n.rangeError || 'Waarde moet tussen -5 en 5 zijn'));
            clearTimeout(saveTimers[key]);
            return;
        }

        clearInputError(input);
        setInputStatus(input, 'saving');

        clearTimeout(saveTimers[key]);
        saveTimers[key] = setTimeout(function() {
            saveEffect(input, fnName, category, points);
        }, 400);
    }

    function showInputError(input, message) {
        setInputStatus(input, 'error');
        var wrap = input.closest('.effect-input-wrap');
        if (!wrap) return;
        var msg = wrap.querySelector('.effect-input-fout');
        if (!msg) {
            msg = document.createElement('span');
            msg.className = 'effect-input-fout';
            msg.setAttribute('role', 'alert');
            wrap.appendChild(msg);
        }
        msg.textContent = message;
        input.setAttribute('aria-invalid', 'true');
    }

    function clearInputError(input) {
        var wrap = input.closest('.effect-input-wrap');
        if (!wrap) return;
        var msg = wrap.querySelector('.effect-input-fout');
        if (msg) msg.remove();
        input.removeAttribute('aria-invalid');
    }

    function saveEffect(input, fnName, category, points) {
        fetch('/api/effects/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({
                function_name: fnName,
                category: category,
                points: points
            })
        })
        .then(function(res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function(data) {
            if (!data.success) throw new Error('Server zei nee');
            input.setAttribute('data-initial', points);
            setInputStatus(input, 'saved');
            if (data.totals) updateTotalen(data.totals);
        })
        .catch(function(err) {
            console.error('Save error:', err);
            setInputStatus(input, 'error');
            showToast(i18n.saveError || 'Save error', 'error');
        });
    }

    function setInputStatus(input, status) {
        var wrap = input.closest('.effect-input-wrap');
        if (!wrap) return;
        wrap.setAttribute('data-status', status);

        var key = input.getAttribute('data-function') + '|' + input.getAttribute('data-category');
        clearTimeout(savedStatusTimers[key]);

        if (status === 'saved') {
            // verberg de "saved" indicator na 1.5s
            savedStatusTimers[key] = setTimeout(function() {
                if (wrap.getAttribute('data-status') === 'saved') {
                    wrap.setAttribute('data-status', 'idle');
                }
            }, 1500);
        }
    }

    // ============ Live totalen update ============
    function updateTotalen(totals) {
        if (!totalenGrid) return;
        Object.keys(totals).forEach(function(category) {
            var kaart = totalenGrid.querySelector('[data-category="' + category + '"]');
            if (!kaart) return;
            var score = totals[category];
            var scoreEl = kaart.querySelector('[data-score]');
            if (!scoreEl) return;

            var teken = score > 0 ? '+' + score : String(score);
            scoreEl.textContent = teken;

            kaart.classList.remove('totaal-positief', 'totaal-negatief', 'totaal-neutraal');
            if (score > 0)      kaart.classList.add('totaal-positief');
            else if (score < 0) kaart.classList.add('totaal-negatief');
            else                kaart.classList.add('totaal-neutraal');

            // pulse-animatie
            kaart.classList.remove('totaal-pulse');
            void kaart.offsetWidth;
            kaart.classList.add('totaal-pulse');
        });
    }

    // ============ Functie toevoegen ============
    if (addFunctionBtn) {
        addFunctionBtn.addEventListener('click', function() {
            addInput.value = '';
            hideFormError();
            openSubmodal(addModal, addInput);
        });
    }

    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();
            var name = addInput.value.trim();
            if (!name) { showFormError(i18n.nameRequired); return; }
            if (name.length > 50) { showFormError(i18n.nameTooLong); return; }
            submitAddFunction(name);
        });
    }

    function showFormError(msg) {
        addFout.textContent = msg;
        addFout.hidden = false;
        addInput.setAttribute('aria-invalid', 'true');
    }

    function hideFormError() {
        addFout.hidden = true;
        addFout.textContent = '';
        addInput.removeAttribute('aria-invalid');
    }

    function submitAddFunction(name) {
        var submitBtn = addForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        fetch('/api/effects/add-function', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ function_name: name })
        })
        .then(function(res) {
            return res.json().then(function(data) {
                if (!res.ok) throw data;
                return data;
            });
        })
        .then(function(data) {
            if (!data.success) throw data;
            appendNewRow(data);
            if (data.totals) updateTotalen(data.totals);
            closeSubmodal(addModal);
            showToast((i18n.functionAdded || 'Toegevoegd') + ' "' + name + '"', 'success');
            updateStats();
        })
        .catch(function(err) {
            console.error('Add error:', err);
            if (err && err.errors && err.errors.function_name) {
                showFormError(i18n.nameExists);
            } else {
                showFormError(i18n.addError);
            }
        })
        .finally(function() {
            submitBtn.disabled = false;
        });
    }

    function appendNewRow(data) {
        var emptyRow = document.getElementById('effects-empty-row');
        if (emptyRow) emptyRow.remove();

        var tr = document.createElement('tr');
        tr.className = 'effects-row effects-row-nieuw';
        tr.setAttribute('role', 'row');
        tr.setAttribute('data-function', data.function_name);

        var naamTd = document.createElement('td');
        naamTd.className = 'td-functienaam';
        naamTd.textContent = data.function_name;
        tr.appendChild(naamTd);

        data.categories.forEach(function(cat) {
            var td = document.createElement('td');
            td.className = 'td-points';
            td.setAttribute('role', 'cell');

            var wrap = document.createElement('div');
            wrap.className = 'effect-input-wrap';
            wrap.setAttribute('data-status', 'idle');

            var input = document.createElement('input');
            input.type = 'number';
            input.className = 'effect-input';
            input.value = data.effects[cat];
            input.min = '-5';
            input.max = '5';
            input.step = '1';
            input.setAttribute('data-function', data.function_name);
            input.setAttribute('data-category', cat);
            input.setAttribute('data-initial', data.effects[cat]);
            input.setAttribute('aria-label', data.function_name + ' - ' + cat);

            var status = document.createElement('span');
            status.className = 'effect-status';
            status.setAttribute('aria-hidden', 'true');

            wrap.appendChild(input);
            wrap.appendChild(status);
            td.appendChild(wrap);
            tr.appendChild(td);
        });

        var actiesTd = document.createElement('td');
        actiesTd.className = 'td-actions';
        var delBtn = document.createElement('button');
        delBtn.className = 'btn-delete-function';
        delBtn.setAttribute('data-function', data.function_name);
        delBtn.setAttribute('type', 'button');
        delBtn.setAttribute('aria-label', (i18n.delete || 'Verwijder') + ' ' + data.function_name);
        delBtn.title = (i18n.delete || 'Verwijder') + ' ' + data.function_name;
        delBtn.textContent = (i18n.delete || 'Verwijder');
        actiesTd.appendChild(delBtn);
        tr.appendChild(actiesTd);

        tbody.appendChild(tr);
    }

    // ============ Functie verwijderen ============
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('.btn-delete-function');
        if (!btn) return;
        deleteTargetFunction = btn.getAttribute('data-function');
        deleteNaamSpan.textContent = deleteTargetFunction;
        openSubmodal(deleteModal, deleteBevestig);
    });

    if (deleteBevestig) {
        deleteBevestig.addEventListener('click', function() {
            if (!deleteTargetFunction) return;
            submitDeleteFunction(deleteTargetFunction);
        });
    }

    function submitDeleteFunction(name) {
        deleteBevestig.disabled = true;

        fetch('/api/effects/delete-function', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken()
            },
            body: JSON.stringify({ function_name: name })
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.success) throw data;
            var row = tbody.querySelector('tr[data-function="' + cssEscape(name) + '"]');
            if (row) {
                row.classList.add('effects-row-verwijderd');
                setTimeout(function() {
                    row.remove();
                    showEmptyIfNeeded();
                    updateStats();
                }, 220);
            }
            if (data.totals) updateTotalen(data.totals);
            closeSubmodal(deleteModal);
            showToast((i18n.functionDeleted || 'Verwijderd') + ' "' + name + '"', 'success');
            deleteTargetFunction = null;
        })
        .catch(function(err) {
            console.error('Delete error:', err);
            showToast(i18n.deleteError || 'Delete error', 'error');
        })
        .finally(function() {
            deleteBevestig.disabled = false;
        });
    }

    function cssEscape(value) {
        if (window.CSS && CSS.escape) return CSS.escape(value);
        return String(value).replace(/["\\]/g, '\\$&');
    }

    function showEmptyIfNeeded() {
        if (tbody.querySelector('.effects-row')) return;
        if (document.getElementById('effects-empty-row')) return;
        var table = tbody.closest('table');
        var cols = table ? table.querySelectorAll('thead th').length : 1;
        var tr = document.createElement('tr');
        tr.id = 'effects-empty-row';
        var td = document.createElement('td');
        td.colSpan = cols;
        td.className = 'effects-empty';
        td.textContent = i18n.noResults || 'Geen resultaten';
        tr.appendChild(td);
        tbody.appendChild(tr);
    }

    // ============ Zoek in beheer ============
    if (zoekInput) {
        zoekInput.addEventListener('input', function() {
            var term = zoekInput.value.toLowerCase().trim();
            var rows = tbody.querySelectorAll('.effects-row');
            var zichtbaar = 0;
            rows.forEach(function(row) {
                var naam = (row.getAttribute('data-function') || '').toLowerCase();
                if (!term || naam.indexOf(term) !== -1) {
                    row.style.display = '';
                    zichtbaar++;
                } else {
                    row.style.display = 'none';
                }
            });
            updateStats(zichtbaar, rows.length);
        });
    }

    function updateStats(zichtbaar, totaal) {
        if (!stats) return;
        var rows = tbody.querySelectorAll('.effects-row');
        if (typeof totaal === 'undefined') totaal = rows.length;
        if (typeof zichtbaar === 'undefined') zichtbaar = totaal;

        var template = i18n.functionCount || ':count functies';
        if (zichtbaar === totaal) {
            stats.textContent = template.replace(':count', totaal);
        } else {
            stats.textContent = zichtbaar + ' / ' + template.replace(':count', totaal);
        }
    }

    // ============ Toast notifications ============
    function showToast(message, type) {
        if (!toastContainer) return;
        var toast = document.createElement('div');
        toast.className = 'toast toast-' + (type || 'info');
        toast.setAttribute('role', 'status');
        toast.textContent = message;
        toastContainer.appendChild(toast);
        // fade in
        void toast.offsetWidth;
        toast.classList.add('toast-zichtbaar');
        // auto verwijderen
        setTimeout(function() {
            toast.classList.remove('toast-zichtbaar');
            setTimeout(function() { toast.remove(); }, 300);
        }, 2800);
    }

    updateStats();
})();
