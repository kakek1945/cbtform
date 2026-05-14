(function () {
    const timer = document.getElementById('exam-timer');
    const warning = document.getElementById('exam-warning');
    const formWrapper = document.getElementById('form-wrapper');
    const googleFormFrame = document.getElementById('google-form-frame');
    const submissionModal = document.getElementById('submission-modal');
    const returnDashboardButton = document.getElementById('return-dashboard-button');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!timer || !csrf || !window.examSecurity) {
        return;
    }

    const expiresAt = new Date(timer.dataset.expiresAt).getTime();
    let finished = false;
    let statusInterval = null;
    let timerInterval = null;
    let formFrameLoaded = false;
    let formSubmitDetectionArmed = false;
    let formFrameFocused = false;

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

    async function getJson(url) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            throw new Error('Request gagal.');
        }

        return response.json();
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

    async function finishAfterFormSubmission(message) {
        if (finished) {
            return;
        }

        finished = true;

        try {
            await post(timer.dataset.finishUrl);
        } catch (error) {
            // The popup still gives the student a safe way back if the network request fails.
        }

        showSubmittedModal(message);
    }

    function showSubmittedModal(message) {
        finished = true;
        window.clearInterval(statusInterval);
        window.clearInterval(timerInterval);

        if (formWrapper) {
            formWrapper.classList.add('hidden');
        }

        showWarning(message || 'Jawaban sudah terkirim. Silakan kembali ke dashboard siswa.');

        if (submissionModal) {
            submissionModal.classList.remove('hidden');
            submissionModal.classList.add('flex');
        }

    }

    async function checkSubmissionStatus() {
        if (finished || !timer.dataset.submissionStatusUrl) {
            return;
        }

        try {
            const payload = await getJson(timer.dataset.submissionStatusUrl);

            if (payload.submitted) {
                showSubmittedModal(payload.message);
            }
        } catch (error) {
            // Keep the student focused on the form if status polling is temporarily unavailable.
        }
    }

    googleFormFrame?.addEventListener('load', () => {
        if (!formFrameLoaded) {
            formFrameLoaded = true;
            window.setTimeout(() => {
                formSubmitDetectionArmed = true;
            }, 8000);

            return;
        }

        if (formSubmitDetectionArmed && formFrameFocused) {
            finishAfterFormSubmission('Jawaban Google Form sudah dikirim. Silakan kembali ke dashboard siswa.');
        }
    });

    window.addEventListener('blur', () => {
        if (document.activeElement === googleFormFrame) {
            formFrameFocused = true;
        }
    });

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

    returnDashboardButton?.addEventListener('click', () => {
        returnDashboardButton.disabled = true;
        returnDashboardButton.textContent = 'Mengalihkan...';
        window.location.href = timer.dataset.dashboardUrl;
    });

    tick();
    timerInterval = window.setInterval(tick, 1000);
    window.setTimeout(checkSubmissionStatus, 5000);
    statusInterval = window.setInterval(checkSubmissionStatus, 10000);
})();
