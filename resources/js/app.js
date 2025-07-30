// Import Flowbite components
import 'flowbite';

import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox/fancybox.css";
// Bootstrap or custom setup
import './bootstrap';
import toastr from 'toastr'
import 'toastr/build/toastr.min.css'
window.toastr = toastr

Fancybox.bind("[data-fancybox]", {
  animated: true,
  dragToClose: false,
  groupAll: true,
});

// Toastr default options (can be customized further)
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

// Optional future: import Swiper, Photoswipe, SplitType if used
// import Swiper from 'swiper';
// import PhotoSwipe from 'photoswipe';
// import SplitType from 'split-type';
