document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('arquivo');
    const fileNameDisplay = document.getElementById('fileNameDisplay');

    if (fileInput) {
        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                fileNameDisplay.textContent = "Arquivo selecionado: " + e.target.files[0].name;
            } else {
                fileNameDisplay.textContent = "";
            }
        });
    }
});
