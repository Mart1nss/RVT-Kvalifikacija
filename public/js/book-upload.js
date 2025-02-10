const dropZone = document.getElementById("drop-zone");
const fileInput = document.getElementById("fileInput");
const fileInfo = document.getElementById("file-info");
const fileChosen = document.getElementById("file-chosen");

// Prevent default drag behaviors
["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
    dropZone.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

// Highlight drop zone when dragging over it
["dragenter", "dragover"].forEach((eventName) => {
    dropZone.addEventListener(eventName, highlight, false);
});

["dragleave", "drop"].forEach((eventName) => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

// Handle dropped files
dropZone.addEventListener("drop", handleDrop, false);

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    dropZone.classList.add("dragover");
}

function unhighlight(e) {
    dropZone.classList.remove("dragover");
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;

    if (files.length > 0) {
        fileInput.files = files;
        updateFileInfo(files[0]);
    }
}

function validateFileType(file) {
    const validTypes = ["application/pdf"];
    if (file && !validTypes.includes(file.type)) {
        dropZone.classList.add("file-error");
        document.getElementById("fileError").textContent =
            "Please upload only PDF files";
        return false;
    }
    return true;
}

function validateFileSize(file) {
    const maxSize = 10 * 1024 * 1024; // 10MB in bytes
    if (file && file.size > maxSize) {
        dropZone.classList.add("file-error");
        document.getElementById("fileError").textContent =
            "File size must be less than 10MB";
        return false;
    }
    return true;
}

function updateFileInfo(file) {
    if (file) {
        const isValidType = validateFileType(file);
        const isValidSize = validateFileSize(file);

        if (isValidType && isValidSize) {
            fileChosen.textContent = file.name;
            fileInfo.style.display = "flex";
            dropZone.style.display = "none";
            dropZone.classList.remove("file-error");
            document.getElementById("fileError").textContent = "";
        } else {
            fileChosen.textContent = "Choose or drop PDF file";
            fileInfo.style.display = "none";
            dropZone.style.display = "block";
            fileInput.value = ""; // Clear the file input
        }
    } else {
        fileChosen.textContent = "Choose or drop PDF file";
        fileInfo.style.display = "none";
        dropZone.style.display = "block";
        document.getElementById("fileError").textContent = "";
        dropZone.classList.remove("file-error");
    }
}

fileInput.addEventListener("change", function () {
    if (this.files && this.files[0]) {
        updateFileInfo(this.files[0]);
    } else {
        updateFileInfo(null);
    }
});

document
    .querySelector(".upload-book-form")
    .addEventListener("submit", function (e) {
        const titleInput = document.getElementById("titleInput");
        const authorInput = document.getElementById("authorInput");
        const categoryInput = document.getElementById("categoryInput");
        const fileInput = document.getElementById("fileInput");

        // Clear all previous error messages
        document
            .querySelectorAll(".error-message")
            .forEach((el) => (el.textContent = ""));

        let hasError = false;

        // Check title
        if (!titleInput.value.trim()) {
            e.preventDefault();
            document.getElementById("titleError").textContent =
                "Please enter the book title";
            hasError = true;
        }

        // Check author
        if (!authorInput.value.trim()) {
            e.preventDefault();
            document.getElementById("authorError").textContent =
                "Please enter the author name";
            hasError = true;
        }

        // Check category
        if (!categoryInput.value) {
            e.preventDefault();
            document.getElementById("categoryError").textContent =
                "Book genre required";
            hasError = true;
        }

        // Check if file is selected
        if (!fileInput.files[0]) {
            e.preventDefault();
            document.getElementById("fileError").textContent =
                "Please select a PDF file";
            dropZone.classList.add("file-error");
            return;
        }

        const file = fileInput.files[0];
        const isValidType = validateFileType(file);
        const isValidSize = validateFileSize(file);

        if (!isValidType || !isValidSize) {
            e.preventDefault();
            return;
        }
    });

function showFloatingAlert(type, message) {
    const alertContainer = document.getElementById("alertContainer");
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type === "error" ? "danger" : type}`;
    alertDiv.textContent = message;

    // Remove any existing alerts
    alertContainer.innerHTML = "";
    alertContainer.appendChild(alertDiv);

    // Remove the alert after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

function clearFileInput() {
    fileInput.value = "";
    updateFileInfo(null);
}
