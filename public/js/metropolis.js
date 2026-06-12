// ============================================
// Metropolis — JavaScript (simpel gehouden)
// ============================================

// === Helper: CSRF token uit de meta tag ===
function csrf() {
    return document.querySelector('meta[name="csrf-token"]').content;
}

// === Helper: POST JSON naar een URL en geef de response terug ===
function postJson(url, data) {
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf()
        },
        body: data ? JSON.stringify(data) : null
    }).then(function(res) { return res.json(); });
}

// === Helper: aankondiging naar screenreader sturen ===
function kondigAan(tekst) {
    // Gebruik de ingebouwde screenreader als die aanstaat
    if (window.srSpreek) {
        window.srSpreek(tekst);
    }
    // Altijd ook de aria-live regio bijwerken (voor externe screenreaders zoals NVDA/JAWS)
    var el = document.getElementById('grid-aankondiging');
    if (el) {
        el.textContent = '';
        setTimeout(function() { el.textContent = tekst; }, 50);
    }
}

// Bewaar scroll-positie zodat de pagina na reload niet naar boven springt
window.addEventListener('DOMContentLoaded', function() {
    var y = sessionStorage.getItem('metropolisScrollY');
    if (y !== null) {
        window.scrollTo(0, parseInt(y, 10) || 0);
        sessionStorage.removeItem('metropolisScrollY');
    }
});
// Onthoud scroll-positie bij elke form-submit (place/remove)
document.addEventListener('submit', function() {
    sessionStorage.setItem('metropolisScrollY', window.scrollY);
}, true);

