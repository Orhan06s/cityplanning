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

// === Helper: maak een form en submit het (voor place/remove) ===
function submitForm(action, velden) {
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

    // Aankondiging alleen bij binnenkomen van een cel (niet continu tijdens dragover)
    cel.addEventListener('dragenter', function(e) {
        e.preventDefault();
        var label = cel.getAttribute('aria-label');
        if (label) kondigAan(label);
    });

    cel.addEventListener('drop', function(e) {
        e.preventDefault();
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

    // Functie toevoegen via modal (BES.2)
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
