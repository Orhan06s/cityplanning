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
