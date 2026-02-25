import $ from 'jquery';

export function initLayout(rows, cols) {

    const upperPreview = document.querySelectorAll('.layout-box')[0];
    const lowerPreview = document.querySelectorAll('.layout-box')[1];

    // Left side grid
    createGrid('UPPER');
    createGrid('LOWER');

    // Right side preview
    generateAll(rows, cols, upperPreview);
    generateAll(rows, cols, lowerPreview);
}

export function generateAll(rows, cols, container) {  

    container.innerHTML = '';

    container.style.display = 'grid';
    container.style.gridTemplateColumns = `repeat(${cols}, 40px)`;
    container.style.gap = '6px';

    for (let i = 0; i < rows * cols; i++) {
        const div = document.createElement('div');
        div.className = 'preview-cell';
        container.appendChild(div);
    }
}

export function createGrid(deck) {

    let rowCount = parseInt(document.getElementById('rows').value);
    let colCount = parseInt(document.getElementById('cols').value);

    if (!rowCount || !colCount) return;

    let html = `<table class="seatGrid"><tbody>`;

    for (let r = 1; r <= rowCount; r++) {
        html += `<tr>`;
        for (let c = 1; c <= colCount; c++) {
            html += `
                <td class="seat"
                    contenteditable="true"
                    data-row="${r}"
                    data-col="${c}"
                    data-deck="${deck}">
                </td>`;
        }
        html += `</tr>`;
    }

    html += `</tbody></table>`;

    document.getElementById(deck).innerHTML = html;

    attachEvents();
}


export function attachEvents() {
        document.querySelectorAll('.seat').forEach(td => {
            td.onkeydown = e => { if (e.key === 'Enter') e.preventDefault(); };
            td.onblur = () => validateSeat(td);
        });
}

export function validateSeat(td) {
    if (!name) return;

    let deck = td.dataset.deck;

    let matches = [...document.querySelectorAll(`.seat[data-deck="${deck}"]`)]
        .filter(s => s.innerText.trim() === name);

    matches.forEach(resetSeat);

    if (matches.length === 1) {
        mark(matches[0], 'SEATER');
        return;
    }

    if (matches.length > 2) {
        alert(`Seat "${name}" used more than twice`);
        matches.forEach(clearSeat);
        return;
    }

    let [a,b] = matches;
    let r1 = +a.dataset.row, c1 = +a.dataset.col;
    let r2 = +b.dataset.row, c2 = +b.dataset.col;

    if (r1 === r2 && Math.abs(c1-c2) === 1) {
        mark(a,'SLEEPER'); mark(b,'SLEEPER');
    }
    else if (c1 === c2 && Math.abs(r1-r2) === 1) {
        mark(a,'VERTICAL_SLEEPER'); mark(b,'VERTICAL_SLEEPER');
    }
    else {
        alert(`Seat "${name}" must be adjacent`);
        matches.forEach(clearSeat);
    }
}

export function mark(td, type) {

    td.dataset.type = type;

    td.classList.remove('seater','sleeper','vertical-sleeper');

    if (type === 'SEATER') {
        td.classList.add('seater');
    }
    else if (type === 'SLEEPER') {
        td.classList.add('sleeper');
    }
    else if (type === 'VERTICAL_SLEEPER') {
        td.classList.add('vertical-sleeper');
    }

    updatePreview(
        td.dataset.deck,
        +td.dataset.row,
        +td.dataset.col,
        type
    );
}


export function resetSeat(td) {
    td.classList.remove('seater','sleeper');
    delete td.dataset.type;
}

export function clearSeat(td) {

    const deck = td.dataset.deck;
    const row  = +td.dataset.row;
    const col  = +td.dataset.col;

    removePreview(deck, row, col);

    td.innerText = '';
    resetSeat(td);
}

// function updatePreview(deck, row, col, type) {

//     const cols = parseInt(document.getElementById('cols').value);

//     const index = (row - 1) * cols + (col - 1);

//     const layoutBox = deck === 'UPPER'
//         ? document.querySelectorAll('.layout-box')[0]
//         : document.querySelectorAll('.layout-box')[1];

//     const cells = layoutBox.children;
//     const cell = cells[index];

//     if (!cell) return;

//     cell.style.visibility = 'visible';
//     cell.className = (type === 'SEATER')
//         ? 'seat_prv'
//         : 'sleeper_prv';
// }

function updatePreview(deck, row, col, type) {

    const cols = parseInt(document.getElementById('cols').value);
    const index = (row - 1) * cols + (col - 1);

    const layoutBox = deck === 'UPPER'
        ? document.querySelectorAll('.layout-box')[0]
        : document.querySelectorAll('.layout-box')[1];

    const cell = layoutBox.children[index];
    if (!cell) return;

    cell.classList.remove(
        'seat_prv',
        'sleeper_prv',
        'vertical_sleeper_prv'
    );

    if (type === 'SEATER') {
        cell.classList.add('seat_prv');
    }
    else if (type === 'SLEEPER') {
        cell.classList.add('sleeper_prv');
    }
    else if (type === 'VERTICAL_SLEEPER') {
        cell.classList.add('vertical_sleeper_prv');
    }

    cell.style.visibility = 'visible';
}

function removePreview(deck, row, col) {

    const cols = parseInt(document.getElementById('cols').value);
    const index = (row - 1) * cols + (col - 1);

    const layoutBoxes = document.querySelectorAll('.layout-box');
    const layoutBox = deck === 'UPPER'
        ? layoutBoxes[0]
        : layoutBoxes[1];

    const cell = layoutBox.children[index];
    if (!cell) return;

    cell.classList.remove('seat_prv', 'sleeper_prv');
    cell.style.visibility = 'hidden';
}

function removeSeat(td) {

    const deck = td.dataset.deck;
    const row  = +td.dataset.row;
    const col  = +td.dataset.col;

    removePreview(deck, row, col);

    td.classList.remove('seater', 'sleeper');
    delete td.dataset.type;
}

