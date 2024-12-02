document.addEventListener('DOMContentLoaded', function () {
  const pdfjsLib = window['pdfjs-dist/build/pdf'];
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';

  let pdfDoc = null,
      scale = 1.0;

  const pdfContainer = document.getElementById('pdf-container');
  const pageNumSpan = document.getElementById('page-num');
  const pageCountSpan = document.getElementById('page-count');
  const toolbarHeight = document.getElementById('pdf-toolbar').offsetHeight;
  
  // Get the book ID from the URL
  const bookId = window.location.pathname.split('/').pop();

  const initPDFViewer = (url) => {
      pdfjsLib.getDocument(url).promise.then((pdfDoc_) => {
          pdfDoc = pdfDoc_;
          pageCountSpan.textContent = pdfDoc.numPages;

          for (let i = 1; i <= pdfDoc.numPages; i++) {
              renderPage(i);
          }

          // Restore the last page position after rendering
          restorePagePosition();

          pdfContainer.addEventListener('scroll', handleScroll);
      });
  };

  const renderPage = (num) => {
      pdfDoc.getPage(num).then((page) => {
          const viewport = page.getViewport({ scale: scale });
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          canvas.height = viewport.height;
          canvas.width = viewport.width;

          canvas.classList.add('pdf-page');
          canvas.dataset.pageNumber = num;

          const renderContext = {
              canvasContext: ctx,
              viewport: viewport
          };
          page.render(renderContext);

          pdfContainer.appendChild(canvas);
      });
  };

  const handleScroll = () => {
      const pages = document.querySelectorAll('.pdf-page');
      let currentPage = 1;
      const containerScrollTop = pdfContainer.scrollTop;
      const containerHeight = pdfContainer.clientHeight;

      for (let page of pages) {
          const pageNumber = parseInt(page.dataset.pageNumber, 10);
          const pageTop = page.offsetTop;
          const pageHeight = page.clientHeight;

          if (containerScrollTop >= pageTop - toolbarHeight && containerScrollTop < pageTop + pageHeight - toolbarHeight) {
              currentPage = pageNumber;
              break;
          }
      }

      pageNumSpan.textContent = currentPage;
      
      // Save the current page and scroll position
      localStorage.setItem(`book_${bookId}_page`, currentPage);
      localStorage.setItem(`book_${bookId}_scroll`, containerScrollTop);
  };

  // Function to restore the last page position
  const restorePagePosition = () => {
      const lastPage = parseInt(localStorage.getItem(`book_${bookId}_page`)) || 1;
      const lastScroll = parseInt(localStorage.getItem(`book_${bookId}_scroll`)) || 0;
      
      // Wait for pages to render
      setTimeout(() => {
          pdfContainer.scrollTop = lastScroll;
          pageNumSpan.textContent = lastPage;
      }, 100);
  };

  document.getElementById('zoom-in').addEventListener('click', () => {
      const currentScroll = pdfContainer.scrollTop;
      scale += 0.1;
      pdfContainer.innerHTML = '';
      for (let i = 1; i <= pdfDoc.numPages; i++) {
          renderPage(i);
      }
      // Maintain scroll position after zoom
      setTimeout(() => pdfContainer.scrollTop = currentScroll * (1 + 0.1), 100);
  });

  document.getElementById('zoom-out').addEventListener('click', () => {
      if (scale <= 0.1) return;
      const currentScroll = pdfContainer.scrollTop;
      scale -= 0.1;
      pdfContainer.innerHTML = '';
      for (let i = 1; i <= pdfDoc.numPages; i++) {
          renderPage(i);
      }
      // Maintain scroll position after zoom
      setTimeout(() => pdfContainer.scrollTop = currentScroll * (1 - 0.1), 100);
  });

  // Add bookmark functionality
  const bookmarkBtn = document.getElementById('bookmark-btn');
  let currentBookmark = null;

  // Load existing bookmark when page loads
  const loadBookmark = () => {
    fetch(`/bookmarks/${bookId}`)
      .then(response => response.json())
      .then(data => {
        if (data && data.id) {  
          currentBookmark = data;
          bookmarkBtn.classList.add('active');
          // Option to jump to bookmark
          if (confirm('Would you like to go to your bookmarked page?')) {
            pdfContainer.scrollTop = data.scroll_position;
            pageNumSpan.textContent = data.page_number;
          }
        } else {
          currentBookmark = null;
          bookmarkBtn.classList.remove('active');
        }
      })
      .catch(error => {
        console.error('Error loading bookmark:', error);
        currentBookmark = null;
        bookmarkBtn.classList.remove('active');
      });
  };

  // Toggle bookmark
  bookmarkBtn.addEventListener('click', () => {
    const currentPage = parseInt(pageNumSpan.textContent);
    const scrollPosition = pdfContainer.scrollTop;

    if (currentBookmark) {
      // Remove bookmark
      fetch(`/bookmarks/${bookId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
      })
      .then(() => {
        currentBookmark = null;
        bookmarkBtn.classList.remove('active');
        alert('Bookmark removed');
      });
    } else {
      // Add bookmark
      fetch('/bookmarks', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
          product_id: bookId,
          page_number: currentPage,
          scroll_position: scrollPosition
        })
      })
      .then(response => response.json())
      .then(data => {
        currentBookmark = data.bookmark;
        bookmarkBtn.classList.add('active');
        alert('Page bookmarked!');
      });
    }
  });

  // Load bookmark when page loads
  loadBookmark();

  window.initPDFViewer = initPDFViewer;
});
