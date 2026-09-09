"use strict";

$(function () {
    // Initialize Tooltips
    $('[data-coreui-toggle="tooltip"]').each(function () {
        new coreui.Tooltip(this);
    });

    // Header Shadow on Scroll
    const $header = $('header.header');
    $(document).on('scroll', function () {
        $header.toggleClass('shadow-sm', $(document).scrollTop() > 0);
    });

    // Initialize Tagify
    const $tagInput = $('.tags-evs');
    if ($tagInput.length) {
        new Tagify($tagInput[0]);
    }

    // Initialize Summernote
    initializeSummernote('.summernote');

    // Initialize Clipboard.js
    const clipboard = new ClipboardJS('.copyNow');
    clipboard.on('success', function (e) {
        const button = e.trigger;
        const tooltip = coreui.Tooltip.getInstance(button);
        if (tooltip) {
            tooltip.setContent({'.tooltip-inner': 'Copied!'});
            tooltip.show();
        }
    });

    // Auto Slugify Title Inputs
    $(document).on('input', '.title-to-slug', function () {
        const $this = $(this);
        const $target = $($this.data('slug-target'));
        $target.val(slugify($this.val()));
    });

    // Scroll to Active Sidebar Menu Item (Once)
    const $activeSidebarItem = $('.sidebar .nav-link.active');
    if ($activeSidebarItem.length) {
        $activeSidebarItem[0].scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
    }


});
document.addEventListener("DOMContentLoaded", () => {
    // 1️⃣ Override the internal _setActiveLink method so it does nothing
    if (coreui.Navigation && coreui.Navigation.prototype) {
        coreui.Navigation.prototype._setActiveLink = function() {
            /* no-op */
        };
    }

    // 2️⃣ Now (re)initialize your navigation component as usual
    document.querySelectorAll('[data-coreui="navigation"]')
        .forEach(el => {
            coreui.Navigation.getOrCreateInstance(el, {
                // you can still control collapse behavior…
                groupsAutoCollapse: true,
                // …but activeLinksExact no longer matters, since _setActiveLink is empty
            });
        });
});
