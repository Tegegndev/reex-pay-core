<script id="field-template" type="text/template">
    @include('backend.kyc.template.partials._fields', ['key' => '__INDEX__'])
</script>

<script>
    'use strict';

    let fieldIndex = 0;

    // Function to get and increase index per modal instance
    function getNextFieldIndex(modalSelector) {
        const $fieldsContainer = $(modalSelector).find('.fields-container');
        const count = $fieldsContainer.find('.field-item').length;
        return count || 0;
    }

    // Event delegation: Add new field
    $(document).on('click', '.add-new-field', function () {
        const $modal = $(this).closest('.modal');
        const $fieldsContainer = $modal.find('.fields-container');
        const $fieldTemplate = $('#field-template').html();

        fieldIndex = getNextFieldIndex($modal);
        const template = $fieldTemplate.replace(/__INDEX__/g, fieldIndex);
        $fieldsContainer.append(template);
        fieldIndex++;
    });

    // Event delegation: Remove field
    $(document).on('click', '.remove-field', function () {
        $(this).closest('.field-item').remove();
    });

    // Optional: Rebind modal form functionality if using AJAX
    editFormByModal('editTemplateModal', 'edit-template-form');
</script>