// === Helper: maak een form en submit het (voor place/remove) ===
function submitForm(action, velden) {
    sessionStorage.setItem('metropolisScrollY', window.scrollY);
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    var token = document.createElement('input');
    token.name = '_token';
    token.value = csrf();
    form.appendChild(token);

    Object.keys(velden).forEach(function(naam) {
        var input = document.createElement('input');
        input.name = naam;
        input.value = velden[naam];
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}


// ============================================
// HOOFDPAGINA — grid + library
// ============================================

// Welk library-item is geselecteerd (voor klik-om-te-plaatsen)
var geselecteerdId = null;

// Selectie-state voor groepen (koppelen / verplaatsen)
var selectedGroupCellId = null;
var selectedGroupId = null;

// Drag-state voor het verslepen van gekoppelde groepen
var isGroupDragging = false;
var dragStartX = 0;
var dragStartY = 0;
var dragAnchorCellId = null;
var dragAnchorPosition = null;
var dragAnchorRect = null;
var dragSnapTargetEl = null;
var dragGhostElements = [];
var cellWidth = 0;
var cellHeight = 0;
var dragMoved = false;
var dragGroupElements = [];

function saveSelectionState() {
    if (selectedGroupId) {
        sessionStorage.setItem('metropolisSelectedGroupId', selectedGroupId);
        sessionStorage.removeItem('metropolisSelectedCellId');
    } else if (selectedGroupCellId) {
        sessionStorage.setItem('metropolisSelectedCellId', selectedGroupCellId);
        sessionStorage.removeItem('metropolisSelectedGroupId');
    } else {
        sessionStorage.removeItem('metropolisSelectedGroupId');
        sessionStorage.removeItem('metropolisSelectedCellId');
    }
}

function restoreSelectionState() {
    var groupId = sessionStorage.getItem('metropolisSelectedGroupId');
    if (groupId) {
        var cel = document.querySelector('.cel[data-linked-group="' + groupId + '"]');
        if (cel) selectGroupCell(cel);
        sessionStorage.removeItem('metropolisSelectedGroupId');
        return;
    }

    var cellId = sessionStorage.getItem('metropolisSelectedCellId');
    if (cellId) {
        var cel = document.querySelector('.cel[data-cell-id="' + cellId + '"]');
        if (cel && cel.classList.contains('bezet')) selectGroupCell(cel);
        sessionStorage.removeItem('metropolisSelectedCellId');
    }
}

function resetGroupSelection() {
    selectedGroupCellId = null;
    selectedGroupId = null;
    document.querySelectorAll('.cel.group-selected').forEach(function(cel) {
        cel.classList.remove('group-selected');
    });
    updateBestemmingActies(null);
}

function selectGroupCell(cel) {
    resetGroupSelection();
    selectedGroupCellId = cel.dataset.cellId;
    selectedGroupId = cel.dataset.linkedGroup || null;

    if (selectedGroupId) {
        document.querySelectorAll('.cel[data-linked-group="' + selectedGroupId + '"]').forEach(function(other) {
            other.classList.add('group-selected');
        });
    } else {
        cel.classList.add('group-selected');
    }
    updateBestemmingActies(cel);
}

// Library items: kunnen gesleept worden EN aangeklikt voor selectie
document.querySelectorAll('.library-item').forEach(function(item) {
    item.addEventListener('dragstart', function(e) {
        e.dataTransfer.setData('text/plain', item.dataset.id);
    });

    item.addEventListener('click', function() {
        resetGroupSelection();
        var wasSelected = item.classList.contains('geselecteerd');
        document.querySelectorAll('.library-item.geselecteerd').forEach(function(i) {
            i.classList.remove('geselecteerd');
        });
        if (!wasSelected) {
            item.classList.add('geselecteerd');
            geselecteerdId = item.dataset.id;
        } else {
            geselecteerdId = null;
        }
    });
});

// Grid cellen: kunnen drop ontvangen, geklikt worden, of een groep verslepen
document.querySelectorAll('.cel').forEach(function(cel) {
    cel.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    // Aankondiging + visuele highlight bij binnenkomen van een cel
    cel.addEventListener('dragenter', function(e) {
        e.preventDefault();
        cel.classList.add('drag-over');
        var label = cel.getAttribute('aria-label');
        if (label) kondigAan(label);
    });

    cel.addEventListener('dragleave', function(e) {
        // Alleen verwijderen als we echt de cel verlaten (niet bij een child element)
        if (e.target === cel) {
            cel.classList.remove('drag-over');
        }
    });

    cel.addEventListener('drop', function(e) {
        e.preventDefault();
        cel.classList.remove('drag-over');
        var id = e.dataTransfer.getData('text/plain');
        if (id) {
            resetGroupSelection();
            submitForm('/place', {
                cell_id: cel.dataset.cellId,
                destination_id: id,
                size: window.metroGridSize || 12
            });
        }
    });

    cel.addEventListener('click', function() {
        // Net een drag gehad → click niet behandelen
        if (dragMoved) { dragMoved = false; return; }

        // Library-item geselecteerd + lege cel → plaatsen
        if (geselecteerdId && cel.classList.contains('leeg')) {
            resetGroupSelection();
            var geselecteerdItem = document.querySelector('.library-item.geselecteerd');
            var functieNaam = geselecteerdItem ? geselecteerdItem.dataset.name : '';
            kondigAan(functieNaam + ' geplaatst op cel ' + (parseInt(cel.dataset.position, 10) + 1));
            submitForm('/place', {
                cell_id: cel.dataset.cellId,
                destination_id: geselecteerdId,
                size: window.metroGridSize || 12
            });
            return;
        }

        // Klik op een bezette cel → selecteren of koppelen
        if (cel.classList.contains('bezet')) {
            if (!selectedGroupCellId) {
                selectGroupCell(cel);
                return;
            }
            if (selectedGroupCellId === cel.dataset.cellId) {
                resetGroupSelection();
                return;
            }
            // Koppelen is alleen voor admin/gebruiker; anderen wisselen gewoon selectie
            if (!window.metroCanEdit) {
                selectGroupCell(cel);
                return;
            }
            postJson('/group/link', {
                source_cell_id: selectedGroupCellId,
                target_cell_id: cel.dataset.cellId
            }).then(function(data) {
                if (data.success) {
                    saveSelectionState();
                    location.reload();
                } else {
                    alert(data.message || 'Fout bij koppelen.');
                }
            });
            return;
        }

        // Klik op lege cel terwijl een groep geselecteerd is → verplaatsen
        if (cel.classList.contains('leeg') && selectedGroupCellId) {
            postJson('/group/move', {
                source_cell_id: selectedGroupCellId,
                target_cell_id: cel.dataset.cellId
            }).then(function(data) {
                if (data.success) {
                    saveSelectionState();
                    location.reload();
                } else {
                    alert(data.message || 'Fout bij verplaatsen.');
                }
            });
        }
    });

    // Rechtermuisklik op andere bezette cel → koppel met geselecteerde
    cel.addEventListener('contextmenu', function(e) {
        if (!selectedGroupCellId) return;
        if (selectedGroupCellId === cel.dataset.cellId) return;
        if (!cel.classList.contains('bezet')) return;

        e.preventDefault();

        postJson('/group/link', {
            source_cell_id: selectedGroupCellId,
            target_cell_id: cel.dataset.cellId
        }).then(function(data) {
            if (data.success) {
                saveSelectionState();
                location.reload();
            } else {
                alert(data.message || 'Fout bij koppelen.');
            }
        });
    });

    // Sleep gekoppelde groep met de muis
    cel.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return;                       // alleen linkermuisknop
        if (e.target.closest('.verwijder-btn')) return;   // niet bij klik op X
        var linked = cel.dataset.linkedGroup;
        if (!linked) return;

        dragMoved = false;

        var groupCells = Array.from(document.querySelectorAll('.cel[data-linked-group="' + linked + '"]'));
        var anchorCell = groupCells.reduce(function(min, el) {
            return parseInt(el.dataset.position, 10) < parseInt(min.dataset.position, 10) ? el : min;
        }, groupCells[0]);

        dragAnchorCellId = anchorCell.dataset.cellId;
        dragAnchorPosition = parseInt(anchorCell.dataset.position, 10);
        dragAnchorRect = anchorCell.getBoundingClientRect();
        cellWidth = anchorCell.offsetWidth;
        cellHeight = anchorCell.offsetHeight;

        // Maak ghost-elementen die de muis volgen
        dragGhostElements = groupCells.map(function(el) {
            var rect = el.getBoundingClientRect();
            var ghost = el.cloneNode(true);
            ghost.classList.add('drag-ghost');
            ghost.style.position = 'absolute';
            ghost.style.left = rect.left + 'px';
            ghost.style.top = rect.top + 'px';
            ghost.style.width = rect.width + 'px';
            ghost.style.height = rect.height + 'px';
            ghost.style.margin = '0';
            ghost.style.transform = 'none';
            ghost.style.transition = 'transform 0s';
            ghost.style.pointerEvents = 'none';
            ghost.style.opacity = '0.45';
            ghost.style.zIndex = '100';
            document.body.appendChild(ghost);
            return ghost;
        });

        dragGroupElements = groupCells;
        dragGroupElements.forEach(function(el) {
            el.classList.add('dragging');
            el.style.zIndex = 60;
            el.style.pointerEvents = 'none';
        });

        dragStartX = e.clientX;
        dragStartY = e.clientY;

        function resetDragVisuals() {
            dragGroupElements.forEach(function(el) {
                el.classList.remove('dragging');
                el.style.zIndex = '';
                el.style.pointerEvents = '';
            });
            dragGroupElements = [];
            dragGhostElements.forEach(function(el) { el.remove(); });
            dragGhostElements = [];
            if (dragSnapTargetEl) {
                dragSnapTargetEl.classList.remove('drag-over');
                dragSnapTargetEl = null;
            }
        }

        function onMouseMove(ev) {
            var dx = ev.clientX - dragStartX;
            var dy = ev.clientY - dragStartY;
            if (!isGroupDragging && Math.hypot(dx, dy) > 6) {
                isGroupDragging = true;
            }

            if (!isGroupDragging) return;

            dragGhostElements.forEach(function(ghost) {
                ghost.style.transform = 'translate(' + dx + 'px,' + dy + 'px)';
            });
            dragMoved = true;

            // Bepaal doelcel op basis van anchor-ghost middelpunt
            var anchorLeft = dragAnchorRect.left + dx;
            var anchorTop  = dragAnchorRect.top + dy;
            var centerX = anchorLeft + (cellWidth / 2);
            var centerY = anchorTop + (cellHeight / 2);

            if (dragSnapTargetEl) {
                dragSnapTargetEl.classList.remove('drag-over');
                dragSnapTargetEl = null;
            }

            var elemAt = document.elementFromPoint(Math.round(centerX), Math.round(centerY));
            var snapEl = elemAt ? elemAt.closest('.cel') : null;
            if (snapEl && snapEl.classList.contains('leeg')) {
                dragSnapTargetEl = snapEl;
                dragSnapTargetEl.classList.add('drag-over');
            }
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);

            if (!isGroupDragging) {
                resetDragVisuals();
                return;
            }

            if (dragSnapTargetEl) {
                postJson('/group/move', {
                    source_cell_id: dragAnchorCellId,
                    target_cell_id: dragSnapTargetEl.dataset.cellId
                }).then(function(data) {
                    if (data.success) {
                        saveSelectionState();
                        location.reload();
                    } else {
                        alert(data.message || 'Fout bij verplaatsen.');
                        resetDragVisuals();
                    }
                }).catch(function() { resetDragVisuals(); });
            } else {
                resetDragVisuals();
            }

            isGroupDragging = false;
            dragAnchorCellId = null;
            dragAnchorPosition = null;
            dragAnchorRect = null;
            cellWidth = 0;
            cellHeight = 0;
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });
});

