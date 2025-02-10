let currentDeleteForm = null;

function openEditModal(id) {
    fetch(`/edit/${id}`)
        .then((response) => response.json())
        .then((data) => {
            document.getElementById("title").value = data.title;
            document.getElementById("author").value = data.author;
            document.getElementById("category_id").value =
                data.category_id || "";
            document.getElementById("edit_is_public").checked = data.is_public;
            document.querySelector("#editForm .visibility-label").textContent =
                data.is_public ? "Public" : "Private";
            document.getElementById("editForm").action = `/update/${id}`;
            const modal = document.getElementById("editModal");
            modal.style.display = "block";
            modal.classList.remove("closing");
            document.body.style.overflow = "hidden";
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while loading the book details");
        });
}

function closeEditModal() {
    const modal = document.getElementById("editModal");
    modal.classList.add("closing");
    setTimeout(() => {
        modal.style.display = "none";
        modal.classList.remove("closing");
        document.body.style.overflow = "";
    }, 300); // Match animation duration
}

function confirmDelete(title, author, id) {
    currentDeleteForm = document.getElementById("deleteForm" + id);
    document.getElementById(
        "deleteBookDetails"
    ).textContent = `${title} by ${author}`;
    const modal = document.getElementById("deleteModal");
    modal.style.display = "block";
    modal.classList.remove("closing");
    document.body.style.overflow = "hidden";
}

function closeDeleteModal() {
    const modal = document.getElementById("deleteModal");
    modal.classList.add("closing");
    setTimeout(() => {
        modal.style.display = "none";
        modal.classList.remove("closing");
        document.body.style.overflow = "";
        currentDeleteForm = null;
    }, 300); // Match animation duration
}

function submitDelete() {
    if (currentDeleteForm) {
        currentDeleteForm.submit();
    }
}

// Close modals when clicking outside
document.addEventListener("DOMContentLoaded", function () {
    window.onclick = function (event) {
        const editModal = document.getElementById("editModal");
        const deleteModal = document.getElementById("deleteModal");
        if (event.target == editModal) {
            closeEditModal();
        } else if (event.target == deleteModal) {
            closeDeleteModal();
        }
    };
});
