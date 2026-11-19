{{-- CSRF Token Auto-Refresh --}}
{{-- Keeps the session alive and syncs the CSRF token to the client every 5 minutes. --}}
{{-- The server no longer regenerates the token on a timer (see RefreshCsrfToken.php), --}}
{{-- so the token stays stable for the whole session and 419s are caused only by a    --}}
{{-- genuine session expiry, which is handled by the Livewire hook in the layout.      --}}
<script>
(function() {
    var REFRESH_INTERVAL = 5 * 60 * 1000; // 5 minutes – keeps session alive
    var lastHiddenAt = 0;

    function refreshCsrfToken() {
        fetch('/csrf-token', {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
        .then(function(r) {
            if (!r.ok) return null;
            return r.json();
        })
        .then(function(data) {
            if (!data || !data.token) return;

            var token = data.token;

            // 1. Update <meta name="csrf-token">
            var meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', token);

            // 2. Update all hidden _token inputs on the page
            document.querySelectorAll('input[name="_token"]').forEach(function(el) {
                el.value = token;
            });

            // 3. Axios (if used)
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
            }

            // 4. jQuery AJAX (if used)
            if (window.jQuery) {
                jQuery.ajaxSetup({ headers: { 'X-CSRF-TOKEN': token } });
            }

            // 5. Livewire v3 — update its internal CSRF token
            if (window.Livewire && typeof window.Livewire.updateCsrfToken === 'function') {
                window.Livewire.updateCsrfToken(token);
            }
        })
        .catch(function() { /* ignore – next tick will retry */ });
    }

    // Periodic keepalive
    setInterval(refreshCsrfToken, REFRESH_INTERVAL);

    // When the user returns to this tab after being away, refresh immediately
    // but only if the page was hidden for more than 60 seconds (avoid spurious fires)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            lastHiddenAt = Date.now();
        } else {
            if (Date.now() - lastHiddenAt > 60000) {
                refreshCsrfToken();
            }
        }
    });
})();
</script>
