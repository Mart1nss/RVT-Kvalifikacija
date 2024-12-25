// Book Thumbnails
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';
function generateThumbnail(pdfPath) {
    pdfjsLib.getDocument(pdfPath).promise.then(function (pdf) {
        pdf.getPage(1).then(function (page) {
            var scale = 1;
            var viewport = page.getViewport({ scale: scale });
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            var renderContext = {
                canvasContext: context,
                viewport: viewport
            };

            page.render(renderContext).promise.then(function () {
                var thumbnailImg = document.createElement('img');
                thumbnailImg.src = canvas.toDataURL();

                var thumbnailDiv = document.querySelector('.thumbnail[data-pdfpath="' + pdfPath + '"]');
                thumbnailDiv.innerHTML = '';
                thumbnailDiv.appendChild(thumbnailImg);
            });
        });
    }).catch(function (error) {
        console.error("Error loading PDF:", error);
    });
}

document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(function (thumbnailDiv) {
    var pdfPath = thumbnailDiv.dataset.pdfpath;
    generateThumbnail(pdfPath);
});