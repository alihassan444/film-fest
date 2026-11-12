(() => {
    const form = document.getElementById('bulk-ticket-approval');
    if (!form) return;
    const all = document.getElementById('select-pending-page');
    const boxes = [...document.querySelectorAll('.ticket-bulk-checkbox')];
    const button = document.getElementById('bulk-approve-button');
    const count = document.getElementById('bulk-selection-count');
    const update = () => {
        const selected = boxes.filter(box => box.checked).length;
        count.textContent = selected + ' selected';
        button.disabled = selected === 0;
        all.checked = selected === boxes.length;
        all.indeterminate = selected > 0 && selected < boxes.length;
    };
    all.addEventListener('change', () => { boxes.forEach(box => { box.checked = all.checked; }); update(); });
    boxes.forEach(box => box.addEventListener('change', update));
    form.addEventListener('submit', event => {
        const selected = boxes.filter(box => box.checked).length;
        if (!selected || !window.confirm('Approve ' + selected + ' tickets and send their QR emails?')) {
            event.preventDefault();
            return;
        }
        button.disabled = true;
        button.textContent = 'Approving tickets…';
    });
    window.addEventListener('pageshow', update);
    update();
})();
