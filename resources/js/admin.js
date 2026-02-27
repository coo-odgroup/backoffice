import $ from 'jquery';
window.$ = window.jQuery = $;

import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import 'datatables.net';
import 'datatables.net-bs5';

import 'datatables.net-buttons';
import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5';
import 'datatables.net-buttons/js/buttons.print';

import JSZip from 'jszip';
window.JSZip = JSZip;

import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
pdfMake.vfs = pdfFonts.pdfMake.vfs;

import select2 from 'select2';
select2($); 

import '@fortawesome/fontawesome-free/js/all';

$(document).ajaxStart(function () {    
    $('#pageLoader').removeClass('d-none').addClass('d-flex');
});

$(document).ajaxStop(function () {
    $('#pageLoader').removeClass('d-flex').addClass('d-none');
});

window.addEventListener('load', function () {

    const loader = document.getElementById('pageLoader');

    // console.log("Page fully loaded, hiding loader...");

    if (loader) {
        // console.log("Fading out loader...");
        loader.style.opacity = '0';
        loader.style.transition = 'opacity 0.3s ease';

        setTimeout(() => {
            loader.style.display = 'none';
        }, 1000);
    }

});


document.addEventListener('DOMContentLoaded', function () {
    
   
    const alerts = document.querySelectorAll('.alert');

    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 10000);
    });

    const btnToggle = document.getElementById('menu-toggle');

    btnToggle.addEventListener('click', function () {
         console.log("Toggle clicked");
    });




    //For Toggle Filter Button
    const btn = document.getElementById('btnToggleFilter');
    const filterBox = document.getElementById('filterBox');

    if (!btn || !filterBox) return; 

    btn.addEventListener('click', function () {

        if (filterBox.classList.contains('d-none')) {
            filterBox.classList.remove('d-none');
        } else {
            filterBox.classList.add('d-none');
        }

        btn.classList.toggle('active-filter');

        if (btn.classList.contains('active-filter')) {

            btn.classList.remove('btn-primary');
            btn.classList.add('btn-danger');
            btn.innerHTML = '<i class="fa-solid fa-xmark me-1"></i> Close';

        } else {

            btn.classList.remove('btn-danger');
            btn.classList.add('btn-primary');
            btn.innerHTML = '<i class="fa-solid fa-magnifying-glass me-1"></i> Filter';
        }

    });

});


import * as validation from './validation';
window.validator = validation;

// Import your custom scripts LAST
import { loadDataTable } from './common-datatable';
window.loadDataTable = loadDataTable;

import * as commonAjax from './commonAjax';
window.commonAjax = commonAjax; 
window.initTooltips = commonAjax.initTooltips;
window.actionRec = commonAjax.actionRec;
window.initCharCounter = commonAjax.initCharCounter;

$(document).on('click', '.btn-view-log', function() {

    let table = $(this).data('table');
    let id    = $(this).data('id');

    commonAjax.viewLogs(table, id);
});

import * as seatAjax from './seat-layout';
window.seatAjax = seatAjax; 

import ClassicEditor from '@ckeditor/ckeditor5-build-classic';

// Global CKEditor registry
window.ckEditors = {};

/**
 * Initialize CKEditor anywhere
 * @param {string} selector
 * @param {object} options
 */
window.initCkEditor = function (selector, options = {}) {

    const element = document.querySelector(selector);
    if (!element) return;

    ClassicEditor
        .create(element, {
            toolbar: [
                'heading',
                '|',
                'bold',
                'italic',
                'underline',
                'link',
                'bulletedList',
                'numberedList',
                '|',
                'blockQuote',
                'insertTable',
                '|',
                'undo',
                'redo'
            ],
            ...options
        })
        .then(editor => {
            window.ckEditors[selector] = editor;
        })
        .catch(error => {
            console.error('CKEditor init error:', error);
        });
};