// Verwijder-knop niet door laten klikken naar de cel-click
document.querySelectorAll('.verwijder-btn').forEach(function(btn) {
    btn.addEventListener('click', function(e) { e.stopPropagation(); });
});

// ============================================
// BESTEMMINGEN — toon knoppen bij geselecteerde cel
// ============================================

// Toon/verberg de bestemmings-actieknop op basis van cel-selectie
function updateBestemmingActies(geselecteerdeCel) {
    // Verberg alle acties eerst
    document.querySelectorAll('.cel-acties').forEach(function(el) {
        el.setAttribute('aria-hidden', 'true');
        el.style.display = 'none';
    });

    if (geselecteerdeCel) {
        var acties = geselecteerdeCel.querySelector('.cel-acties');
        if (acties) {
            acties.setAttribute('aria-hidden', 'false');
            acties.style.display = 'flex';
        }
    }
}

// Bestemmings-knoppen AJAX
document.addEventListener('click', function(e) {

    // Stel in als bestemming
    var markBtn = e.target.closest('.btn-stel-bestemming');
    if (markBtn) {
        e.stopPropagation();
        var cellId = markBtn.dataset.cellId;
        postJson('/cel/bestemming', { cell_id: cellId }).then(function(data) {
            if (data.success) location.reload();
        });
        return;
    }

    // Goedkeuren (alleen beleidsmaker)
    var approveBtn = e.target.closest('.btn-goedkeuren');
    if (approveBtn) {
        e.stopPropagation();
        if (!confirm('Weet je zeker dat je deze bestemming wilt goedkeuren? Dit wordt vastgelegd met je naam en datum.')) return;
        var goedkeurId = approveBtn.dataset.cellId;
        postJson('/cel/goedkeuren', { cell_id: goedkeurId }).then(function(data) {
            if (data.success) location.reload();
            else alert(data.message || 'Goedkeuren mislukt.');
        });
        return;
    }

    // Goedkeuring intrekken
    var unapproveBtn = e.target.closest('.btn-goedkeuring-intrekken');
    if (unapproveBtn) {
        e.stopPropagation();
        if (!confirm('Weet je zeker dat je de goedkeuring wilt intrekken?')) return;
        var intrekId = unapproveBtn.dataset.cellId;
        postJson('/cel/goedkeuren/intrekken', { cell_id: intrekId }).then(function(data) {
            if (data.success) location.reload();
        });
        return;
    }

    // Verwijder als bestemming
    var unmarkBtn = e.target.closest('.btn-verwijder-bestemming');
    if (unmarkBtn) {
        e.stopPropagation();
        var cellId = unmarkBtn.dataset.cellId;
        postJson('/cel/bestemming/verwijder', { cell_id: cellId }).then(function(data) {
            if (data.success) location.reload();
        });
        return;
    }
});

restoreSelectionState();

// Context-menu uitzetten op het grid (we gebruiken rechtermuisknop voor koppelen)
var grid = document.querySelector('.grid');
if (grid) {
    grid.addEventListener('contextmenu', function(e) { e.preventDefault(); });
}

// Zoekveld in functiebibliotheek
var zoekInput = document.getElementById('zoekterm');
if (zoekInput) {
    zoekInput.addEventListener('input', function() {
        var term = zoekInput.value.toLowerCase();
        document.querySelectorAll('.library-item').forEach(function(item) {
            var naam = item.dataset.name.toLowerCase();
            item.style.display = naam.indexOf(term) !== -1 ? '' : 'none';
        });
    });
}

