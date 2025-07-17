// Import Flowbite components
import 'flowbite';


// Bootstrap or custom setup
import './bootstrap';
import toastr from 'toastr'
import 'toastr/build/toastr.min.css'
window.toastr = toastr


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
