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

            let id = `${deck}_${r}_${c}`;

            html += `
                <td class="seat"
                    contenteditable="true"
                    data-row="${r}"
                    data-col="${c}"
                    data-deck="${deck}">
                    <input type="hidden" 
                        name="seat[${deck}][${r}][${c}]" 
                        id="seat_${id}">
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


export function validateSeat(td) {

    const name = td.innerText.trim();
    const deck = td.dataset.deck;
    const row = +td.dataset.row;
    const col = +td.dataset.col;

    /* ================= EMPTY CASE ================= */

    const hidden = td.querySelector('input[type="hidden"]');

    if (hidden) {
        hidden.value = name;
    }

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
    const grouped = {};

    seats.forEach(td => {
        const value = td.innerText.trim();
        if (!value) return;

        if (!grouped[value]) grouped[value] = [];
        grouped[value].push(td);
    });

    // Object.entries(grouped).forEach(([seatText, matches]) => {

    //     // 🔹 Detect role
    //     let role = 'NORMAL';
    //     if (seatText === 'E') role = 'EXIT';
    //     if (seatText === 'T') role = 'TOILET';

    //     // Reset
    //     matches.forEach(s => {
    //         removePreview(deck, +s.dataset.row, +s.dataset.col);
    //         resetSeat(s);
    //     });

    //     // ❌ EXIT / TOILET must be exactly 2 cells
    //     if ((role === 'EXIT' || role === 'TOILET') && matches.length !== 2) {
    //         alert(`${seatText} must be exactly 2 adjacent cells`);
    //         matches.forEach(clearSeat);
    //         return;
    //     }

    //     // ✅ Normal seat (single)
    //     if (role === 'NORMAL' && matches.length === 1) {
    //         mark(matches[0], 'SEATER');
    //         return;
    //     }

    //     // ❌ More than 2
    //     if (matches.length > 2) {
    //         alert(`"${seatText}" used more than twice`);
    //         matches.forEach(clearSeat);
    //         return;
    //     }

    //     const [a, b] = matches;

    //     const r1 = +a.dataset.row, c1 = +a.dataset.col;
    //     const r2 = +b.dataset.row, c2 = +b.dataset.col;

    //     const isHorizontal = (r1 === r2 && Math.abs(c1 - c2) === 1);
    //     const isVertical = (c1 === c2 && Math.abs(r1 - r2) === 1);

    //     // ❌ Not adjacent
    //     if (!isHorizontal && !isVertical) {
    //         alert(`"${seatText}" must be adjacent`);
    //         matches.forEach(clearSeat);
    //         return;
    //     }

    //     // 🔹 Decide type
    //     let type;

    //     if (role === 'EXIT') {
    //         type = isVertical ? 'EXIT_VERTICAL_SLEEPER' : 'EXIT_VERTICAL_SLEEPER';
    //     }
    //     else if (role === 'TOILET') {
    //         type = isVertical ? 'TOILET_VERTICAL_SLEEPER' : 'TOILET_VERTICAL_SLEEPER';
    //     }
    //     else {
    //         type = isVertical ? 'VERTICAL_SLEEPER' : 'SLEEPER';
    //     }

    //     // 🔹 Pick correct starting cell
    //     let primary;

    //     if (isHorizontal) {
    //         primary = c1 < c2 ? a : b;
    //     } else {
    //         primary = r1 < r2 ? a : b;
    //     }

    //     mark(primary, type);
    // });

    Object.entries(grouped).forEach(([seatText, matches]) => {

        // 🔹 Detect role
        let role = 'NORMAL';
        if (seatText === 'EXIT') role = 'EXIT';
        if (seatText === 'TOILET') role = 'TOILET';

        // Reset
        matches.forEach(s => {
            removePreview(deck, +s.dataset.row, +s.dataset.col);
            resetSeat(s);
        });

        // ✅ SINGLE CELL (Seater / Exit)
        if (matches.length === 1) {

            let type = 'SEATER';

            if (role === 'EXIT') type = 'EXIT_SEATER';

            mark(matches[0], type);
            return;
        }

        // ❌ More than 2
        if (matches.length > 2) {
            alert(`"${seatText}" used more than twice`);
            matches.forEach(clearSeat);
            return;
        }

        const [a, b] = matches;

        const r1 = +a.dataset.row, c1 = +a.dataset.col;
        const r2 = +b.dataset.row, c2 = +b.dataset.col;

        // 🔸 VERTICAL (IMPORTANT for EXIT + TOILET)
        if (c1 === c2 && Math.abs(r1 - r2) === 1) {

            let type = 'VERTICAL_SLEEPER';

            if (role === 'EXIT') type = 'EXIT_VERTICAL_SLEEPER';
            if (role === 'TOILET') type = 'TOILET_VERTICAL_SLEEPER';

            const primary = r1 < r2 ? a : b;

            mark(primary, type);
            return;
        }

        if (r1 === r2 && Math.abs(c1 - c2) === 1) {

            let type = 'SLEEPER';

            if (role === 'EXIT') type = 'EXIT_SLEEPER';
            if (role === 'TOILET') type = 'TOILET_SLEEPER';

            const primary = c1 < c2 ? a : b;

            mark(primary, type);
            return;
        }

        // 🔸 HORIZONTAL (only normal sleeper)
        if (r1 === r2 && Math.abs(c1 - c2) === 1) {

            let type = 'SLEEPER';

            const primary = c1 < c2 ? a : b;

            mark(primary, type);
            return;
        }

        alert(`"${seatText}" must be adjacent`);
        matches.forEach(clearSeat);
    });
}



/* ================= MARKING ================= */

export function mark(td, type) {

    const deck = td.dataset.deck;
    const row = +td.dataset.row;
    const col = +td.dataset.col;

    // 🔹 Detect role
    let role = 'NORMAL';
    if (type.includes('EXIT')) role = 'EXIT';
    else if (type.includes('TOILET')) role = 'TOILET';

    // 🔹 Detect layout
    let layout = 'SEATER';

    if (type.includes('VERTICAL')) layout = 'VERTICAL_SLEEPER';
    else if (type.includes('SLEEPER')) layout = 'SLEEPER';

    td.dataset.type = role;
    td.dataset.layout = layout;

    td.classList.remove('seater', 'sleeper', 'vertical-sleeper');

    if (layout === 'SEATER') td.classList.add('seater');
    if (layout === 'SLEEPER') td.classList.add('sleeper');
    if (layout === 'VERTICAL_SLEEPER') td.classList.add('vertical-sleeper');

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

    const isExit = type.includes('EXIT');
    const isToilet = type.includes('TOILET');

    // 🔹 Layout span
    if (type.includes('VERTICAL')) {
        seat.style.gridRowEnd = row + 2;
    }
    else if (type.includes('SLEEPER')) {
        seat.style.gridColumnEnd = col + 2;
    }

    // 🔥 EXIT
    if (isExit) {

        if (type.includes('VERTICAL')) {
            seat.classList.add('vertical_exit_prv');
        } 
        else if (type.includes('SLEEPER')) {
            seat.classList.add('horizontal_exit_prv'); // ✅ NEW
        }
        else {
            seat.classList.add('seat_exit_prv');
        }
    }

    // 🚽 TOILET
    else if (isToilet) {

        if (type.includes('VERTICAL')) {
            seat.classList.add('vertical_toilet_prv');
        } 
        else if (type.includes('SLEEPER')) {
            seat.classList.add('horizontal_toilet_prv'); // ✅ NEW
        }
        else {
            seat.classList.add('seat_toilet_prv');
        }
    }

    // 🪑 NORMAL
    else {
        if (type === 'SEATER') {
            seat.classList.add('seat_prv');
        }
        else if (type === 'SLEEPER') {
            seat.classList.add('sleeper_prv');
        }
        else if (type === 'VERTICAL_SLEEPER') {
            seat.classList.add('vertical_sleeper_prv');
        }
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


export function generateSeatJSON() {

    const created_by = 1;
    const tier = document.getElementById('busTier').value;

    let seats = [];

    document.querySelectorAll(".seat").forEach(td => {

        const seat = td.innerText.trim();
        const deck = td.dataset.deck;

        if (tier == "1" && deck === "UPPER") return;

        const row = +td.dataset.row;
        const col = +td.dataset.col;

        const berth_type = deck === "UPPER" ? 2 : 1;

        const role = td.dataset.type || 'NORMAL';

        const right = document.querySelector(
            `.seat[data-deck="${deck}"][data-row="${row}"][data-col="${col + 1}"]`
        );

        const bottom = document.querySelector(
            `.seat[data-deck="${deck}"][data-row="${row + 1}"][data-col="${col}"]`
        );

        if (td.dataset.skip === "true") return;

        if (!seat) {
            seats.push({
                seat_class: 0,
                berth_type,
                seat_text: null,
                row_number: row,
                col_number: col,
                created_by
            });
            return;
        }

        // Horizontal sleeper
        if (right && right.innerText.trim() === seat) {
            seats.push({
                seat_class: 2,
                berth_type,
                seat_text: seat,
                row_number: row,
                col_number: col,
                role,
                created_by
            });
            right.dataset.skip = "true";
            return;
        }

        // Vertical sleeper / exit / toilet
        if (bottom && bottom.innerText.trim() === seat) {
            seats.push({
                seat_class: 3,
                berth_type,
                seat_text: seat,
                row_number: row,
                col_number: col,
                role,
                created_by
            });
            bottom.dataset.skip = "true";
            return;
        }

        // Single seat / exit
        seats.push({
            seat_class: 1,
            berth_type,
            seat_text: seat,
            row_number: row,
            col_number: col,
            role,
            created_by
        });

    });

    return seats;
}