// Leeg grid knop (verwijdert alle functies van het grid)
var clearGridBtn = document.getElementById('clear-grid-btn');
if (clearGridBtn) {
    clearGridBtn.addEventListener('click', function() {
        if (!confirm('Weet je zeker dat je alle functies van het grid wilt verwijderen?')) return;
        postJson('/grid/clear', null).then(function(data) {
            if (data.success) location.reload();
            else alert('Fout bij legen van grid.');
        }).catch(function() { alert('Fout bij legen van grid.'); });
    });
}


// ============================================
// EFFECTEN-MENU bovenaan: sluit andere dropdowns als er één open gaat
// ============================================

document.querySelectorAll('.effecten-details').forEach(function(d) {
    d.addEventListener('toggle', function() {
        if (!d.open) return;
        document.querySelectorAll('.effecten-details[open]').forEach(function(ander) {
            if (ander !== d) ander.open = false;
        });
    });
});

// Klik buiten het menu → alle dropdowns sluiten
document.addEventListener('click', function(e) {
    if (e.target.closest('.effecten-details')) return;
    document.querySelectorAll('.effecten-details[open]').forEach(function(d) {
        d.open = false;
    });
});


// ============================================
// HOVER TOOLTIP op bezette grid-cellen (SIM.10)
// ============================================

var effectsMatrix = window.metroEffectsMatrix;

document.querySelectorAll('.cel.bezet').forEach(function(cel) {
    cel.addEventListener('mouseenter', function() { toonTooltip(cel); });
    cel.addEventListener('mouseleave', verwijderTooltips);
});

function toonTooltip(cel) {
    if (!effectsMatrix) return;
    var fn = cel.dataset.functionName;
    if (!effectsMatrix[fn]) return;

    verwijderTooltips();

    var tt = document.createElement('div');
    tt.className = 'cel-tooltip';

    Object.keys(effectsMatrix[fn]).forEach(function(cat) {
        var v = effectsMatrix[fn][cat];
        var teken = v > 0 ? '+' + v : String(v);
        var kleur = v > 0 ? 'positief' : (v < 0 ? 'negatief' : 'neutraal');

        var rij = document.createElement('div');
        rij.className = 'tooltip-rij';
        rij.innerHTML =
            '<span>' + cat + '</span>' +
            '<span class="tooltip-waarde is-' + kleur + '">' + teken + '</span>';
        tt.appendChild(rij);
    });

    cel.appendChild(tt);
}

function verwijderTooltips() {
    document.querySelectorAll('.cel-tooltip').forEach(function(t) { t.remove(); });
}


// ============================================
// EFFECTENMATRIX paneel (read-only) — klik om te openen/sluiten
// ============================================

var matrixTrigger = document.getElementById('effecten-trigger');
var matrixPanel   = document.getElementById('effecten-panel');
var matrixSluit   = document.getElementById('effecten-sluit');

if (matrixTrigger && matrixPanel) {
    matrixTrigger.addEventListener('click', function() {
        var wasOpen = !matrixPanel.hidden;
        matrixPanel.hidden = wasOpen;
        matrixTrigger.setAttribute('aria-expanded', String(!wasOpen));
    });

    if (matrixSluit) {
        matrixSluit.addEventListener('click', function() {
            matrixPanel.hidden = true;
            matrixTrigger.setAttribute('aria-expanded', 'false');
        });
    }
}


// ============================================
// EFFECTS MANAGER PAGINA
// ============================================

var tbody = document.getElementById('effects-tbody');

if (tbody) {
    // Wijziging in een input → opslaan via AJAX
    tbody.addEventListener('change', function(e) {
        if (!e.target.classList.contains('effect-input')) return;
        var input = e.target;
        var waarde = parseInt(input.value, 10);

        if (isNaN(waarde) || waarde < -5 || waarde > 5) {
            alert('Waarde moet een getal tussen -5 en 5 zijn.');
            input.value = input.dataset.initial;
            return;
        }

        postJson('/api/effects/update', {
            function_name: input.dataset.function,
            category:      input.dataset.category,
            points:        waarde
        }).then(function(data) {
            if (data.success) {
                input.dataset.initial = waarde;
            } else {
                alert('Fout bij opslaan.');
            }
        });
    });

    // Klik op verwijder-knop in een rij
    tbody.addEventListener('click', function(e) {
        if (!e.target.classList.contains('btn-delete-function')) return;
        var naam = e.target.dataset.function;
        if (!confirm('Weet je zeker dat je "' + naam + '" wilt verwijderen?')) return;

        postJson('/api/effects/delete-function', { function_name: naam })
            .then(function(data) {
                if (data.success) location.reload();
                else alert('Fout bij verwijderen.');
            });
    });

    // Functie toevoegen via modal
    setupAddFunctionModal();

    // Reset naar standaardwaarden
    var resetBtn = document.getElementById('reset-defaults-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (!confirm('Reset alle effecten naar de standaardwaarden? Eigen aanpassingen gaan verloren.')) return;

            postJson('/api/effects/reset-defaults', null)
                .then(function(data) {
                    if (data.success) location.reload();
                    else alert('Fout bij resetten.');
                });
        });
    }

    // Zoekveld in manager
    var zoekManager = document.getElementById('effects-manager-zoek-input');
    if (zoekManager) {
        zoekManager.addEventListener('input', function() {
            var term = zoekManager.value.toLowerCase();
            document.querySelectorAll('.effects-row').forEach(function(row) {
                var fn = row.dataset.function.toLowerCase();
                row.style.display = fn.indexOf(term) !== -1 ? '' : 'none';
            });
        });
    }
}


