// Import Flowbite components
import 'flowbite';

import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
// Bootstrap or custom setup
import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import tinymce from 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom/model';

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

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.tinymce-editor').forEach(function(el) {
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