(function () {
    const timer = document.getElementById('exam-timer');
    const warning = document.getElementById('exam-warning');
    const formWrapper = document.getElementById('form-wrapper');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!timer || !csrf || !window.examSecurity) {
        return;
    }

    const expiresAt = new Date(timer.dataset.expiresAt).getTime();
    let finished = false;

    function showWarning(message) {
        if (!warning) {
            return;
        }

        warning.textContent = message;
        warning.classList.remove('hidden');
    }

    function format(seconds) {
        const safe = Math.max(0, seconds);
        const hours = String(Math.floor(safe / 3600)).padStart(2, '0');
        const minutes = String(Math.floor((safe % 3600) / 60)).padStart(2, '0');
        const secs = String(safe % 60).padStart(2, '0');

        return `${hours}:${minutes}:${secs}`;
    }

    async function post(url) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
        });
    }

    async function finishByTimeout() {
        if (finished) {
            return;
        }

        finished = true;
        if (formWrapper) {
            formWrapper.classList.add('hidden');
        }
        showWarning('Waktu ujian habis. Sistem sedang mengarahkan ke halaman selesai.');

        try {
            const response = await post(timer.dataset.finishUrl);
            const payload = await response.json();
            window.location.href = payload.redirect || timer.dataset.finishedUrl;
        } catch (error) {
            window.location.href = timer.dataset.finishedUrl;
        }
    }

    function tick() {
        const remaining = Math.ceil((expiresAt - Date.now()) / 1000);
        timer.textContent = format(remaining);
        timer.classList.toggle('bg-amber-600', remaining <= 300 && remaining > 60);
        timer.classList.toggle('bg-red-700', remaining <= 60);

        if (remaining <= 0) {
            finishByTimeout();
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden && !finished) {
            post(window.examSecurity.tabSwitchUrl);
            showWarning('Peringatan: aktivitas pindah tab tercatat oleh sistem.');
        }
    });

    document.addEventListener('contextmenu', (event) => event.preventDefault());
    document.addEventListener('keydown', (event) => {
        const key = event.key.toLowerCase();
        const blocked = event.key === 'F12'
            || (event.ctrlKey && ['c', 'v', 'u', 'x', 's', 'p'].includes(key));

        if (blocked) {
            event.preventDefault();
            showWarning('Shortcut ini dinonaktifkan selama ujian.');
        }
    });

    tick();
    window.setInterval(tick, 1000);
})();