// ============================================
// DAG/NACHT MODIFIERS — Effects Manager (admin tabel)
// ============================================

var daynightTbody = document.getElementById('daynight-tbody');

if (daynightTbody) {
    daynightTbody.addEventListener('change', function(e) {
        var input = e.target;
        
        if (!input.classList.contains('input-dag') && !input.classList.contains('input-nacht')) return;

        var waarde = parseInt(input.value, 10);

        if (isNaN(waarde) || waarde < -5 || waarde > 5) {
            alert('Waarde moet tussen -5 en 5 zijn');
            input.value = input.dataset.initial;
            return;
        }

        var wrap = input.closest('.modifier-input-wrap');
        var phase = input.dataset.phase;
        var dagVal   = parseInt(input.getAttribute('data-day-value') || '0', 10);
        var nachtVal = parseInt(input.getAttribute('data-night-value') || '0', 10);
        var sendDag   = phase === 'dag' ? waarde : dagVal;
        var sendNacht = phase === 'nacht' ? waarde : nachtVal;

        var payload = {
            function_name:  input.dataset.function,
            category:      input.dataset.category,
            day_modifier:   sendDag,
            night_modifier: sendNacht
        };

       
        })
        .catch(function(err) {
            console.error('❌ Network error:', err); // 👈 DEBUG
            alert('Netwerkfout: ' + err.message);
            input.value = input.dataset.initial;
        })
        .finally(function() {
            setTimeout(function() {
                if (wrap) wrap.removeAttribute('data-status');
            }, 1200);
        });


}


// ============================================
// DAY/NIGHT CYCLE + SIMULATION SPEED (SIM.6 / SIM.6.1)
// ============================================

var DAY_START_HOUR   = 8;
var NIGHT_START_HOUR = 20;

var SIM_SPEEDS = {
    slow:   60,
    normal: 300,
    fast:   1200
};

var speedLabels = { 
    slow: 'Realtime', 
    normal: 'Normaal', 
    fast: 'Snel' 
};

var simSpeed    = SIM_SPEEDS.slow;
var simPaused   = false;
var simTime     = null;
var simInterval = null;

function initSimTime() {
    var saved = localStorage.getItem('metroSimTime');
    if (saved) {
        var d = new Date(parseInt(saved, 10));
        if (!isNaN(d.getTime())) { simTime = d; return; }
    }
    simTime = new Date();
}

function saveSimTime() {
    if (simTime) localStorage.setItem('metroSimTime', String(simTime.getTime()));
}

function isDay(time) {
    var h = time.getHours();
    return h >= DAY_START_HOUR && h < NIGHT_START_HOUR;
}

function formatTime(date) {
    var h = String(date.getHours()).padStart(2, '0');
    var m = String(date.getMinutes()).padStart(2, '0');
    return h + ':' + m;
}

function startCycle() {
    if (simInterval) clearInterval(simInterval);
    if (!simTime) initSimTime();

    simInterval = setInterval(function() {
        if (simPaused) return;
        simTime = new Date(simTime.getTime() + simSpeed * 1000);
        saveSimTime();
        updateDayNightUI();
        recalculateScoresWithModifiers();
    }, 1000);

    updateDayNightUI();
    recalculateScoresWithModifiers();
}

function updateDayNightUI() {
    var day = isDay(simTime);
    var gridEl    = document.querySelector('.grid');
    var wrapperEl = document.querySelector('.grid-wrapper');

    if (gridEl) {
        gridEl.classList.toggle('is-day',   day);
        gridEl.classList.toggle('is-night', !day);
    }
    if (wrapperEl) {
        wrapperEl.classList.toggle('is-day',   day);
        wrapperEl.classList.toggle('is-night', !day);
    }

    document.body.classList.toggle('is-day-mode',  day);
    document.body.classList.toggle('is-night-mode', !day);

    var clockEl = document.getElementById('sim-clock');
    if (clockEl) clockEl.textContent = formatTime(simTime);

    var phaseEl = document.getElementById('sim-phase-label');
    if (phaseEl) phaseEl.textContent = day ? window.simLabels.dag : window.simLabels.nacht;
}

function recalculateScoresWithModifiers() {
    var baseMatrix = window.metroEffectsMatrix     || {};
    var modifiers  = window.metroDayNightModifiers || {};
    var events     = window.metroEventEffects      || {};
    var phase      = isDay(simTime) ? 'day' : 'night';
    var totals     = {};

    // Begin met alle zichtbare categorieën op 0, plus event-effects als basis
    document.querySelectorAll('.totaal-kaart').forEach(function(k) {
        if (!k.dataset.category) return;
        var cat = k.dataset.category;
        totals[cat] = (events[cat] || 0);
    });

    document.querySelectorAll('.cel.bezet').forEach(function(cel) {
        var fn = cel.dataset.functionName;
        if (!fn) return;

        var base = baseMatrix[fn] || {};
        var mods = modifiers[fn]  || {};

        Object.keys(base).forEach(function(cat) {
            var baseScore = base[cat] || 0;
            var mod = (mods[cat] && mods[cat][phase]) ? mods[cat][phase] : 0;
            totals[cat] = (totals[cat] || 0) + baseScore + mod;
        });
    });

    Object.keys(totals).forEach(function(cat) {
        var kaart = document.querySelector('.totaal-kaart[data-category="' + cat + '"]');
        if (!kaart) return;
        
        var score  = totals[cat];
        var teken  = score > 0 ? '+' + score : String(score);
        var klasse = score > 0 ? 'totaal-positief' : (score < 0 ? 'totaal-negatief' : 'totaal-neutraal');

        kaart.className = 'totaal-kaart ' + klasse;
        var scoreEl = kaart.querySelector('[data-score]');
        if (scoreEl) scoreEl.textContent = teken;
    });
}

