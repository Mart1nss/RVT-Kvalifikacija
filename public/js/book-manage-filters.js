function applyVisibilityFilter() {
    // DROPDOWN VISIBILITY FILTER.
    const visibility = document.getElementById("visibilityFilter").value;
    const currentUrl = new URL(window.location.href);

    currentUrl.searchParams.set("visibility", visibility);

    const searchQuery = document.querySelector('input[name="query"]')?.value;
    if (searchQuery) {
        currentUrl.searchParams.set("query", searchQuery);
    }

    currentUrl.searchParams.delete("page");

    window.location.href = currentUrl.toString();
}

document.addEventListener("DOMContentLoaded", function () {
    // EDIT MDOAL VISIBILITY TOGGLE.
    const editModalToggle = document.querySelector(
        '#editForm .visibility-toggle input[type="checkbox"]'
    );
    if (editModalToggle) {
        editModalToggle.addEventListener("change", function () {
            const label =
                this.closest(".visibility-toggle").querySelector(
                    ".visibility-label"
                );
            label.textContent = this.checked ? "Public" : "Private";
        });
    }
});
