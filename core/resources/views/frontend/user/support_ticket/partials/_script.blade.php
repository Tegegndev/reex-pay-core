<script>
    "use strict";

    function handleFileChange() {
        const input = document.getElementById("file-input");
        const preview = document.getElementById("attachment-preview");
        const fileNameSpan = document.getElementById("file-name");

        if (input.files.length > 0) {
            let name = input.files[0].name;
            fileNameSpan.textContent = truncateFileName(name, 30); // max 30 chars
            preview.classList.remove("d-none");
        } else {
            preview.classList.add("d-none");
        }
    }

    function removeFile() {
        const input = document.getElementById("file-input");
        const preview = document.getElementById("attachment-preview");
        const fileNameSpan = document.getElementById("file-name");

        input.value = "";
        preview.classList.add("d-none");
        fileNameSpan.textContent = "";
    }

    function truncateFileName(name, maxLength = 30) {
        if (name.length <= maxLength) return name;

        const extIndex = name.lastIndexOf('.');
        const ext = extIndex !== -1 ? name.substring(extIndex) : '';
        const base = name.substring(0, extIndex);

        const visibleLength = maxLength - ext.length - 3; // 3 for "..."
        const start = base.substring(0, Math.ceil(visibleLength * 0.6));
        const end = base.substring(base.length - Math.floor(visibleLength * 0.4));

        return `${start}...${end}${ext}`;
    }


</script>