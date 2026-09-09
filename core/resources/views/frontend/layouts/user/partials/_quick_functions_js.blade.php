<script>
    "use strict";
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById("{{ $btnId ?? 'quickFunctionBtn' }}");
        const dropdown = document.getElementById("{{ $dropdownId ?? 'quickFunctionDropdown' }}");
        const moreBtn = document.getElementById('qfMoreBtn_{{ $dropdownId ?? 'default' }}');
        const moreGrid = document.getElementById('quickFunctionMoreGrid_{{ $dropdownId ?? 'default' }}');
        const moreLabel = document.getElementById('qfMoreBtnLabel_{{ $dropdownId ?? 'default' }}');
        moreGrid.style.maxHeight = '0';
        moreGrid.style.overflow = 'hidden';
        moreGrid.style.transition = 'max-height 0.35s cubic-bezier(.4,0,.2,1)';
        let expanded = false;
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
        moreBtn.addEventListener('click', function() {
            expanded = !expanded;
            moreBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            if (expanded) {
                moreGrid.style.maxHeight = '300px';
                moreLabel.innerHTML = '<i class="fa-solid fa-chevron-up me-1"></i> {{ __('Less Functions') }}';
            } else {
                moreGrid.style.maxHeight = '0';
                moreLabel.innerHTML = '<i class="fa-solid fa-chevron-down me-1"></i> {{ __('More Functions') }}';
            }
        });
    });
</script>