function setSimSpeed(multiplier) {
    simSpeed = multiplier;
}

function pauseSim() {
    simPaused = true;
}

function resumeSim() {
    simPaused = false;
}

document.addEventListener('DOMContentLoaded', function() {
    var slowBtn = document.querySelector('.btn-speed[data-speed-key="slow"]');
    if (slowBtn) slowBtn.classList.add('is-actief');

    document.querySelectorAll('.btn-speed').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-speed').forEach(function(b) {
                b.classList.remove('is-actief');
            });
            btn.classList.add('is-actief');
            setSimSpeed(SIM_SPEEDS[btn.dataset.speedKey]);
        });
    });

    var simPauseBtn = document.getElementById('sim-pause-btn');
if (simPauseBtn) {
    simPauseBtn.addEventListener('click', function() {
        var pauseText = simPauseBtn.querySelector('.pause-text');
        var resumeText = simPauseBtn.querySelector('.resume-text');
        
        if (simPaused) {
            resumeSim();
            pauseText.classList.remove('hidden');
            resumeText.classList.add('hidden');
            simPauseBtn.setAttribute('aria-pressed', 'false');
        } else {
            pauseSim();
            pauseText.classList.add('hidden');
            resumeText.classList.remove('hidden');
            simPauseBtn.setAttribute('aria-pressed', 'true');
        }
    });
}

    if (document.querySelector('.grid')) {
        startCycle();
    }
});


if (document.querySelector('.grid')) {
    startCycle();
}

 var currentPhase = 'dag';

    function switchPhase(phase) {
        currentPhase = phase;

        var btnDag = document.getElementById('btn-toggle-dag');
        var btnNacht = document.getElementById('btn-toggle-nacht');

        if (phase === 'dag') {
            btnDag.classList.add('is-actief');
            btnDag.setAttribute('aria-selected', 'true');
            btnNacht.classList.remove('is-actief');
            btnNacht.setAttribute('aria-selected', 'false');
            document.body.classList.remove('showing-nacht');
            document.body.classList.add('showing-dag');
        } else {
            btnNacht.classList.add('is-actief');
            btnNacht.setAttribute('aria-selected', 'true');
            btnDag.classList.remove('is-actief');
            btnDag.setAttribute('aria-selected', 'false');
            document.body.classList.remove('showing-dag');
            document.body.classList.add('showing-nacht');
        }

        document.querySelectorAll('.modifier-dag').forEach(function(el) {
            el.classList.toggle('hidden', phase !== 'dag');
        });
        document.querySelectorAll('.modifier-nacht').forEach(function(el) {
            el.classList.toggle('hidden', phase !== 'nacht');
        });
    }

    // Init on load
    document.addEventListener('DOMContentLoaded', function() {
        document.body.classList.add('showing-dag');
        
      
        var daynightTbody = document.getElementById('daynight-tbody');

        if (daynightTbody) {
            daynightTbody.addEventListener('change', function(e) {
                var input = e.target;
                
                if (!input.classList.contains('input-dag') && !input.classList.contains('input-nacht')) return;

                var waarde = parseInt(input.value, 10);

                if (isNaN(waarde) || waarde < -5 || waarde > 5) {
                    alert('Waarde moet tussen -5 en 5 zijn');
                    input.value = input.dataset.initial;
                    return;
                }

                var wrap = input.closest('.modifier-input-wrap');
                var phase = input.dataset.phase;
                var dagVal   = parseInt(input.getAttribute('data-day-value') || '0', 10);
                var nachtVal = parseInt(input.getAttribute('data-night-value') || '0', 10);
                var sendDag   = phase === 'dag' ? waarde : dagVal;
                var sendNacht = phase === 'nacht' ? waarde : nachtVal;

                wrap.setAttribute('data-status', 'saving');

                fetch('/api/day-night-modifiers/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf(),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        function_name:  input.dataset.function,
                        category:      input.dataset.category,
                        day_modifier:   sendDag,
                        night_modifier: sendNacht
                    })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.success) {
                        input.dataset.initial = waarde;
                        if (phase === 'dag') {
                            input.setAttribute('data-day-value', waarde);
                        } else {
                            input.setAttribute('data-night-value', waarde);
                        }
                        wrap.setAttribute('data-status', 'saved');
                    } else {
                        alert('Fout bij opslaan.');
                        input.value = input.dataset.initial;
                    }
                })
                .catch(function() {
                    alert('Netwerkfout.');
                    input.value = input.dataset.initial;
                })
                .finally(function() {
                    setTimeout(function() {
                        if (wrap) wrap.removeAttribute('data-status');
                    }, 1200);
                });
            });
        }
    });




