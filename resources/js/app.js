import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import $ from "jquery";
window.$ = window.jQuery = $;

import "datatables.net";                    // Core DataTables
import "datatables.net-buttons";            // Buttons
import "datatables.net-buttons/js/buttons.html5.js";
import "datatables.net-buttons/js/buttons.print.js";

import jszip from "jszip";
window.JSZip = jszip;

import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;
