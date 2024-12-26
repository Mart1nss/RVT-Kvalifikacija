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
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>    <link rel="stylesheet" href="{{ asset('css/welcome-style.css') }}">
</head>

<body>

    <header class="navbar">
        <h1 class="logo"
            style="font-family: sans-serif; color: white; cursor: pointer; font-weight: 800; font-size: 24px">
            ELEVATE
            READS</h1>

        <nav class="nav-btn">
            @if (Route::has('login'))
                @auth
                    <button onclick="window.location.href = '{{ route('home') }}'"
                        class="login-btn">Dashboard</button>
                @else
                    <button class="login-btn" style="margin-right: 14px;"
                        onclick="window.location.href = '{{ route('login') }}'">Login</button>

                    @if (Route::has('register'))
                        <button onclick="window.location.href = '{{ route('register') }}'"
                            class="register-btn">Register</button>
                    @endif
                @endauth
            @endif
        </nav>
    </header>

    <div class="main-container">
        <section class="hero">
            <h2 class="hero-title">Discover the Power of Knowledge to Transform Your Life</h2>
            <p class="hero-subtitle">Start Elevating Today</p>
            <button onclick="window.location.href = '{{ route('register') }}'" class="register-btn cta-button">Get
                Started</button>
        </section>

        <section class="library-preview">
            <div class="text-container">
                <h3 class="section-title">Inside the Library</h3>
                <p class="section-subtitle">Gain access to over 100 e-books</p>
            </div>
            <div class="pdf-carousel">
                <div class="carousel-track-container">
                    <div class="carousel-track">
                        @foreach ($data as $book)
                            <div class="pdf-item">
                                <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section class="benefits">
            <h3 class="section-title">Your Journey to Success Starts Here</h3>
            <p class="section-text">
                Dive into eBooks covering essential topics such as:
            </p>
        
            <div class="genre-cards">
        
                <div class="genre-card">
                    <div class="genre-title">
                        <div class="icon-container">
                            <i class='bx bxs-bulb'></i> 
                        </div>
                        <h4 class="genre-title-text">Psychology</h4>
                    </div>
                    <p class="genre-description">Understand the science of behavior and emotions to cultivate deeper relationships and inner peace.</p>
                </div>
        
                <div class="genre-card">
                    <div class="genre-title">
                        <div class="icon-container">
                            <i class='bx bxs-phone'></i>
                        </div>
                        <h4 class="genre-title-text">Sales & Negotiation</h4>
                    </div>
                    <p class="genre-description">Master the art of persuasion and close deals with confidence.</p>
                </div>
        
                <div class="genre-card">
                    <div class="genre-title">
                        <div class="icon-container">
                            <i class='bx bxs-chart'></i>
                        </div>
                        <h4 class="genre-title-text">Productivity</h4>
                    </div>
                    <p class="genre-description">Unlock strategies to get more done in less time, without sacrificing balance.</p>
                </div>
        
                <div class="genre-card">
                    <div class="genre-title">
                        <div class="icon-container">
                            <i class='bx bxs-briefcase'></i>
                        </div>
                        <h4 class="genre-title-text">Business & Career</h4>
                    </div>
                    <p class="genre-description">Gain insights to accelerate your career and build successful businesses.</p>
                </div>
        
                <div class="genre-card">
                    <div class="genre-title">
                        <div class="icon-container">
                            <i class='bx bxs-wallet'></i>
                        </div>
                        <h4 class="genre-title-text">Money & Investments</h4>
                    </div>
                    <p class="genre-description">Learn how to manage your finances and grow your wealth wisely.</p>
                </div>
        
                <div class="genre-card">
                    <div class="genre-title">
                        <div class="icon-container">
                            <i class='bx bxs-heart'></i>
                        </div>
                        <h4 class="genre-title-text">Health & Wellness</h4>
                    </div>
                    <p class="genre-description">Discover holistic approaches to maintain physical and mental well-being for a fulfilling life.</p>
                </div>
            </div>
        </section>

        <section class="why-choose">
            <h3 class="section-title">Why Choose Elevate Reads?</h3>
            <ul class="why-choose-list">
                hz
                <button onclick="window.location.href = '{{ route('register') }}'" class="register-btn cta-button">Get
                    Started</button>
            </ul>
            
        </section>

        <section class="faq-section">
            <h3 class="section-title">Frequently Asked Questions</h3>
            <div class="accordion">
                <div class="accordion-item">
                    <button class="accordion-header">
                        What types of eBooks do you offer?
                        <i class='bx bx-chevron-down'></i>
                    </button>
                    <div class="accordion-content">
                        <div class="content-wrapper">
                            <p>We offer a wide range of eBooks across various categories, including psychology, sales,
                                negotiation, productivity, business, career development, money management, and
                                investments.</p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        How do I access the eBooks?
                        <i class='bx bx-chevron-down'></i>
                    </button>
                    <div class="accordion-content">
                        <div class="content-wrapper">
                            <p>Once you've registered and logged in, you can access our entire library of eBooks through
                                your
                                dashboard. You can read them online or download them for offline access.</p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Are the eBooks compatible with all devices?
                        <i class='bx bx-chevron-down'></i>
                    </button>
                    <div class="accordion-content">
                        <div class="content-wrapper">
                            <p>Our eBooks are available in PDF format, which is compatible with most devices, including
                                computers, tablets, and smartphones. </p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        How often is new content added?
                        <i class='bx bx-chevron-down'></i>
                    </button>
                    <div class="accordion-content">
                        <div class="content-wrapper">
                            <p>We are constantly adding new eBooks to our library to ensure you have access to the latest
                                insights and knowledge in various fields.</p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Is there a subscription fee?
                        <i class='bx bx-chevron-down'></i>
                    </button>
                    <div class="accordion-content">
                        <div class="content-wrapper">
                            <p>We offer both free and premium content. While some eBooks are available for free, others
                                may
                                require a one-time purchase or be part of a subscription plan. </p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Can I suggest topics or authors for future eBooks?
                        <i class='bx bx-chevron-down'></i>
                    </button>
                    <div class="accordion-content">
                        <div class="content-wrapper">
                            <p>Absolutely! We value your input and encourage you to suggest topics or authors you'd like
                                to
                                see in our library. You can send us your suggestions through our contact form.</p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        How do I get support if I have a question or issue?
                        <i class='bx bx-chevron-down'></i>
                    </button>
                    <div class="accordion-content">
                        <div class="content-wrapper">
                            <p>If you have any questions or need assistance, you can reach out to our support team
                                through
                                our contact form or email. We're here to help!</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <script src="{{ asset('js/pdfThumbnails.js') }}" type="module"></script>
    
    <script>
        const accordionHeaders = document.querySelectorAll('.accordion-header');

        accordionHeaders.forEach(header => {
            header.addEventListener('click', () => {
                const accordionItem = header.parentElement;
                const accordionContent = header.nextElementSibling;

                accordionItem.classList.toggle('active');

                if (accordionItem.classList.contains('active')) {
                    accordionContent.style.maxHeight = accordionContent.scrollHeight + 'px';
                } else {
                    accordionContent.style.maxHeight = 0;
                }
            });
        });
    </script>

</body>

</html>