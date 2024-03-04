window.addEventListener('DOMContentLoaded', event => {
    let options = {
        hiddenHeader: true
    }
    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        new simpleDatatables.DataTable(datatablesSimple);
    }
});
