// FOOTER PHIL STANDARD TIME
function updateDateTime() {
    const now = new Date();
    document.getElementById('current-date').textContent = now.toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true,
        timeZone: 'Asia/Manila'
    });
}
updateDateTime();
setInterval(updateDateTime, 1000);