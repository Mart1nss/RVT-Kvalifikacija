import * as pdfjsLib from "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs";

pdfjsLib.GlobalWorkerOptions.workerSrc =
    "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs";

// Track which thumbnails have been generated and are loading
const generatedThumbnails = new Set();
const loadingThumbnails = new Set();
const failedThumbnails = new Set();

// Make the observer accessible globally
let observer;

function initializePdfThumbnails() {
    // Clear previous thumbnails state
    generatedThumbnails.clear();
    loadingThumbnails.clear();
    failedThumbnails.clear();

    // Disconnect previous observer if exists
    if (observer) {
        observer.disconnect();
    }

    // Create new observer
    observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const thumbnailDiv = entry.target;
                    const pdfPath = thumbnailDiv.dataset.pdfpath;
                    // Extract filename from the path
                    const filename = pdfPath.split('/').pop();
                    
                    if (
                        !generatedThumbnails.has(filename) &&
                        !loadingThumbnails.has(filename) &&
                        !failedThumbnails.has(filename)
                    ) {
                        loadingThumbnails.add(filename);
                        generateThumbnail(filename);
                    }
                }
            });
        },
        {
            rootMargin: "100px 0px",
            threshold: 0.1,
        }
    );

    // Observe all thumbnails
    document
        .querySelectorAll(".thumbnail[data-pdfpath]")
        .forEach((thumbnailDiv) => {
            observer.observe(thumbnailDiv);
        });
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", initializePdfThumbnails);

// Make function available globally
window.initializePdfThumbnails = initializePdfThumbnails;

async function generateThumbnail(filename) {
    try {
        const pdfUrl = `/book-thumbnail/${filename}`;
        const loadingTask = pdfjsLib.getDocument(pdfUrl);
        let timeoutId = setTimeout(() => {
            loadingTask.destroy();
            handleThumbnailError(filename, new Error("Loading timeout"));
        }, 10000); // 10 second timeout

        const pdf = await loadingTask.promise;
        clearTimeout(timeoutId);

        const page = await pdf.getPage(1);

        // Calculate optimal dimensions
        const viewport = page.getViewport({
            scale: 1.0,
        });
        const MAX_WIDTH = 400;
        const scale = Math.min(1.0, MAX_WIDTH / viewport.width);
        const scaledViewport = page.getViewport({
            scale,
        });

        const canvas = document.createElement("canvas");
        canvas.width = scaledViewport.width;
        canvas.height = scaledViewport.height;

        const context = canvas.getContext("2d", {
            alpha: false,
            desynchronized: true,
        });

        await page.render({
            canvasContext: context,
            viewport: scaledViewport,
            intent: "display",
        }).promise;

        // Create and optimize thumbnail
        const thumbnailImg = new Image();
        thumbnailImg.decoding = "async";
        thumbnailImg.loading = "lazy";
        thumbnailImg.style.cssText =
            "width: 100%; height: 100%; object-fit: cover;";

        // Use better quality JPEG with reasonable compression
        thumbnailImg.src = canvas.toDataURL("image/jpeg", 0.85);

        // Update all instances of this thumbnail
        const oldPath = `/assets/${filename}`;
        document
            .querySelectorAll(`.thumbnail[data-pdfpath="${oldPath}"]`)
            .forEach((div) => {
                div.innerHTML = "";
                div.appendChild(thumbnailImg.cloneNode(true));
            });

        // Cleanup
        canvas.width = 0;
        canvas.height = 0;
        context.clearRect(0, 0, 0, 0);
        page.cleanup();
        pdf.destroy();
        loadingTask.destroy();

        generatedThumbnails.add(filename);
        loadingThumbnails.delete(filename);
    } catch (error) {
        handleThumbnailError(filename, error);
    }
}

function handleThumbnailError(filename, error) {
    console.error("Error generating thumbnail:", error);
    const oldPath = `/assets/${filename}`;
    document
        .querySelectorAll(`.thumbnail[data-pdfpath="${oldPath}"]`)
        .forEach((div) => {
            div.innerHTML = `
          <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #2a2a2a;">
            <i class='bx bx-error' style="font-size: 2rem; color: #ff4444;"></i>
          </div>
        `;
        });
    loadingThumbnails.delete(filename);
    failedThumbnails.add(filename);
}

// Add styles
const style = document.createElement("style");
style.textContent = `
      @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
      }
      .thumbnail {
        position: relative;
        background: #2a2a2a;
      }
      .loading-indicator {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #2a2a2a;
      }
      .loading-indicator i {
        font-size: 2rem;
        color: white;
        animation: spin 1s linear infinite;
      }
    `;
document.head.appendChild(style);

// Memory management
let cleanupInterval;
const MAX_CACHED_THUMBNAILS = 50; 

function startCleanupInterval() {
    cleanupInterval = setInterval(() => {
        if (generatedThumbnails.size > MAX_CACHED_THUMBNAILS) {
            const thumbnailsToRemove = Array.from(generatedThumbnails).slice(
                0,
                20
            );
            thumbnailsToRemove.forEach((path) => {
                generatedThumbnails.delete(path);
                document
                    .querySelectorAll(`.thumbnail[data-pdfpath="${path}"]`)
                    .forEach((div) => {
                        if (!div.isIntersecting) {
                            div.innerHTML = `
                  <div class="loading-indicator">
                    <i class='bx bx-loader-alt'></i>
                  </div>
                `;
                        }
                    });
            });
        }
    }, 30000); // Check every 30 seconds
}

// Visibility handling
document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
        clearInterval(cleanupInterval);
    } else {
        startCleanupInterval();
    }
});

startCleanupInterval();

// Cleanup on page leave
window.addEventListener("unload", () => {
    observer.disconnect();
    generatedThumbnails.clear();
    loadingThumbnails.clear();
    failedThumbnails.clear();
    clearInterval(cleanupInterval);
});
