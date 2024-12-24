<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <!-- Fonts -->
    <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css"
        integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/welcome-style.css') }}">

</head>


<body>

    <header class="navbar">
        <h1 class="logo"
            style="font-family: sans-serif; color: white; cursor: pointer; font-weight: 800 font-size: 24px">ELEVATE
            READS</h1>

        <nav class="nav-btn">
            @if (Route::has('login'))
                @auth
                    <button onclick="window.location.href = '{{ route('home') }}'" class="login-btn">Dashboard</button>
                @else
                    <button class="login-btn" style="margin-right: 14px;"
                        onclick="window.location.href = '{{ route('login') }}'">Login</button>

                    @if (Route::has('register'))
                        <button onclick="window.location.href = '{{ route('register') }}'" class="register-btn">Register</button>
                    @endif
                @endauth
            @endif
        </nav>
    </header>



    <div class="main-container">
        <p class="main-text">Discover the Power of Knowledge to Transform Your Life</p><br>

        <p class="main-text">Start Elevating Today
        </p><br>


        <div class="carousel-container">
            <div class="text-container">
                <h1>INSIDE THE LIBRARY</h1>
                <p>Gain access to over 100 e-books</p>
            </div>
            <div class="pdf-carousel">
                <button style="margin-right: 10px;" class="carousel-btn carousel-prev"><i
                        class='bx bx-chevron-left'></i></button>
                <div class="carousel-track-container">
                    <div class="carousel-track">
                        @foreach ($data as $book)
                            <div class="pdf-item">
                                <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <button class="carousel-btn carousel-next"><i class='bx bx-chevron-right'></i></button>
            </div>
        </div>

        <p class="main-text" style="margin-top: 20px;">Your Journey to Success Starts Here
            Dive into expertly written eBooks covering essential topics such as:

            Psychology: Understand the science of behavior and emotions to cultivate deeper relationships and inner
            peace.

            Sales & Negotiation: Master the art of persuasion and close deals with confidence.

            Productivity: Unlock strategies to get more done in less time, without sacrificing balance.

            Business & Career: Gain insights to accelerate your career and build successful businesses.

            Money & Investments: Learn how to manage your finances and grow your wealth wisely.
        </p><br>

        <p class="main-text">Why Choose Elevate Reads?

            Diverse Library: Access a comprehensive range of topics, all in one place.

            Expert Authors: Learn from seasoned professionals and thought leaders.

            Flexible Reading: Enjoy instant access to eBooks anytime, anywhere.

            Tailored for Growth: Whether you’re looking to boost your career, sharpen your skills, or improve your
            mindset, we’ve got you covered.</p><br>

        <p class="faq">FREQUENTLY ASKED QUESTIONS
        </p><br>

        <button class="register-btn">Get Started</button><br>


    </div>

    <script type="module">
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

        // Carousel functionality
        const track = document.querySelector('.carousel-track');
        const slides = Array.from(track.children);
        const nextButton = document.querySelector('.carousel-next');
        const prevButton = document.querySelector('.carousel-prev');
        const slideWidth = slides[0].getBoundingClientRect().width;
        const gap = parseInt(getComputedStyle(track).gap); // Get the gap between slides
        const slidesPerPage = 2; // Number of slides to move at a time

        // Helper function to set initial slide positions
        const setSlidePosition = (slide, index) => {
            slide.style.left = (slideWidth + gap) * index + 'px';
        };
        slides.forEach(setSlidePosition);

        // Function to move to a specific slide
        const moveToSlide = (track, currentSlide, targetSlide) => {
            const currentSlideIndex = slides.indexOf(currentSlide);
            const targetSlideIndex = slides.indexOf(targetSlide);
            const moveAmount = (slideWidth + gap) * targetSlideIndex; // Ensure it moves by the total width of the slides including gap
            track.style.transform = 'translateX(-' + moveAmount + 'px)';
            currentSlide.classList.remove('current-slide');
            targetSlide.classList.add('current-slide');
        };

        // Get the next slide, handling wraparound
        const getNextSlide = (currentSlide) => {
            const currentSlideIndex = slides.indexOf(currentSlide);
            let nextSlideIndex = (currentSlideIndex + slidesPerPage) % slides.length;
            return slides[nextSlideIndex];
        };

        // Get the previous slide, handling wraparound
        const getPrevSlide = (currentSlide) => {
            const currentSlideIndex = slides.indexOf(currentSlide);
            let prevSlideIndex = (currentSlideIndex - slidesPerPage + slides.length) % slides.length;
            return slides[prevSlideIndex];
        };

        // Update the currentSlide variable within the event listeners
        nextButton.addEventListener('click', () => {
            const currentSlide = track.querySelector('.current-slide');
            const nextSlide = getNextSlide(currentSlide);
            moveToSlide(track, currentSlide, nextSlide);
        });

        prevButton.addEventListener('click', () => {
            const currentSlide = track.querySelector('.current-slide');
            const prevSlide = getPrevSlide(currentSlide);
            moveToSlide(track, currentSlide, prevSlide);
        });

        // Initially mark the first slide as current
        slides[0].classList.add('current-slide');


        // Dragging functionality
        let startX, currentX, initialPosition, isDragging = false;

        track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            initialPosition = track.style.transform ? parseInt(track.style.transform.split('(')[1].split('px')[0]) : 0;
            isDragging = true;
            track.style.transition = 'none';
        });

        track.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            currentX = e.touches[0].clientX;
            const moveX = currentX - startX;
            track.style.transform = `translateX(${initialPosition + moveX}px)`;
        });

        track.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            isDragging = false;
            track.style.transition = 'transform 0.3s ease-in';
            const moveX = currentX - startX;

            if (moveX < -50) {
                const currentSlide = track.querySelector('.current-slide');
                const nextSlide = currentSlide.nextElementSibling;
                if (nextSlide) {
                    moveToSlide(track, currentSlide, nextSlide);
                } else {
                    track.style.transform = `translateX(${initialPosition}px)`;
                }
            } else if (moveX > 50) {
                const currentSlide = track.querySelector('.current-slide');
                const prevSlide = currentSlide.previousElementSibling;
                if (prevSlide) {
                    moveToSlide(track, currentSlide, prevSlide);
                } else {
                    track.style.transform = `translateX(${initialPosition}px)`;
                }
            } else {
                track.style.transform = `translateX(${initialPosition}px)`;
            }
        });

    </script>

</body>

</html>