(() => {
    'use strict';
    const modal = document.getElementById('receiptPreviewModal');
    const image = document.getElementById('receiptPreviewImage');
    const status = document.getElementById('receiptPreviewStatus');
    const reference = document.getElementById('receiptPreviewReference');
    if (!modal || !image || !status || !reference) return;
    modal.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        const source = trigger?.dataset.receiptSrc;
        if (!source) { event.preventDefault(); return; }
        image.hidden = true;
        status.hidden = false;
        status.textContent = 'Loading receipt…';
        reference.textContent = trigger.dataset.ticketReference || '';
        image.alt = 'Payment receipt for ' + reference.textContent;
        image.onload = () => { image.hidden = false; status.hidden = true; };
        image.onerror = () => {
            image.hidden = true;
            status.hidden = false;
            status.textContent = 'This receipt could not be loaded. Close the preview and try again.';
        };
        image.src = source;
    });
    modal.addEventListener('hidden.bs.modal', () => {
        image.onload = null;
        image.onerror = null;
        image.removeAttribute('src');
        image.hidden = true;
        reference.textContent = '';
    });
})();
