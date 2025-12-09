// Import Flowbite components
import 'flowbite';
import moment from "moment";

import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

import Highcharts from 'highcharts';
import Exporting from 'highcharts/modules/exporting';
import ExportData from 'highcharts/modules/export-data';
import OfflineExporting from 'highcharts/modules/offline-exporting';

window.Highcharts = Highcharts;

// Initialize modules correctly
Exporting?.default?.(Highcharts);
ExportData?.default?.(Highcharts);
OfflineExporting?.default?.(Highcharts);

window.moment = moment;
window.jQuery.moment = moment;

Alpine.start();

import ZoomVideo from "@zoom/videosdk";

window.ZoomVideo = ZoomVideo;

import './videosdk-main.js';

import tinymce from 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom/model';

import "daterangepicker/daterangepicker.css";

// Plugins
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/code';
import 'tinymce/plugins/table';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/autoresize';

import 'tinymce/skins/ui/oxide/skin.css';
import 'tinymce/skins/content/default/content.css';
import 'tinymce/skins/content/default/content.min.css';


import toastr from 'toastr'
import 'toastr/build/toastr.min.css'
window.toastr = toastr

Fancybox.bind("[data-fancybox]", {
    animated: true,
    dragToClose: false,
    groupAll: true,
});

toastr.options = {
    closeButton: true,
    progressBar: true,
    timeOut: "5000",
    extendedTimeOut: "1000",
    positionClass: "toast-top-right",
    showDuration: "300",
    hideDuration: "1000",
    showMethod: "fadeIn",
    hideMethod: "fadeOut"
}

document.addEventListener('DOMContentLoaded', async () => {
    const { default: daterangepicker } = await import("daterangepicker");

    $(".date-range").each(function () {
        var $this = $(this);

        var ranges = {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        };

        $this.daterangepicker({
            timePicker: false,
            autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD',
                cancelLabel: 'Clear',
                direction: $('html').attr('dir') === 'rtl' ? 'rtl' : 'ltr'
            },
            ranges: ranges,
            opens: $('html').attr('dir') === 'rtl' ? 'left' : 'right',
            drops: 'down',
        });

        $this.on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $this.on('cancel.daterangepicker', function (ev, picker) {
            $(this).val('');
        });
    });


    document.querySelectorAll('.tinymce-editor').forEach(function (el) {
        tinymce.init({
            target: el,
            directionality: el.getAttribute('dir') === 'rtl' ? 'rtl' : 'ltr',
            height: 400,
            license_key: 'gpl',
            toolbar: 'undo redo | bold italic underline removeformat | alignleft aligncenter alignright | link | bullist numlist | outdent indent | blockquote | table | code preview',
            plugins: 'preview directionality code lists link table advlist',
            menubar: true,
            branding: false,
            statusbar: true,
            base_url: '/tinymce', // we'll map this path
            suffix: '.min',

            setup: function (editor) {
                editor.on('change', function () {
                    editor.save(); // Update textarea value
                });
            },
        });

    });

    // tinymce.init({
    // selector: '.tinymce-editor',
    // license_key: 'gpl',
    // plugins: 'link image code table lists autoresize',
    // toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image table | code',
    // height: 400,
    // menubar: false,
    // branding: false,
    // setup: function (editor) {
    //     editor.on('change', function () {
    //         editor.save(); // Update textarea value
    //     });
    // },


    // });
});

// window.startZoomVideo = async function (data, username) {
 
//     const client = ZoomVideo.createClient();
//     await client.init("en-US", "CDN");

//     // ✅ Correct join order
//     await client.join(data.meeting_number, data.signature, username, '');

//     const stream = client.getMediaStream();
//     await stream.startVideo();

//     const container = document.getElementById('videoContainer');
//     container.innerHTML = ''; // clear previous video

//     const videoElement = document.createElement("video");
//     videoElement.id = "self-video";
//     videoElement.autoplay = true;
//     videoElement.muted = true;
//     videoElement.playsInline = true;
//     videoElement.style.width = "400px";
//     videoElement.style.height = "300px";

//     container.appendChild(videoElement);

//     const userId = client.getCurrentUserInfo().userId;
//     await stream.attachVideo(userId, videoElement); // ✅ Correct self-view method

//     document.getElementById('videoArea').classList.remove('hidden');

//     document.getElementById('leaveBtn').addEventListener('click', async () => {
//         await stream.stopVideo();
//         await stream.stopAudio();
//         await client.leave();
//         document.getElementById('videoArea').classList.add('hidden');
//     });
// };




    const sidebar = document.getElementById('sidebar');
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');

    if (openBtn && sidebar) {
        openBtn.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
        });
    }

    if (closeBtn && sidebar) {
        closeBtn.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });
    }


    

 (function () {
    // cached DOM refs
    const openBtn = document.getElementById('btnMenuOpen');
    const closeBtn = document.getElementById('btnMenuClose');
    const panel = document.getElementById('mobileNavPanel');
    const overlay = document.getElementById('menuOverlay');

    if (!openBtn || !closeBtn || !panel) {
      console.error('Menu script: required elements not found. IDs must match: btnMenuOpen, btnMenuClose, mobileNavPanel.');
      return;
    }

    function openMenu() {
      panel.classList.remove('-translate-x-full');
      panel.setAttribute('aria-hidden', 'false');
      openBtn.setAttribute('aria-expanded', 'true');
      if (overlay) overlay.classList.remove('hidden');
      // prevent body scroll when menu open on mobile
      document.documentElement.classList.add('overflow-hidden');
      document.body.classList.add('overflow-hidden');
    }

    function closeMenu() {
      panel.classList.add('-translate-x-full');
      panel.setAttribute('aria-hidden', 'true');
      openBtn.setAttribute('aria-expanded', 'false');
      if (overlay) overlay.classList.add('hidden');
      document.documentElement.classList.remove('overflow-hidden');
      document.body.classList.remove('overflow-hidden');
    }

    openBtn.addEventListener('click', openMenu);
    closeBtn.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);

    // close with ESC key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeMenu();
    });

    // Optional: close on window resize if desktop breakpoint reached
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1280) { // xl breakpoint default 1280px
        // make sure panel is visible in desktop layout (Tailwind handles xl:translate-x-0)
        panel.classList.remove('-translate-x-full');
        panel.setAttribute('aria-hidden', 'false');
        if (overlay) overlay.classList.add('hidden');
        document.documentElement.classList.remove('overflow-hidden');
        document.body.classList.remove('overflow-hidden');
      } else {
        // ensure hidden by default on small screens
        if (!panel.classList.contains('-translate-x-full') && openBtn.getAttribute('aria-expanded') !== 'true') {
          panel.classList.add('-translate-x-full');
          panel.setAttribute('aria-hidden', 'true');
        }
      }
    });
  })();









