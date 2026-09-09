/**
 * Notify the user with a message of a certain type.
 *
 * @param {string} type - The type of the notification.
 * @param {string} message - The message to display.
 */
function notifyEvs(type, message) {
    "use strict";

    // Set the title based on the type
    let title = type;

    // Create a new notification with specified properties
    new Notify({
        status: type,
        title: title.charAt(0).toUpperCase() + title.slice(1),
        text: message,
        effect: 'slide',
        speed: 500,
        customClass: '',
        customIcon: '',
        showIcon: true,
        showCloseButton: true,
        autoclose: true,
        autotimeout: 5000,
        gap: 20,
        distance: 20,
        type: 1,
        position: 'right top',
        customWrapper: '',
    });
}

// Function to disable the submit button on form submission
function disableSubmitButton(form, message = 'Processing...') {
    const submitButton = form.querySelector('.submit-btn');
    if (submitButton) {
        submitButton.disabled = true; // Disable the button
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span> ' + message + ''; // Optional: Show processing state
    }
}


function validateNumber(value) {
    "use strict";
    const pattern = /^[0-9]*$/; // Allow only numbers
    return pattern.test(value) ? value : value.replace(/[^0-9]/g, '');
}

function validateDouble($value) {
    "use strict";
    return $value.replace(/[^0-9.]/g, '')
        // Remove any additional decimal points.
        .replace(/(\..*?)\..*/g, '$1');
}

function readURL(input, imagePreview) {
    'use strict';
    // Check if there is a selected file
    if (input.files && input.files[0]) {
        // Create a new FileReader
        var reader = new FileReader();
        // Define the onload event
        reader.onload = function (e) {
            // Set the background image of the image preview element
            imagePreview.css('background-image', 'url(' + e.target.result + ')');
            // Hide the image preview and then fade it in
            imagePreview.hide().fadeIn(400);
        };
        // Read the data URL of the selected file
        reader.readAsDataURL(input.files[0]);
    }
}

// Function to handle image preview for dynamically added elements
handleImagePreview = function handleImagePreview() {
    'use strict';
    var hostname = window.location.hostname;
    $(".imageUpload").change(function () {
        var previewId = $(this).data("preview-id");
        var imagePreview = $("#" + previewId);
        readURL(this, imagePreview);
    });
    $(".imageRemove").on('click', function (event) {
        var previewId = $(this).prev().data("preview-id");
        var imagePreview = $("#" + previewId);

        // Change to default placeholder image using the hostname
        imagePreview.css('background-image', 'url(http://' + hostname + '/general/static/default/placeholder.png)');
        // Set value to indicate removal
        var imageInput = $("#" + previewId + "-remove");
        imageInput.val('coevs-remove');

        var imageNameInput = $("#" + previewId + "_upload");
        imageInput.attr('name', imageNameInput.attr('name'));
    });
}
handleImagePreview()

$(document).on('click', '.delete', ({target}) => {
    // Ensure we're getting the closest .delete button element
    const url = $(target).closest('.delete').data('url');

    // Check if the URL exists
    if (url) {
        // Set the action attribute of the #delete-form-modal element to the retrieved URL
        $('#delete-form-modal').attr('action', url);

        // Display the #delete_modal modal
        $('#delete_modal').modal('show');
    } else {
        console.error('URL not found for the delete action.');
    }
});


function tooltipTriger() {
    'use strict';

    // Remove all tooltip DOM manually (prevent leftover ghost tooltips)
    document.querySelectorAll('.tooltip').forEach(el => el.remove());

    const tooltipTriggerList = document.querySelectorAll('.modal-tooltip');
    tooltipTriggerList.forEach(el => {
        const existing = coreui.Tooltip.getInstance(el);
        if (existing) {
            existing.dispose();
        }
        new coreui.Tooltip(el);

    });
}

function initializeSummernote(selector) {
    $(selector).summernote({
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture']],
            ['view', ['help']],
        ],
        styleTags: ['p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        placeholder: 'Write Your Message',
        height: 200,
        focus: true,
        callbacks: {
            onImageUpload: function (files) {
                for (let i = 0; i < files.length; i++) {
                    uploadImageToServer(files[i], $(this));
                }
            },
            onMediaDelete: function (target) {
                deleteImageFromServer(target[0].src);
            }
        }
    });

    // Apply custom styles to editable content
    $('.note-editable').css('font-weight', '400');
}


// Modal Dynamic Content Loading
editFormByModal = function (modalShowId, modalDataAppendId, isFile = true, tooltip = false) {
    const $modal = $('#' + modalShowId);
    const $modalContent = $('#' + modalDataAppendId);

    $(document).on('click', '.edit-modal', function () {
        const url = $(this).data('edit-url');
        const loadingHtml = `
            <div class="d-flex justify-content-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;

        $modal.modal('show');
        $modalContent.html(loadingHtml);

        $.get(url, function (data) {
            $modalContent.html(data);
            initializeSummernote($modalContent.find('.summernote'));

            if (tooltip) tooltipTriger();
            if (isFile) handleImagePreview();
        });
    });
};

function dayMap(day) {
    if (typeof day === "string" && isNaN(Number(day))) {
        return day;
    }
    const days = {
        1: 'Sunday',
        2: 'Monday',
        3: 'Tuesday',
        4: 'Wednesday',
        5: 'Thursday',
        6: 'Friday',
        7: 'Saturday'
    };
    const dayNum = Number(day);
    return days[dayNum] || day;
}

function slugify(text) {
    return text.toString().normalize('NFD')  // unicode normalize
        .replace(/[\u0300-\u036f]/g, '')     // remove accents
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')        // remove non-alphanum
        .trim()
        .replace(/\s+/g, '-')                // replace spaces with -
        .replace(/-+/g, '-');                // remove multiple hyphens
}

function uploadImageToServer(file, editor) {
    let formData = new FormData();
    formData.append('file', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    $.ajax({
        url: window.location.origin + '/summernote/image-upload',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            editor.summernote('insertImage', response.url);
        },
        error: function (xhr) {
            console.error('Image upload failed.', xhr.responseText);
        }
    });
}

function deleteImageFromServer(imageUrl) {
    $.ajax({
        url: window.location.origin + '/summernote/image-delete',
        type: 'POST',
        data: {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            url: imageUrl
        },
        error: function (xhr) {
            console.error('Image delete failed.', xhr.responseText);
        }
    });
}
