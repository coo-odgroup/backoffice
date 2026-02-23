import $ from 'jquery';

window.generateAll = function () {
    createGrid('LOWER');
    createGrid('UPPER');
};

export function createGrid(deck) {
    let rowCount = parseInt(document.getElementById('rows').value);
    let colCount = parseInt(document.getElementById('cols').value);

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

    let name = td.innerText.trim();
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

export function mark(td,type) {
    td.dataset.type = type;
    td.classList.add(
        type === 'SEATER' ? 'seater' :
        type === 'SLEEPER' ? 'sleeper-h' : 'sleeper-v'
    );
}

export function resetSeat(td) {
    td.classList.remove('seater','sleeper-h','sleeper-v');
    delete td.dataset.type;
}

export function clearSeat(td) {
    td.innerText = '';
    resetSeat(td);
}