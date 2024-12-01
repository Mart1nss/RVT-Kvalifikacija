document.addEventListener('DOMContentLoaded', function () {
  const pdfjsLib = window['pdfjs-dist/build/pdf'];
  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.6.347/pdf.worker.min.js';

  let pdfDoc = null,
      scale = 1.0;

  const pdfContainer = document.getElementById('pdf-container');
  const pageNumSpan = document.getElementById('page-num');
  const pageCountSpan = document.getElementById('page-count');
  const toolbarHeight = document.getElementById('pdf-toolbar').offsetHeight;

  const initPDFViewer = (url) => {
      pdfjsLib.getDocument(url).promise.then((pdfDoc_) => {
          pdfDoc = pdfDoc_;
          pageCountSpan.textContent = pdfDoc.numPages;

          for (let i = 1; i <= pdfDoc.numPages; i++) {
              renderPage(i);
          }

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
  };

  document.getElementById('zoom-in').addEventListener('click', () => {
      scale += 0.1;
      pdfContainer.innerHTML = '';
      for (let i = 1; i <= pdfDoc.numPages; i++) {
          renderPage(i);
      }
  });

  document.getElementById('zoom-out').addEventListener('click', () => {
      if (scale <= 0.1) return;
      scale -= 0.1;
      pdfContainer.innerHTML = '';
      for (let i = 1; i <= pdfDoc.numPages; i++) {
          renderPage(i);
      }
  });

  window.initPDFViewer = initPDFViewer;
});
