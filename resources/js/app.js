// Import Flowbite components
import 'flowbite';


// Bootstrap or custom setup
import './bootstrap';



// Optional: use globally if needed in Blade
window.Alpine = Alpine
window.toastr = toastr

// Init Alpine
Alpine.start()

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
