<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Welcome</title>

        <!-- Fonts -->
        <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css" integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    </head>

    
    <body>

        <style>

        * {
            padding: 0;
            margin: 0;
            font-family: sans-serif;
            box-sizing: border-box;
        }

        body {
            background-color: #1c1a1a;
            min-width: 375px;
            align-items: center;
            justify-content: center;
            font-family: sans-serif;
        }

        .navbar {
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 50px;
            background: #1c1a1a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 999;
        }

        h1 {
            color: #fff;
            font-size: 30px;
            text-transform: uppercase;
            font-weight: 700;
            position: relative;
        }


        nav {
            display: flex;
            align-items: center;
        }

        .nav-a a {
            position: relative;
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-family: sans-serif;
            font-weight: 700;
            cursor: pointer;
            font-size: 17px;
            text-transform: uppercase;
        }

        .nav-a .active {
            border-bottom: 4px solid #ffffff
        }

        .nav-a a::before {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            width: 0;
            height: 4px;
            background: #ffffff;
            transition: 0.2s
        }

        .nav-a a:hover::before {
            width: 100%;
        }

        .register-btn {
            border: 1px solid white;
            background-color: white;
            padding: 10px;
            height: 40px;
            width: 120px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            transition: all 0.15;
        }

        .login-btn {
            border: 1px solid white;
            background-color: rgb(37, 37, 37);
            color: white;
            padding: 10px;
            height: 40px;
            width: 120px;
            border-radius: 20px;
            font-weight: 800;
            margin-left: 20px;
            font-size: 12px;
            text-transform: uppercase;
            transition: all 0.15;
        }

        button:hover {
            cursor: pointer;
            opacity: 0.7;
        }


        @media (max-width: 769px) {

            .navbar {
                padding: 0 10px;
                padding-top: 10px;
                flex-direction: column;
                display: flex;
            }

            .navbar .nav-a a {
                display: none;
            }

            .navbar h1 {
                justify-content: center;
                text-align: center;
            }

            .nav-btn{
                display: flex;
                width: 100%;
                margin-top: 1%;
                margin-bottom: 1.5%;
            }

            .login-btn {
                float: left;
                margin-left: 0;
            }

            .navbar button {
                align-items: center;
                justify-content: center;
                width: 100%;
            }
        }

        p {
            font-weight: 800;
            margin-left: 20px;
            font-size: 12px;
            text-transform: uppercase;
            color: white;
        }



        .item-container2 {
            background-color: rgb(37, 37, 37);
            border-radius: 10px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
            grid-gap: 20px; 
            justify-content: start; 
            padding: 20px; 
  }

  .item-card {
        background-color: #1c1a1a;
      color: white;
      border-radius: 10px;
      border: white 1px solid;
      overflow: hidden;
      position: relative;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: 0.15s;
      height: 400px;
    }


.main-container {
    background-color: #1c1a1a;
    padding: 0 50px;
    display: flex;
    flex-direction: column;
    width: 100%;
    height:min-content;
    padding-bottom: 30px;

    @media (max-width: 769px) {
        padding: 0 10px;
        margin-top: 12px;
    }
}

.item-container {
    background-color: rgb(37, 37, 37);
    border-radius: 10px;
    padding: 16px;
}

.pdf-carousel {
    display: flex;
    align-items: center;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
}

.carousel-track-container {
    overflow: hidden;
    width: 100%;
    touch-action: pan-y;
}

.carousel-track {
    display: flex;
    transition: transform 0.3s ease-in;
    gap: 13px; 
}

.pdf-item {
    width: calc(25% - 12px);
    flex: 0 0 auto;
    background-color: #1c1a1a;
    color: white;
    border-radius: 10px;
    border: white 1px solid;
    overflow: hidden;
    box-sizing: border-box;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    scroll-snap-align: start;
    transition: 0.15s;
}

.thumbnail img {
    max-width: 100%;
    object-fit: contain;
    height: 100%;
    width: 100%;
}

.carousel-btn {
    height: 40px;
    width: 40px;
    background: white;
    text-align: center;
    color: black;
    line-height: 40px;
    border-radius: 50%;
    border: none;
    cursor: pointer;
    font-size: 1.25rem;
}

@media (max-width: 769px) { 
    .pdf-item {
        flex: 0 0 calc(50% - 10px); /* Two books per row on mobile */
        min-width: calc(50% - 10px);
    }
    .carousel-track {
        gap: 10px; 
    }

    .carousel-btn {
        display: none;
    }
}

.text-container{
  background-color: rgb(37, 37, 37);
  color: white;
  text-align: center;
  padding: 16px;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}


        </style>

        <header class="navbar">
            <h1 class="logo" style="font-family: sans-serif; color: white; cursor: pointer; font-weight: 800">LOGO</h1>
    
            <nav class="nav-btn">
                @if (Route::has('login'))
                    @auth
                        <button onclick="window.location.href = '{{ route('home') }}'" class="login-btn">Dashboard</button>
                    @else
                    <button class="login-btn" style="margin-right: 14px;" onclick="window.location.href = '{{ route('login') }}'">Login</button>    

                        @if (Route::has('register'))
                            <button onclick="window.location.href = '{{ route('register') }}'" class="register-btn">Register</button>
                        @endif
                    @endauth
                @endif
            </nav>
        </header>



        <div class="main-container">
            <div class="item-container">
                <div class="text-container">
                <h1>INSIDE THE LIBRARY</h1>
                <p>Gain access to over 100 e-books</p>
                </div>
                <div class="pdf-carousel">
                    <button style="margin-right: 10px;" class="carousel-btn carousel-prev"><i class='bx bx-chevron-left'></i></button>
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
        </div>


        <script type="module">
            // Book Thumbnails
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';
            function generateThumbnail(pdfPath) {
                pdfjsLib.getDocument(pdfPath).promise.then(function(pdf) {
                    pdf.getPage(1).then(function(page) {
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
        
                        page.render(renderContext).promise.then(function() {
                            var thumbnailImg = document.createElement('img');
                            thumbnailImg.src = canvas.toDataURL();
        
                            var thumbnailDiv = document.querySelector('.thumbnail[data-pdfpath="' + pdfPath + '"]');
                            thumbnailDiv.innerHTML = '';
                            thumbnailDiv.appendChild(thumbnailImg);
                        });
                    });
                }).catch(function(error) {
                    console.error("Error loading PDF:", error);
                });
            }
        
            document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(function(thumbnailDiv) {
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



<!--
<div style="margin-top: 20px;" class="item-container2">
    <div class="item-card">

    </div>
    <div class="item-card">
        
    </div>
    <div class="item-card">
        
    </div>
    <div class="item-card">
        
    </div>
</div> -->
        
    </body>
</html>
