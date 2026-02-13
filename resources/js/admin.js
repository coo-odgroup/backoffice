import $ from 'jquery';
window.$ = window.jQuery = $;

import 'bootstrap';

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

// Import your custom scripts LAST
import { loadDataTable } from './common-datatable';
window.loadDataTable = loadDataTable;

import * as commonAjax from './commonAjax';
window.commonAjax = commonAjax; 

console.log('AdminLTE assets loaded');
