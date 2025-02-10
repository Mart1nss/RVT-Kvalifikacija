document.addEventListener("DOMContentLoaded", function () {
    const genreDropdownBtn = document.querySelector(
        ".genre-dropdown .dropdown-btn"
    );
    const genreDropdownContent = document.querySelector(
        ".genre-dropdown .dropdown-content"
    );
    const sortDropdownBtn = document.querySelector(
        ".sort-dropdown .dropdown-btn"
    );
    const sortDropdownContent = document.querySelector(
        ".sort-dropdown .dropdown-content"
    );
    const clearFiltersBtn = document.querySelector(".clear-filters-btn");
    const searchInput = document.getElementById("search-input");
    const sortSelect = document.getElementById("sortSelect");
    const filterInfoRow = document.querySelector(".filter-info-row");
    const bookCountElement = document.getElementById("book-count");
    const activeFiltersElement = document.getElementById("active-filters");

    const selectedGenres = new Set();
    let isLoading = false;
    let searchTimeout;

    function updateClearFiltersVisibility() {
        const hasFilters =
            selectedGenres.size > 0 ||
            searchInput.value.trim() !== "" ||
            sortSelect.value !== "newest";
        clearFiltersBtn.style.display = hasFilters ? "flex" : "none";

        // Show filter info row for genres and search
        if (selectedGenres.size > 0 || searchInput.value.trim() !== "") {
            filterInfoRow.style.display = "flex";
        } else {
            filterInfoRow.style.display = "none";
        }
    }

    function updateFilterInfoRow(totalBooks) {
        if (!filterInfoRow) return;

        bookCountElement.textContent = totalBooks;

        activeFiltersElement.innerHTML = "";
        selectedGenres.forEach((genre) => {
            const tag = document.createElement("span");
            tag.className = "filter-tag";
            tag.textContent = genre;
            activeFiltersElement.appendChild(tag);
        });

        if (searchInput.value.trim() !== "") {
            const searchTag = document.createElement("span");
            searchTag.className = "filter-tag";
            searchTag.textContent = `${totalBooks} results for "${searchInput.value}"`;
            activeFiltersElement.appendChild(searchTag);
        }
    }

    function clearAllFilters() {
        // Clear genre filters
        selectedGenres.clear();
        document
            .querySelectorAll(
                '.genre-checkbox-container input[type="checkbox"]'
            )
            .forEach((checkbox) => {
                checkbox.checked = false;
            });
        updateDropdownButton();

        // Clear search
        searchInput.value = "";

        // Reset sort to default
        sortSelect.value = "newest";
        sortDropdownBtn.textContent = "Newest First";
        sortDropdownBtn.innerHTML +=
            '<span style="margin-left: auto;">▼</span>';

        // Update UI
        updateClearFiltersVisibility();

        // Refresh results
        updateResults(1);

        // Update filter info row
        updateFilterInfoRow(document.querySelectorAll(".pdf-item").length);
    }

    function populateGenres(genres) {
        const fragment = document.createDocumentFragment();
        const urlParams = new URLSearchParams(window.location.search);
        const currentGenres = urlParams.get("genres")
            ? urlParams.get("genres").split(",")
            : [];

        genres.forEach((genre) => {
            if (genre) {
                const li = document.createElement("li");
                const label = document.createElement("label");
                label.className = "genre-checkbox-container";
                label.innerHTML = `
                    <input type="checkbox" value="${genre}" ${
                    currentGenres.includes(genre) ? "checked" : ""
                }>
                    <span class="custom-checkbox"></span>
                    <span class="genre-name">${genre}</span>
                `;
                li.appendChild(label);
                fragment.appendChild(li);

                if (currentGenres.includes(genre)) {
                    selectedGenres.add(genre);
                }
            }
        });

        // Update desktop genre list
        if (genreDropdownContent) {
            genreDropdownContent.innerHTML = "";
            genreDropdownContent.appendChild(fragment.cloneNode(true));
        }

        updateDropdownButton();
        updateClearFiltersVisibility();
    }

    function updateDropdownButton() {
        if (!genreDropdownBtn) return;
        genreDropdownBtn.innerHTML = '<i class="bx bx-filter-alt"></i> Genres';
    }

    function updateSortButtonText(text) {
        if (!sortDropdownBtn) return;
        sortDropdownBtn.innerHTML = '<i class="bx bx-sort-alt-2"></i> ' + text;
    }

    function updateSelectedSortOption(value) {
        document
            .querySelectorAll(".sort-dropdown .dropdown-content li")
            .forEach((li) => {
                li.classList.remove("selected");
            });
        const selectedOption = document.querySelector(
            `.sort-dropdown .dropdown-content li[data-value="${value}"]`
        );
        if (selectedOption) {
            selectedOption.classList.add("selected");
        }
    }

    function updateResults(page = null) {
        if (isLoading) return;
        isLoading = true;

        const searchQuery = searchInput.value;
        const sort = document.getElementById("sortSelect").value;
        const genres = Array.from(selectedGenres);

        const itemContainer = document.querySelector(".item-container");
        const paginationContainer = document.querySelector(
            ".pagination-container"
        );
        itemContainer.style.opacity = "0.5";

        const url = new URL(window.location.href);
        url.searchParams.set("sort", sort);
        if (searchQuery) url.searchParams.set("query", searchQuery);
        else url.searchParams.delete("query");
        if (genres.length) url.searchParams.set("genres", genres.join(","));
        else url.searchParams.delete("genres");

        // Handle pagination parameter
        if (page) {
            url.searchParams.set("page", page);
        }

        // Use pushState instead of replaceState to properly handle browser history
        window.history.pushState({ page }, "", url);

        fetch(url.toString())
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.text();
            })
            .then((html) => {
                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = html;

                // Update the book items
                const newItems = tempDiv.querySelector(".item-container");
                if (newItems) {
                    itemContainer.innerHTML = newItems.innerHTML;
                }

                // Update pagination
                const newPagination = tempDiv.querySelector(
                    ".pagination-container"
                );
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                    // Reattach pagination event listeners
                    attachPaginationListeners();
                }

                itemContainer.style.opacity = "1";

                // Update filter info row with new total
                const totalBooks = tempDiv.querySelectorAll(".pdf-item").length;
                // Only show filter info for genres and search
                if (selectedGenres.size > 0 || searchQuery) {
                    updateFilterInfoRow(totalBooks);
                }

                // Initialize thumbnails for new items
                if (typeof initializePdfThumbnails === "function") {
                    initializePdfThumbnails();
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                itemContainer.style.opacity = "1";
            })
            .finally(() => {
                isLoading = false;
            });
    }

    function attachPaginationListeners() {
        document
            .querySelector(".pagination-container")
            .addEventListener("click", (e) => {
                if (e.target.tagName === "A") {
                    e.preventDefault();
                    const pageNum = new URL(e.target.href).searchParams.get(
                        "page"
                    );
                    updateResults(pageNum || 1);
                }
            });
    }

    window.addEventListener("popstate", () => {
        const urlParams = new URLSearchParams(window.location.search);
        searchInput.value = urlParams.get("query") || "";
        selectedGenres.clear();
        (urlParams.get("genres") || "")
            .split(",")
            .forEach((g) => selectedGenres.add(g));
        updateResults(urlParams.get("page") || 1);
    });

    // Check for active filters on page load
    function checkInitialFilters() {
        const urlParams = new URLSearchParams(window.location.href);
        const hasGenres = urlParams.has("genres");
        const hasSearch = urlParams.has("query");

        // Only show filter info row for genres and search, not for sort
        if (hasGenres || hasSearch) {
            const totalBooks = document.querySelectorAll(".pdf-item").length;
            updateFilterInfoRow(totalBooks);
        }
    }

    // Event Listeners
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener("click", clearAllFilters);
    }

    // Genre Dropdown Logic
    if (genreDropdownBtn && genreDropdownContent) {
        genreDropdownBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            genreDropdownContent.classList.toggle("show");
            sortDropdownContent?.classList.remove("show");
        });

        genreDropdownContent.addEventListener("change", (e) => {
            if (e.target.type === "checkbox") {
                if (e.target.checked) {
                    if (!selectedGenres.has(e.target.value)) {
                        selectedGenres.add(e.target.value);
                    }
                } else {
                    selectedGenres.delete(e.target.value);
                }
                updateDropdownButton();
                updateClearFiltersVisibility();
                updateResults(1);
            }
        });
    }

    // Sort Dropdown Logic
    if (sortDropdownBtn && sortDropdownContent) {
        sortDropdownBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            sortDropdownContent.classList.toggle("show");
            genreDropdownContent?.classList.remove("show");
        });

        // Handle sort selection
        sortDropdownContent.addEventListener("click", (e) => {
            if (e.target.tagName === "LI") {
                const value = e.target.dataset.value;
                const text = e.target.textContent;

                sortSelect.value = value;
                updateSortButtonText(text);
                updateSelectedSortOption(value);
                sortDropdownContent.classList.remove("show");

                updateClearFiltersVisibility();
                updateResults(1);
            }
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener("click", (e) => {
        if (!e.target.closest(".genre-dropdown")) {
            genreDropdownContent?.classList.remove("show");
        }
        if (!e.target.closest(".sort-dropdown")) {
            sortDropdownContent?.classList.remove("show");
        }
    });

    // Search Input
    searchInput.addEventListener("input", function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            updateClearFiltersVisibility();
            updateResults(1);
        }, 300);
    });

    // Initial Genre Population
    fetch("/get-genres")
        .then((response) => response.json())
        .then((genres) => {
            populateGenres(genres);
            checkInitialFilters(); // Check filters after populating genres
        })
        .catch((error) => {
            console.error("Error fetching genres:", error);
            checkInitialFilters(); // Still check filters even if genre fetch fails
        });

    // Initialize pagination links
    attachPaginationListeners();

    // Set initial sort button text and selected option
    if (sortDropdownBtn && sortSelect) {
        const selectedOption = sortSelect.options[sortSelect.selectedIndex];
        updateSortButtonText(selectedOption.text.replace(" First", ""));
        updateSelectedSortOption(sortSelect.value);
    }

    // Initial clear filters button visibility
    updateClearFiltersVisibility();
});

function applySorting() {
    updateResults(1);
}

function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

$(document).ready(function () {
    const searchBooks = debounce(function (searchTerm) {
        const items = document.querySelectorAll(".pdf-item");
        items.forEach((item) => {
            const title = item
                .querySelector(".info-title")
                .textContent.toLowerCase();
            const author = item
                .querySelector(".info-author")
                .textContent.toLowerCase();
            item.style.display =
                title.includes(searchTerm) || author.includes(searchTerm)
                    ? "block"
                    : "none";
        });
    }, 300);

    $("#search-input").on("keyup", function () {
        const searchTerm = $(this).val().toLowerCase();
        searchBooks(searchTerm);
    });
});
