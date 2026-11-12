@extends('layouts.frontend')

@section('content')
    @if (session('submitted_ticket_id'))
        <section class="order-confirmation" id="ticket-confirmation" aria-labelledby="confirmation-title">
            <div class="container">
                <div class="confirmation-card" role="status">
                    <div class="confirmation-icon" aria-hidden="true"><i class="bi bi-check2"></i></div>
                    <p class="confirmation-eyebrow">Film Fest-26 · Booking received</p>
                    <h1 id="confirmation-title">You're one step closer.</h1>
                    <p class="confirmation-intro">Your ticket request has been submitted successfully. Our team will review your payment receipt next.</p>
                    <div class="confirmation-ticket">
                        <div><span class="confirmation-label">Your ticket ID</span><strong>{{ session('submitted_ticket_id') }}</strong></div>
                        <span class="confirmation-status"><i class="bi bi-clock" aria-hidden="true"></i> Awaiting verification</span>
                    </div>
                    <ol class="confirmation-steps">
                        <li class="is-complete"><span aria-hidden="true"><i class="bi bi-check2"></i></span><div><strong>Request received</strong><p>Your details and payment receipt have been saved.</p></div></li>
                        <li><span aria-hidden="true">02</span><div><strong>Payment review</strong><p>Our team will verify your receipt before confirming your ticket.</p></div></li>
                        <li><span aria-hidden="true">03</span><div><strong>Check your email</strong><p>Your ticket confirmation will be emailed after approval.</p></div></li>
                    </ol>
                    <p class="confirmation-note"><i class="bi bi-bookmark-check" aria-hidden="true"></i> Save your ticket ID for reference. Your ticket is pending until verification is complete.</p>
                    <a class="btn custom-btn" href="{{ route('index') }}">Back to the festival <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i></a>
                </div>
            </div>
        </section>
    @elseif (session('success'))
        <div class="container order-success-fallback"><div class="alert alert-success" role="status">{{ session('success') }}</div></div>
    @endif
    <section class="hero-section" id="section_1">
        <div class="section-overlay"></div>

        <div class="container d-flex justify-content-center align-items-center">
            <div class="row">

                <div class="col-12 mt-auto mb-4 mb-md-5 text-center hero-content">
                    <span class="festival-logo festival-logo-hero"><img src="{{ asset('frontend/images/filmfestlogo.png') }}" alt="IAC Film Festival logo"></span>
                    <small class="d-block mb-2 mb-md-3">Organized By</small>

                    <h1 class="text-white mb-3 mb-md-5 hero-title">Film & TV Society</h1>

                    <a class="btn custom-btn hero-begin-btn smoothscroll mb-4 mb-md-0" href="#section_2">Let's begin</a>
                </div>

                <div class="col-lg-12 col-12 mt-auto mt-md-4 d-flex flex-column flex-lg-row text-center hero-bottom-info">
                    <div class="date-wrap mb-3 mb-lg-0">
                        <h5 class="text-white mb-0">
                            <i class="custom-icon bi-clock me-2"></i>
                            10-11<sup>th</sup>, Dec 2025
                        </h5>
                    </div>

                    <div class="location-wrap mx-auto py-3 py-lg-0 mb-3 mb-lg-0">
                        <h5 class="text-white mb-0">
                            <i class="custom-icon bi-geo-alt me-2"></i>
                            IAC Amphitheatre
                        </h5>
                    </div>

                    <div class="social-share">
                        <ul class="social-icon d-flex align-items-center justify-content-center mb-0">
                            <span class="text-white me-3">Follow us:</span>

                            {{-- <li class="social-icon-item">
                                    <a href="#" class="social-icon-link">
                                        <span class="bi-facebook"></span>
                                    </a>
                                </li>

                                <li class="social-icon-item">
                                    <a href="#" class="social-icon-link">
                                        <span class="bi-twitter"></span>
                                    </a>
                                </li> --}}

                            <li class="social-icon-item">
                                <a href="https://www.instagram.com/iac_ftvsociety/" target="_blank"
                                    class="social-icon-link">
                                    <span class="bi-instagram"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="video-wrap">
            <video autoplay="" loop="" muted="" class="custom-video" poster="">
                <source src="frontend/video/festival.mp4" type="video/mp4">

                Your browser does not support the video tag.
            </video>
        </div>
    </section>

   
    <section class="about-section society-story section-padding" id="section_2" aria-labelledby="society-title">
        <div class="container">
            <div class="society-story-grid">
                <div class="society-copy services-info">
                    <span class="society-eyebrow"><span aria-hidden="true"></span> Behind the stories</span>
                    <h2 id="society-title">A community.<br>A camera.<br><em>Endless possibilities.</em></h2>
                    <p class="society-lead">Film &amp; TV Society at the Institute for Art and Culture.</p>
                    <p class="society-description">In collaboration with the Department of Film &amp; TV, we bring together students who share a passion for cinema, television, and digital media. Through screenings, workshops, and creative projects, we connect classroom learning with real-world practice.</p>
                    <p class="society-description">We believe stories can inspire and connect. Here, students experiment, collaborate, and find their voice as the next generation of filmmakers and media creators.</p>
                    <a class="society-team-link" href="#section_3">Meet the people behind it <i class="bi bi-arrow-down-right" aria-hidden="true"></i></a>
                </div>
                <div class="society-visual about-text-wrap">
                    <figure class="society-photo">
                        <div class="society-frame-label"><span><i class="bi bi-camera-reels" aria-hidden="true"></i> IAC Film &amp; TV Society</span><span aria-hidden="true">01 / Our people</span></div>
                        <img src="{{ asset('frontend/images/leaders/team.png') }}" alt="Film and TV Society team gathered at the Institute for Art and Culture" loading="lazy" width="960" height="540">
                        <figcaption>Different perspectives. One creative community.</figcaption>
                    </figure>
                    <div class="society-legacy"><strong>05<span>years of creativity</span></strong><p>Growing together through cinema, collaboration, and the art of storytelling.</p></div>
                </div>
            </div>
            <div class="society-pillars" aria-label="What we do">
                <div><span class="society-pillar-icon" aria-hidden="true"><i class="bi bi-film"></i></span><div><h3>Watch &amp; discover</h3><p>New stories. Fresh perspectives.</p></div></div>
                <div><span class="society-pillar-icon" aria-hidden="true"><i class="bi bi-camera-video"></i></span><div><h3>Learn &amp; create</h3><p>Workshops that turn ideas into practice.</p></div></div>
                <div><span class="society-pillar-icon" aria-hidden="true"><i class="bi bi-people"></i></span><div><h3>Connect &amp; collaborate</h3><p>A shared space for creative voices.</p></div></div>
            </div>
        </div>
    </section>


    <section class="artists-section section-padding" id="section_3">
        <div class="container">
            <div class="row justify-content-center">

                <div class="col-12 text-center">
                    <p class="leadership-eyebrow">The people behind the festival</p><h2 class="mb-4">Current Leadership</h2>
                </div>

                <div class="col-lg-10 col-12">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-12 mb-4 mb-md-4">
                            <div class="artists-thumb h-100">
                                <div class="artists-image-wrap">
                                    <img src="{{ asset('frontend/images/leaders/sir-sikandar.png') }}"
                                        class="artists-image img-fluid" alt="Head of Department">
                                </div>

                                <div class="leadership-details"><p class="leadership-role">Head of Department</p><h3 class="leadership-name">Mr. Sikandar Javed</h3></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="artists-thumb h-100">
                                <div class="artists-image-wrap">
                                    <img src="{{ asset('frontend/images/leaders/sir-ali.jpg') }}"
                                        class="artists-image img-fluid" alt="Festival Director">
                                </div>

                                <div class="leadership-details"><p class="leadership-role">Festival Director</p><h3 class="leadership-name">Mr. Ali Sultan</h3></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="artists-thumb h-100">
                                <div class="artists-image-wrap">
                                    <img src="{{ asset('frontend/images/leaders/sir-umar.png') }}"
                                        class="artists-image img-fluid" alt="Assistant Professor">
                                </div>

                                <div class="leadership-details"><p class="leadership-role">Assistant Professor</p><h3 class="leadership-name">Mr. Umar Farooq (Asst. Prof)</h3></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="artists-thumb h-100">
                                <div class="artists-image-wrap">
                                    <img src="{{ asset('frontend/images/leaders/shah-sab.jpg') }}"
                                        class="artists-image img-fluid" alt="President">
                                </div>

                                <div class="leadership-details"><p class="leadership-role">President</p><h3 class="leadership-name">Syed Jaffar Shah</h3></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="artists-thumb h-100">
                                <div class="artists-image-wrap">
                                    <img src="{{ asset('frontend/images/leaders/faisal-nadeem.jpg') }}"
                                        class="artists-image img-fluid" alt="Fest President">
                                </div>

                                <div class="leadership-details"><p class="leadership-role">Fest President</p><h3 class="leadership-name">Faisal Nadeem</h3></div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-12 mb-4">
                            <div class="artists-thumb h-100">
                                <div class="artists-image-wrap">
                                    <img src="{{ asset('frontend/images/leaders/haziq.jpg') }}"
                                        class="artists-image img-fluid" alt="Vice President">
                                </div>

                                <div class="leadership-details"><p class="leadership-role">Vice President</p><h3 class="leadership-name">Haziq Ammar</h3></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-lg-4 col-12">
                    <div class="artists-thumb">
                        <div class="artists-image-wrap">
                            <img src="frontend/images/leaders/sir-ali.jpg"
                                class="artists-image img-fluid">
                        </div>

                        <div class="leadership-details"><p class="leadership-role">Festival Director</p><h3 class="leadership-name">Mr. Ali Sultan</h3></div>
                    </div>

                    <div class="artists-thumb">
                        <img src="frontend/images/leaders/sir-umar.png"
                            class="artists-image img-fluid">

                        <div class="leadership-details"><p class="leadership-role">Assistant Professor</p><h3 class="leadership-name">Mr. Umar Farooq (Asst. Prof)</h3></div>
                    </div>
                </div> --}}

            </div>
        </div>
    </section>


    <section class="schedule-section festival-schedule section-padding" id="section_4" aria-labelledby="schedule-title">
        <div class="container">
            <div class="schedule-heading">
                <div><span class="schedule-eyebrow">The festival programme</span><h2 id="schedule-title">Two days.<br>Stories that stay.</h2></div>
                <p>From the big screen to a night of music.<br>Explore what's on at Film Fest-26.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-12">
                    <article class="schedule-day-card">
                        <header class="schedule-card-header">
                            <span class="schedule-day-label">Day 01 <span>Cinema</span></span>
                            <div class="schedule-date"><strong>10</strong><span>December</span></div>
                        </header>
                        <div class="schedule-card-content">
                            <span class="schedule-event-icon" aria-hidden="true"><i class="bi bi-film"></i></span>
                            <p class="schedule-category">On the big screen</p>
                            <h3>Screening</h3>
                            <p class="schedule-description">Discover stories through four different forms of filmmaking.</p>
                            <ul class="schedule-genres" aria-label="Screening categories"><li>Short Film</li><li>Documentary</li><li>Music Video</li><li>Animated Short</li></ul>
                        </div>
                        <div class="schedule-card-bottom"><i class="bi bi-geo-alt" aria-hidden="true"></i> IAC Amphitheatre</div>
                    </article>
                </div>
                <div class="col-md-6 col-12">
                    <article class="schedule-day-card schedule-music-card">
                        <header class="schedule-card-header">
                            <span class="schedule-day-label">Day 02 <span>Live music</span></span>
                            <div class="schedule-date"><strong>11</strong><span>December</span></div>
                        </header>
                        <div class="schedule-card-content">
                            <span class="schedule-event-icon" aria-hidden="true"><i class="bi bi-music-note-beamed"></i></span>
                            <p class="schedule-category">An evening of rhythm &amp; soul</p>
                            <h3>Qawali Night</h3>
                            <p class="schedule-description">Come together for an evening of music to close the festival.</p>
                            <div class="schedule-time"><i class="bi bi-clock" aria-hidden="true"></i><span>Timing to be announced</span></div>
                        </div>
                        <div class="schedule-card-bottom"><i class="bi bi-geo-alt" aria-hidden="true"></i> IAC Amphitheatre</div>
                    </article>
                </div>
            </div>
            <div class="schedule-closing"><p><i class="bi bi-stars" aria-hidden="true"></i> Make it a festival to remember.</p><a class="btn schedule-book-btn" href="{{ route('form') }}">Book your ticket <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a></div>
        </div>
    </section>


    <section class="pricing-section section-padding festival-pricing" id="section_5" aria-labelledby="pricing-title">
        <div class="container">
            <div class="pricing-heading text-center">
                <span class="pricing-eyebrow">Your seat at the festival</span>
                <h2 id="pricing-title">Ticket Pricing</h2>
                <p>One festival. Three booking windows. Plan ahead for the best price.</p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6 col-12">
                    <article class="festival-price-card price-card-featured">
                        <div class="price-card-top"><span class="price-card-number">01 /</span><span class="price-card-badge">Lowest price</span></div>
                        <h3>Early Bird</h3>
                        <p class="price-card-window"><i class="bi bi-calendar3" aria-hidden="true"></i> Through 12 November</p>
                        <div class="price-card-amount"><span>Rs</span> 1,000</div>
                        <p class="price-card-unit">per ticket</p>
                        <div class="price-card-divider" aria-hidden="true"></div>
                        <p class="price-card-description">Book in the first window for the festival's lowest ticket price.</p>
                        <a href="{{ route('form') }}" class="btn price-card-button">Buy Ticket <i class="bi bi-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> — Early Bird</span></a>
                    </article>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <article class="festival-price-card">
                        <div class="price-card-top"><span class="price-card-number">02 /</span><i class="bi bi-ticket-perforated" aria-hidden="true"></i></div>
                        <h3>Standard</h3>
                        <p class="price-card-window"><i class="bi bi-calendar3" aria-hidden="true"></i> 13–21 November</p>
                        <div class="price-card-amount"><span>Rs</span> 1,500</div>
                        <p class="price-card-unit">per ticket</p>
                        <div class="price-card-divider" aria-hidden="true"></div>
                        <p class="price-card-description">The next booking window, with the same festival experience.</p>
                        <a href="{{ route('form') }}" class="btn price-card-button">Buy Ticket <i class="bi bi-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> — Standard</span></a>
                    </article>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <article class="festival-price-card">
                        <div class="price-card-top"><span class="price-card-number">03 /</span><i class="bi bi-ticket-perforated" aria-hidden="true"></i></div>
                        <h3>Final Window</h3>
                        <p class="price-card-window"><i class="bi bi-calendar3" aria-hidden="true"></i> From 22 November</p>
                        <div class="price-card-amount"><span>Rs</span> 2,000</div>
                        <p class="price-card-unit">per ticket</p>
                        <div class="price-card-divider" aria-hidden="true"></div>
                        <p class="price-card-description">Booking later? This is the ticket rate after 21 November.</p>
                        <a href="{{ route('form') }}" class="btn price-card-button">Buy Ticket <i class="bi bi-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> — Final Window</span></a>
                    </article>
                </div>
            </div>
            <p class="pricing-footnote"><i class="bi bi-shield-check" aria-hidden="true"></i> Submit your payment receipt when booking. Tickets are confirmed after verification.</p>
        </div>
    </section>
@endsection