// Modal voor functie toevoegen
function setupAddFunctionModal() {
    var openBtn   = document.getElementById('add-function-btn');
    var modal     = document.getElementById('add-function-modal');
    var sluitBtn  = document.getElementById('add-function-modal-sluit');
    var cancelBtn = document.getElementById('add-function-annuleer');
    var form      = document.getElementById('add-function-form');
    var foutBox   = document.getElementById('add-function-fout');
    var preview   = document.getElementById('af-icon-preview');
    var iconInput = document.getElementById('af-icon');
    var fileInput = document.getElementById('af-icon-file');
    var catSelect = document.getElementById('af-category');
    var submitBtn = document.getElementById('add-function-submit');

    if (!openBtn || !modal || !form) return;

    var categoryIcons = window.metroCategoryIcons || {};

    function openModal() {
        form.reset();
        iconInput.value = '';
        foutBox.hidden = true;
        document.querySelectorAll('.icon-keuze-btn.is-actief').forEach(function(b) {
            b.classList.remove('is-actief');
            b.setAttribute('aria-checked', 'false');
        });
        updatePreviewFromCategory();
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        document.getElementById('af-function-name').focus();
    }

    function closeModal() {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
    }

    function updatePreviewFromCategory() {
        if (iconInput.value || (fileInput && fileInput.files.length)) return;
        preview.textContent = categoryIcons[catSelect.value] || '📍';
    }

    openBtn.addEventListener('click', openModal);
    sluitBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.hidden) closeModal();
    });

    catSelect.addEventListener('change', updatePreviewFromCategory);

    document.querySelectorAll('.icon-keuze-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.icon-keuze-btn.is-actief').forEach(function(b) {
                b.classList.remove('is-actief');
                b.setAttribute('aria-checked', 'false');
            });
            btn.classList.add('is-actief');
            btn.setAttribute('aria-checked', 'true');
            iconInput.value = btn.dataset.icon;
            if (fileInput) fileInput.value = '';
            preview.textContent = btn.dataset.icon;
        });
    });

    fileInput.addEventListener('change', function() {
        if (!fileInput.files.length) {
            updatePreviewFromCategory();
            return;
        }
        var file = fileInput.files[0];
        if (file.type.indexOf('image/') === 0) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                preview.innerHTML = '<img src="' + ev.target.result + '" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:6px;">';
            };
            reader.readAsDataURL(file);
        }
        iconInput.value = '';
        document.querySelectorAll('.icon-keuze-btn.is-actief').forEach(function(b) {
            b.classList.remove('is-actief');
            b.setAttribute('aria-checked', 'false');
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        foutBox.hidden = true;

        var naamInput = document.getElementById('af-function-name');
        if (!naamInput.value.trim()) {
            foutBox.textContent = 'Voer een naam in.';
            foutBox.hidden = false;
            naamInput.focus();
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Opslaan...';

        fetch('/api/effects/add-function', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
            body: new FormData(form)
        })
        .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
        .then(function(result) {
            if (result.ok && result.data.success) {
                location.reload();
                return;
            }
            var msg = 'Fout bij toevoegen!';
            if (result.data.errors) {
                var first = Object.values(result.data.errors)[0];
                if (Array.isArray(first) && first.length) msg = first[0];
            }
            foutBox.textContent = msg;
            foutBox.hidden = false;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Toevoegen';
        })
        .catch(function() {
            foutBox.textContent = 'Netwerkfout. Probeer opnieuw.';
            foutBox.hidden = false;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Toevoegen';
        });
    });
}

// ===== SIM.12 — Route tussen twee bestemmingen =====
(function() {
    var startBtn = document.getElementById('route-start-btn');
    var wisBtn   = document.getElementById('route-wis-btn');
    var info     = document.getElementById('route-info');
    var grid     = document.querySelector('.grid');
    if (!startBtn || !grid) return;

    var modus = false;
    var startCel = null;
    var eindCel  = null;
    var svg      = null;

    function zetInfo(t) { if (info) info.textContent = t; }

    function wisRoute() {
        if (startCel) startCel.classList.remove('route-start');
        if (eindCel)  eindCel.classList.remove('route-eind');
        startCel = null; eindCel = null;
        if (svg) { svg.remove(); svg = null; }
        wisBtn.hidden = true;
        modus = false;
        startBtn.textContent = 'Kies route';
        zetInfo('');
        localStorage.removeItem('metroRouteStart');
        localStorage.removeItem('metroRouteEnd');
    }

    function saveRoute() {
        if (startCel && eindCel) {
            localStorage.setItem('metroRouteStart', startCel.dataset.cellId || '');
            localStorage.setItem('metroRouteEnd',   eindCel.dataset.cellId  || '');
        }
    }

    function herstelRoute() {
        var s = localStorage.getItem('metroRouteStart');
        var e = localStorage.getItem('metroRouteEnd');
        if (!s || !e) return;
        var startEl = grid.querySelector('.cel[data-cell-id="' + s + '"]');
        var eindEl  = grid.querySelector('.cel[data-cell-id="' + e + '"]');
        if (!startEl || !eindEl) return;
        startCel = startEl; startCel.classList.add('route-start');
        eindCel  = eindEl;  eindCel.classList.add('route-eind');
        tekenLijn();
        wisBtn.hidden = false;
        startBtn.textContent = 'Nieuwe route';
        zetInfo('Route weergegeven tussen de twee bestemmingen.');
    }

    function tekenLijn() {
        if (!startCel || !eindCel) return;
        var g = grid.getBoundingClientRect();
        var a = startCel.getBoundingClientRect();
        var b = eindCel.getBoundingClientRect();
        var x1 = a.left - g.left + a.width / 2;
        var y1 = a.top  - g.top  + a.height / 2;
        var x2 = b.left - g.left + b.width / 2;
        var y2 = b.top  - g.top  + b.height / 2;
        if (!svg) {
            svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('class', 'route-svg-overlay');
            svg.setAttribute('aria-hidden', 'true');
            if (getComputedStyle(grid).position === 'static') grid.style.position = 'relative';
            grid.appendChild(svg);
        }
        svg.setAttribute('width',  g.width);
        svg.setAttribute('height', g.height);
        // L-vorm: eerst horizontaal, dan verticaal (geen schuine lijn)
        var path = 'M ' + x1 + ' ' + y1 + ' L ' + x2 + ' ' + y1 + ' L ' + x2 + ' ' + y2;
        svg.innerHTML =
            '<path d="'+path+'" fill="none" stroke="#2563eb" stroke-width="5" ' +
            'stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="10 6"/>' +
            '<circle cx="'+x1+'" cy="'+y1+'" r="9" fill="#16a34a"/>' +
            '<circle cx="'+x2+'" cy="'+y2+'" r="9" fill="#dc2626"/>';
    }

    startBtn.addEventListener('click', function() {
        if (modus) { wisRoute(); return; }
        wisRoute();
        modus = true;
        startBtn.textContent = 'Annuleer';
        zetInfo('Klik op een bestemming als startpunt.');
    });

    wisBtn.addEventListener('click', wisRoute);

    grid.addEventListener('click', function(e) {
        if (!modus) return;
        var cel = e.target.closest('.cel.is-bestemming');
        if (!cel) return;
        e.preventDefault();
        e.stopPropagation();
        if (!startCel) {
            startCel = cel;
            cel.classList.add('route-start');
            zetInfo('Klik nu op de eindbestemming.');
        } else if (cel === startCel) {
            return;
        } else {
            eindCel = cel;
            cel.classList.add('route-eind');
            tekenLijn();
            saveRoute();
            modus = false;
            startBtn.textContent = 'Nieuwe route';
            wisBtn.hidden = false;
            zetInfo('Route weergegeven tussen de twee bestemmingen.');
        }
    }, true);

    window.addEventListener('resize', function() { if (svg) tekenLijn(); });

    herstelRoute();
})();


