document.addEventListener("DOMContentLoaded", function () {
    const pdfjsLib = window["pdfjs-dist/build/pdf"];
    pdfjsLib.GlobalWorkerOptions.workerSrc =
        "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js";

    // PDF viewer state
    let pdfDoc = null;
    let scale = 1.0;
    let currentPageNum = 1;
    let pagesRendering = new Set();
    let pageViewports = new Map();
    let pageCanvases = new Map();

    // Cache DOM elements
    const pdfContainer = document.getElementById("pdf-container");
    const pageNumSpan = document.getElementById("page-num");
    const pageCountSpan = document.getElementById("page-count");
    const toolbarHeight = document.getElementById("pdf-toolbar").offsetHeight;
    const zoomInBtn = document.getElementById("zoom-in");
    const zoomOutBtn = document.getElementById("zoom-out");

    // Get book ID from URL (if needed, though productId is passed from Blade now)
    // const bookId = window.location.pathname.split("/").pop();

    // Expose functions for external use (like bookmarking)
    window.getCurrentPageNum = () => currentPageNum;
    window.goToPage = (pageNumber) => {
        const targetPageDiv = document.querySelector(`.pdf-page-container[data-page-number="${pageNumber}"]`);
        if (targetPageDiv) {
            pdfContainer.scrollTop = targetPageDiv.offsetTop - toolbarHeight; // Adjust for toolbar
            // Ensure the page is rendered if not already
            checkVisiblePages();
        } else {
            // console.warn(`goToPage: Page div ${pageNumber} not found.`);
            // Fallback: scroll proportionally (less accurate)
            const scrollPercentage = (pageNumber - 1) / pdfDoc.numPages;
            pdfContainer.scrollTop = pdfContainer.scrollHeight * scrollPercentage;
        }
        // Update the displayed page number immediately
        currentPageNum = pageNumber;
        pageNumSpan.textContent = currentPageNum;
    };

    // Expose function to be called when PDF is loaded but before position restore
    // let pdfLoadCallback = null;
    // Modified initPDFViewer to accept a callback
    const initPDFViewer = async (url, callback) => {
        // pdfLoadCallback = callback; // Store callback
        try {
            // Show loading indicator
            pdfContainer.innerHTML =
                '<div class="loading">Loading PDF...</div>';

            // Load the PDF with authentication headers
            const loadingTask = pdfjsLib.getDocument({
                url: url,
                withCredentials: true,
                httpHeaders: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            pdfDoc = await loadingTask.promise;
            pageCountSpan.textContent = pdfDoc.numPages;

            // Clear loading indicator
            pdfContainer.innerHTML = "";

            // Create placeholder divs for all pages
            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const pageDiv = document.createElement("div");
                pageDiv.className = "pdf-page-container";
                pageDiv.dataset.pageNumber = i;
                // pageDiv.style.minHeight = "800px"; // REMOVED - Let height be determined by canvas
                pdfContainer.appendChild(pageDiv);
            }

            checkVisiblePages();

            pdfContainer.addEventListener(
                "scroll",
                throttle(checkVisiblePages, 100)
            );
            window.addEventListener("resize", throttle(checkVisiblePages, 100));

            // Call the callback function if provided, *after* basic setup but *before* restoring position
            if (typeof callback === 'function') {
                callback();
            }
            // restorePagePosition(); // Restore logic is now handled by the callback in viewproduct.blade.php

        } catch (error) {
            console.error("Error loading PDF:", error);
            pdfContainer.innerHTML =
                '<div class="error" style="text-align: center; padding: 20px; color: #ff4444;">' +
                '<i class="bx bx-error" style="font-size: 48px; display: block; margin-bottom: 10px;"></i>' +
                '<p>Error loading PDF. Please try refreshing the page.</p>' +
                '</div>';
        }
    };

    // Throttle function to limit how often a function is called
    function throttle(func, limit) {
        let inThrottle;
        return function () {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => (inThrottle = false), limit);
            }
        };
    }

    // Check which pages are visible and render them
    const checkVisiblePages = async () => {
        if (!pdfDoc) return;

        const containerRect = pdfContainer.getBoundingClientRect();
        const pageDivs = document.querySelectorAll(".pdf-page-container");

        for (const pageDiv of pageDivs) {
            const pageRect = pageDiv.getBoundingClientRect();
            const pageNumber = parseInt(pageDiv.dataset.pageNumber);

            // Check if page is visible or near visible (one page above and below)
            const isNearVisible =
                pageRect.top < containerRect.bottom + containerRect.height &&
                pageRect.bottom > containerRect.top - containerRect.height;

            if (
                isNearVisible &&
                !pageCanvases.has(pageNumber) &&
                !pagesRendering.has(pageNumber)
            ) {
                renderPage(pageNumber);
            } else if (!isNearVisible && pageCanvases.has(pageNumber)) {
                // Optionally unload far away pages (can improve performance on large docs)
                // const canvas = pageCanvases.get(pageNumber);
                // const pageDiv = canvas.parentNode;
                // pageDiv.innerHTML = ''; // Clear content
                // pageCanvases.delete(pageNumber);
                // pageViewports.delete(pageNumber);
            }

            // Update current page number based on visibility
            if (
                pageRect.top <= containerRect.top + toolbarHeight &&
                pageRect.bottom > containerRect.top + toolbarHeight
            ) {
                if (currentPageNum !== pageNumber) {
                    currentPageNum = pageNumber;
                    pageNumSpan.textContent = currentPageNum;
                }
            }
        }
    };

    // Render a single page
    const renderPage = async (pageNumber) => {
        // Keep try-catch here as rendering can fail
        try {
            if (!pdfDoc) return; // Ensure pdfDoc is loaded
            // Check if already rendering or rendered
            if (pagesRendering.has(pageNumber) || pageCanvases.has(pageNumber)) return;

            pagesRendering.add(pageNumber);
            const page = await pdfDoc.getPage(pageNumber);
            const viewport = page.getViewport({ scale: scale });
            pageViewports.set(pageNumber, viewport);

            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            canvas.classList.add("pdf-page");

            const pageDiv = document.querySelector(
                `.pdf-page-container[data-page-number="${pageNumber}"]`
            );
            pageDiv.style.height = `${viewport.height}px`;
            pageDiv.innerHTML = ""; // Clear any existing content
            pageDiv.appendChild(canvas);

            await page.render({
                canvasContext: ctx,
                viewport: viewport,
            }).promise;

            pageCanvases.set(pageNumber, canvas);
            pagesRendering.delete(pageNumber);
        } catch (error) {
            console.error(`Error rendering page ${pageNumber}:`, error);
            // Attempt to remove the partially rendered/failed page container content
            const pageDiv = document.querySelector(`.pdf-page-container[data-page-number="${pageNumber}"]`);
            if (pageDiv) pageDiv.innerHTML = '<div class="error">Failed to render page</div>';
            pagesRendering.delete(pageNumber); // Ensure it's removed from rendering set
        }
    };

    // Handle zoom
    const handleZoom = async (zoomIn) => {
        const oldScale = scale;
        if (zoomIn) {
            scale = Math.min(3, scale + 0.2); // Max zoom 3x
        } else {
            scale = Math.max(0.5, scale - 0.2); // Min zoom 0.5x
        }

        if (oldScale === scale) return;

        // Store current page and relative scroll position
        const currentPage = parseInt(pageNumSpan.textContent);
        const currentPageDiv = document.querySelector(
            `.pdf-page-container[data-page-number="${currentPage}"]`
        );
        const relativeScrollPosition =
            (pdfContainer.scrollTop - currentPageDiv.offsetTop) /
            currentPageDiv.offsetHeight;

        // Clear existing canvases and reset maps
        pageCanvases.clear();
        pageViewports.clear();
        pagesRendering.clear();

        // Re-render visible pages
        await checkVisiblePages();

        // Restore scroll position relative to current page
        const newPageDiv = document.querySelector(
            `.pdf-page-container[data-page-number="${currentPage}"]`
        );
        if (newPageDiv) {
            const newScrollTop =
                newPageDiv.offsetTop +
                newPageDiv.offsetHeight * relativeScrollPosition;
            pdfContainer.scrollTop = newScrollTop;
        }
    };

    // Event listeners
    zoomInBtn.addEventListener("click", () => handleZoom(true));
    zoomOutBtn.addEventListener("click", () => handleZoom(false));

    // Touch zoom support
    let touchStartDistance = 0;
    pdfContainer.addEventListener("touchstart", (e) => {
        if (e.touches.length === 2) {
            touchStartDistance = Math.hypot(
                e.touches[0].pageX - e.touches[1].pageX,
                e.touches[0].pageY - e.touches[1].pageY
            );
        }
    });

    pdfContainer.addEventListener("touchmove", (e) => {
        if (e.touches.length === 2) {
            e.preventDefault(); // Prevent page scroll during pinch zoom
            const currentDistance = Math.hypot(
                e.touches[0].pageX - e.touches[1].pageX,
                e.touches[0].pageY - e.touches[1].pageY
            );

            if (Math.abs(currentDistance - touchStartDistance) > 50) {
                handleZoom(currentDistance > touchStartDistance);
                touchStartDistance = currentDistance;
            }
        }
    });

    // Expose initialization function
    window.initPDFViewer = initPDFViewer;
});
