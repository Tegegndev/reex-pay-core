@if(setting('screen_lock'))
    <script>
        $(document).ready(function () {

            'use strict';

            let inactivityTime = function () {
                let time;
                window.onload = resetTimer;
                document.onmousemove = resetTimer;
                document.onkeypress = resetTimer;

                function lockScreen() {
                    let lokUrl = `{{ route('admin.lock') }}`;
                    let url = `{{ route('admin.lock-screen.show') }}`;

                    $.get(lokUrl, function() {
                        window.location.href = url;
                    });
                }

                function resetTimer() {
                    clearTimeout(time);
                    time = setTimeout(lockScreen, {{ setting('screen_lock_time') }} * 60 * 1000); // 5 minutes
                }
            };

            inactivityTime();
        });
    </script>
@endif
<script>
    window.APP_DEMO = @json(config('app.demo'));
</script>
