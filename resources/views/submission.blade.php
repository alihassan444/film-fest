@extends('layouts.frontend')

@section('content')
<section class="submission-page" aria-labelledby="submission-title">
    <div class="container">
        <div class="submission-hero">
            <div class="submission-intro">
                <span class="submission-eyebrow">IAC Film Festival · 2026</span>
                <h1 id="submission-title">Your story.<br><em>Our big screen.</em></h1>
                <p class="submission-lead">Every frame has a story to tell. Bring yours to Film Fest-26 and share your vision with a community that celebrates cinema.</p>
                <a class="btn submission-primary" href="https://forms.gle/6rJDdPbYW5a2U42aA" target="_blank" rel="noopener noreferrer">Registration <i class="bi bi-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> (opens Google Forms in a new tab)</span></a>
                <div class="submission-screening"><i class="bi bi-calendar3" aria-hidden="true"></i><div><span>Screening dates</span><strong>9–11 December 2026</strong><small>IAC Amphitheatre · Lahore</small></div></div>
            </div>
            <div class="submission-art" role="img" aria-label="Golden vintage movie camera with flowing cream and gold ribbons, from the official festival flyer">
                <span class="submission-art-label">The art of storytelling awaits you.</span>
                <span class="submission-art-mark" aria-hidden="true">26</span>
            </div>
        </div>
        <div class="submission-categories" aria-label="Submission categories">
            <span><i class="bi bi-film" aria-hidden="true"></i> Short Film</span>
            <span><i class="bi bi-camera-reels" aria-hidden="true"></i> Documentary</span>
            <span><i class="bi bi-music-note-beamed" aria-hidden="true"></i> Music Video</span>
            <span><i class="bi bi-stars" aria-hidden="true"></i> Animated Shorts</span>
        </div>
        <div class="submission-fees-heading">
            <div><span class="submission-eyebrow">Make your next move</span><h2>Submission &amp; registration fees</h2></div>
            <p>Choose your registration window.<br>Let your work take the spotlight.</p>
        </div>
        <div class="submission-fees">
            <article class="submission-fee submission-fee-early">
                <div class="submission-fee-top"><span>01 / Early Bird</span><i class="bi bi-lightning-charge" aria-hidden="true"></i></div>
                <p class="submission-amount"><small>Rs.</small> 1,000</p>
                <h3>A head start for your next story.</h3>
                <p class="submission-deadline"><i class="bi bi-calendar-check" aria-hidden="true"></i> Valid through <strong>15 November 2026</strong></p>
            </article>
            <article class="submission-fee">
                <div class="submission-fee-top"><span>02 / Regular Registration</span><i class="bi bi-camera-reels" aria-hidden="true"></i></div>
                <p class="submission-amount"><small>Rs.</small> 1,500</p>
                <h3>Your place in the festival starts here.</h3>
                <p class="submission-deadline"><i class="bi bi-calendar-check" aria-hidden="true"></i> Deadline: <strong>30 November 2026</strong></p>
            </article>
        </div>
        <div class="submission-register">
            <div><span class="submission-eyebrow"><i class="bi bi-trophy" aria-hidden="true"></i> Cash prizes await</span><h2>Let the next story be yours.</h2><p>Submit your film or video through the official registration form.</p></div>
            <a class="btn submission-primary" href="https://forms.gle/6rJDdPbYW5a2U42aA" target="_blank" rel="noopener noreferrer">Registration <i class="bi bi-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> (opens Google Forms in a new tab)</span></a>
        </div>
    </div>
</section>
@endsection
