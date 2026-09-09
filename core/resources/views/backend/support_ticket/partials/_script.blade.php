<script>
    "use strict";
    function showFileName(input) {
        const file = input.files[0];
        const fileName = document.getElementById("file-name");
        const preview = document.getElementById("attachment-preview");

        if (file) {
            fileName.textContent = truncateFileName(file.name);
            preview.classList.remove("d-none");
        } else {
            preview.classList.add("d-none");
            fileName.textContent = "";
        }
    }

    function removeFile() {
        const input = document.getElementById("file-input");
        const preview = document.getElementById("attachment-preview");
        const fileName = document.getElementById("file-name");

        input.value = "";
        preview.classList.add("d-none");
        fileName.textContent = "";
    }

    function truncateFileName(name, maxLength = 30) {
        if (name.length <= maxLength) return name;
        const extIndex = name.lastIndexOf(".");
        const ext = extIndex !== -1 ? name.substring(extIndex) : "";
        const base = name.substring(0, extIndex);
        const visibleLength = maxLength - ext.length - 3;
        const start = base.substring(0, Math.ceil(visibleLength * 0.6));
        const end = base.substring(base.length - Math.floor(visibleLength * 0.4));
        return `${start}...${end}${ext}`;
    }
</script>