// Meldingen automatisch laten verdwijnen na 4 seconden, of klik X om eerder weg
document.querySelectorAll('.melding').forEach(function(m) {
    var x = document.createElement('button');
    x.type = 'button';
    x.className = 'melding-sluit';
    x.setAttribute('aria-label', 'Sluiten');
    x.textContent = '✕';
    m.appendChild(x);

    function sluit() {
        m.style.transition = 'opacity 0.3s';
        m.style.opacity = '0';
        setTimeout(function() { m.remove(); }, 300);
    }
    x.addEventListener('click', sluit);
    setTimeout(sluit, 4000);
});


// ===== Evenementen pagina: modal openen, evenement opslaan, verwijderen, alles wissen =====
(function() {
    var openBtn   = document.getElementById('add-event-btn');
    var modal     = document.getElementById('add-event-modal');
    var sluitBtn  = document.getElementById('add-event-modal-sluit');
    var annBtn    = document.getElementById('add-event-annuleer');
    var form      = document.getElementById('add-event-form');
    var foutBox   = document.getElementById('add-event-fout');
    var clearBtn  = document.getElementById('clear-events-btn');
    var tbody     = document.getElementById('events-tbody');

    function openModal() {
        if (!modal) return;
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('is-open');
        if (foutBox) { foutBox.hidden = true; foutBox.textContent = ''; }
        var d = new Date().toISOString().slice(0, 10);
        var sd = document.getElementById('ae-start-date');
        var ed = document.getElementById('ae-end-date');
        if (sd && !sd.value) sd.value = d;
        if (ed && !ed.value) ed.value = d;
    }
    function sluitModal() {
        if (!modal) return;
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('is-open');
    }

    if (openBtn)  openBtn.addEventListener('click', openModal);
    if (sluitBtn) sluitBtn.addEventListener('click', sluitModal);

    // Toon/verberg eigen-evenement velden afhankelijk van type-keuze
    var typeSel = document.getElementById('ae-type');
    var customWrap = document.getElementById('ae-custom-velden');
    var customLabel = document.getElementById('ae-custom-label');
    function toggleCustom() {
        if (!typeSel || !customWrap) return;
        var isCustom = typeSel.value === 'custom';
        customWrap.hidden = !isCustom;
        if (customLabel) customLabel.required = isCustom;
    }
    if (typeSel) typeSel.addEventListener('change', toggleCustom);
    toggleCustom();
    if (annBtn)   annBtn.addEventListener('click', sluitModal);
    if (modal)    modal.addEventListener('click', function(e) { if (e.target === modal) sluitModal(); });

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (foutBox) { foutBox.hidden = true; foutBox.textContent = ''; }

            var data = new FormData(form);
            fetch('/events', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' },
                body: data
            })
            .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
            .then(function(res) {
                if (res.ok && res.data.success) {
                    location.reload();
                    return;
                }
                var msg = 'Fout bij toevoegen.';
                if (res.data && res.data.errors) {
                    var first = Object.values(res.data.errors)[0];
                    if (Array.isArray(first) && first.length) msg = first[0];
                } else if (res.data && res.data.message) {
                    msg = res.data.message;
                }
                if (foutBox) { foutBox.textContent = msg; foutBox.hidden = false; }
            })
            .catch(function() {
                if (foutBox) { foutBox.textContent = 'Netwerkfout. Probeer opnieuw.'; foutBox.hidden = false; }
            });
        });
    }

    // Verwijder één evenement
    if (tbody) {
        tbody.addEventListener('click', function(e) {
            var btn = e.target.closest('.remove-event-btn');
            if (!btn) return;
            if (!confirm('Dit evenement verwijderen?')) return;
            var id = btn.getAttribute('data-event-id');
            fetch('/events/' + id, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(d) { if (d.success) location.reload(); });
        });
    }

    // Alles wissen
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (!confirm('Alle evenementen wissen?')) return;
            fetch('/events/clear', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' }
            })
            .then(function(r) { return r.json(); })
            .then(function(d) { if (d.success) location.reload(); });
        });
    }
})();
