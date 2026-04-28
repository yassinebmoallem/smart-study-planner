

async function toggleSession(sessionId, done) {
    try {
        const res = await fetch('toggle_session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ session_id: sessionId, done: done })
        });
        const data = await res.json();
        if (!data.ok) return;

        const row = document.getElementById('session-' + sessionId);
        if (row) {
            row.classList.toggle('done', done === 1);
            const cb = row.querySelector('.session-cb');
            if (cb) cb.checked = done === 1;
            let doneBadge = row.querySelector('.tag-done');
            let missedBadge = row.querySelector('.tag-missed');
            if (done === 1) {
                if (!doneBadge) {
                    doneBadge = document.createElement('span');
                    doneBadge.className = 'tag-done';
                    doneBadge.textContent = '✓ Fait';
                    row.appendChild(doneBadge);
                }
                if (missedBadge) missedBadge.remove();
            } else {
                if (doneBadge) doneBadge.remove();
            }
        }

        const card = document.querySelector(`[data-session="${sessionId}"]`);
        if (card) {
            card.classList.toggle('done', done === 1);
            const check = card.querySelector('.today-check');
            if (check) check.textContent = done === 1 ? '✓' : '';
        }

        updateProgressCircle(data);

    } catch (err) { console.error('toggleSession error:', err); }
}

function updateProgressCircle(data) {
    if (!data || data.progress === undefined) return;
    const pctEl = document.querySelector('.plan-circle-pct');
    if (pctEl) pctEl.textContent = data.progress + '%';
    const circle = document.querySelector('.plan-circle-wrap circle:last-child');
    if (circle) {
        const dash = Math.round(226.2 * data.progress / 100);
        circle.setAttribute('stroke-dasharray', `${dash} 226.2`);
    }
    const statVals = document.querySelectorAll('.plan-stat-val');
    if (statVals[1]) statVals[1].textContent = data.done_count;
    if (statVals[2]) statVals[2].textContent = (parseInt(statVals[0]?.textContent || 0) - data.done_count);
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.kpi-card, .subject-card, .today-item, .ss-card').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(12px)';
        setTimeout(() => {
            el.style.transition = 'opacity .35s ease, transform .35s ease';
            el.style.opacity = '1';
            el.style.transform = 'none';
        }, 60 + i * 50);
    });

    document.querySelectorAll('.progress-fill, .s-bar-fill, .ss-bar-fill').forEach(el => {
        const target = el.style.width;
        el.style.width = '0';
        setTimeout(() => {
            el.style.transition = 'width .7s cubic-bezier(.4,0,.2,1)';
            el.style.width = target;
        }, 200);
    });

    document.querySelectorAll('.session-row').forEach((row, i) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-6px)';
        setTimeout(() => {
            row.style.transition = 'opacity .3s ease, transform .3s ease';
            row.style.opacity = '1';
            row.style.transform = 'none';
        }, 30 + i * 22);
    });

    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity .4s, transform .4s';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-6px)';
            setTimeout(() => alert.remove(), 400);
        }, 4500);
    });
});
