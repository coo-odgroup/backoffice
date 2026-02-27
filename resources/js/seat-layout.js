import $ from 'jquery';

/* ================= INIT ================= */

export function initLayout(rows, cols) {

    const layoutBoxes = document.querySelectorAll('.layout-box');

    // Reset preview containers
    layoutBoxes.forEach(box => {
        box.innerHTML = '';
        box.style.display = 'grid';
        box.style.gridTemplateColumns = `repeat(${cols}, 40px)`;
        box.style.gridAutoRows = '40px';
        box.style.gap = '6px';
    });

    createGrid('UPPER', rows, cols);
    createGrid('LOWER', rows, cols);
}

/* ================= LEFT GRID ================= */

export function createGrid(deck, rows, cols) {

    let html = `<table class="seatGrid"><tbody>`;

    for (let r = 1; r <= rows; r++) {
        html += `<tr>`;
        for (let c = 1; c <= cols; c++) {
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

/* ================= EVENTS ================= */

export function attachEvents() {

    document.querySelectorAll('.seat').forEach(td => {

        td.onkeydown = e => {
            if (e.key === 'Enter') e.preventDefault();
        };

        td.onblur = () => validateSeat(td);
    });
}

/* ================= VALIDATION ================= */

// export function validateSeat(td) {

//     const name = td.innerText.trim();
//     if (!name) return;

//     const deck = td.dataset.deck;

//     const matches = [...document.querySelectorAll(`.seat[data-deck="${deck}"]`)]
//         .filter(s => s.innerText.trim() === name);

//     // Reset existing preview
//     matches.forEach(s => {
//         removePreview(deck, +s.dataset.row, +s.dataset.col);
//         resetSeat(s);
//     });

//     /* ---------- SINGLE SEAT ---------- */

//     if (matches.length === 1) {
//         mark(matches[0], 'SEATER');
//         return;
//     }

//     /* ---------- MORE THAN TWO ---------- */

//     if (matches.length > 2) {
//         alert(`Seat "${name}" used more than twice`);
//         matches.forEach(clearSeat);
//         return;
//     }

//     /* ---------- TWO SEATS ---------- */

//     const [a, b] = matches;

//     const r1 = +a.dataset.row, c1 = +a.dataset.col;
//     const r2 = +b.dataset.row, c2 = +b.dataset.col;

//     // Horizontal sleeper
//     if (r1 === r2 && Math.abs(c1 - c2) === 1) {

//         const primary = c1 < c2 ? a : b;

//         mark(primary, 'SLEEPER');
//         return;
//     }

//     // Vertical sleeper
//     if (c1 === c2 && Math.abs(r1 - r2) === 1) {

//         const primary = r1 < r2 ? a : b;

//         mark(primary, 'VERTICAL_SLEEPER');
//         return;
//     }

//     alert(`Seat "${name}" must be adjacent`);
//     matches.forEach(clearSeat);
// }

export function validateSeat(td) {

    const name = td.innerText.trim();
    const deck = td.dataset.deck;
    const row = +td.dataset.row;
    const col = +td.dataset.col;

    /* ================= EMPTY CASE ================= */

    if (!name) {

        removePreview(deck, row, col);
        resetSeat(td);

        // 🔥 Re-check all seats in this deck
        reValidateDeck(deck);

        return;
    }

    reValidateDeck(deck);
}


function reValidateDeck(deck) {

    const seats = [...document.querySelectorAll(`.seat[data-deck="${deck}"]`)];

    // Group seats by number
    const grouped = {};

    seats.forEach(td => {
        const value = td.innerText.trim();
        if (!value) return;

        if (!grouped[value]) grouped[value] = [];
        grouped[value].push(td);
    });

    Object.values(grouped).forEach(matches => {

        // Clear old state
        matches.forEach(s => {
            removePreview(deck, +s.dataset.row, +s.dataset.col);
            resetSeat(s);
        });

        if (matches.length === 1) {
            mark(matches[0], 'SEATER');
            return;
        }

        if (matches.length > 2) {
            alert(`Seat "${matches[0].innerText}" used more than twice`);
            matches.forEach(clearSeat);
            return;
        }

        const [a, b] = matches;

        const r1 = +a.dataset.row, c1 = +a.dataset.col;
        const r2 = +b.dataset.row, c2 = +b.dataset.col;

        // Horizontal
        if (r1 === r2 && Math.abs(c1 - c2) === 1) {
            // Mark BOTH seats visually
            a.classList.add('sleeper');
            b.classList.add('sleeper');

            // Choose one for preview rendering
            const primary = c1 < c2 ? a : b;
            updatePreview(deck, +primary.dataset.row, +primary.dataset.col, 'SLEEPER');
            return;
        }

        // Vertical
        if (c1 === c2 && Math.abs(r1 - r2) === 1) {
            a.classList.add('vertical-sleeper');
            b.classList.add('vertical-sleeper');

            const primary = r1 < r2 ? a : b;
            updatePreview(deck, +primary.dataset.row, +primary.dataset.col, 'VERTICAL_SLEEPER');
            return;
        }

        alert(`Seat "${a.innerText}" must be adjacent`);
        matches.forEach(clearSeat);
    });
}

/* ================= MARKING ================= */

export function mark(td, type) {

    const deck = td.dataset.deck;
    const row = +td.dataset.row;
    const col = +td.dataset.col;

    td.dataset.type = type;

    td.classList.remove('seater', 'sleeper', 'vertical-sleeper');

    if (type === 'SEATER') {
        td.classList.add('seater');
    }
    else if (type === 'SLEEPER') {
        td.classList.add('sleeper');
    }
    else if (type === 'VERTICAL_SLEEPER') {
        td.classList.add('vertical-sleeper');
    }

    updatePreview(deck, row, col, type);
}

/* ================= RESET ================= */

export function resetSeat(td) {
    td.classList.remove('seater', 'sleeper', 'vertical-sleeper');
    delete td.dataset.type;
}

export function clearSeat(td) {
    td.innerText = '';
    resetSeat(td);
}

/* ================= PREVIEW SYSTEM ================= */

function updatePreview(deck, row, col, type) {

    const layoutBoxes = document.querySelectorAll('.layout-box');
    const layoutBox = deck === 'UPPER' ? layoutBoxes[0] : layoutBoxes[1];

    const seat = document.createElement('div');
    seat.className = 'preview-seat';

    seat.style.gridColumnStart = col;
    seat.style.gridRowStart = row;

    if (type === 'SEATER') {
        seat.classList.add('seat_prv');
    }

    else if (type === 'SLEEPER') {
        seat.classList.add('sleeper_prv');
        seat.style.gridColumnEnd = 'span 2';
    }

    else if (type === 'VERTICAL_SLEEPER') {
        seat.classList.add('vertical_sleeper_prv');
        seat.style.gridRowEnd = 'span 2';
    }

    layoutBox.appendChild(seat);
}

function removePreview(deck, row, col) {

    const layoutBoxes = document.querySelectorAll('.layout-box');
    const layoutBox = deck === 'UPPER' ? layoutBoxes[0] : layoutBoxes[1];

    layoutBox.querySelectorAll('.preview-seat').forEach(el => {
        const r = parseInt(el.style.gridRowStart);
        const c = parseInt(el.style.gridColumnStart);

        if (r === row && c === col) {
            el.remove();
        }
    });
}