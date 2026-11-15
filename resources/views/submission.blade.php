@extends('layouts.frontend')

@section('page_title', "Submit Your Work | IAC's 4th Film Festival 2026")
@section('page_description', "Submit your work for IAC Film Fest-26, IAC's 4th Film Festival in 2026. Review registration fees, screening dates, and deadlines, then complete the official form.")

@section('content')
<section class="submission-page" aria-labelledby="submission-title">
    <div class="container">
        <div class="submission-hero">
            <div class="submission-intro">
                <span class="submission-eyebrow">IAC's 4th Film Festival · 2026</span>
                <h1 id="submission-title">Just One<br><em>Step Away.</em></h1>
                <p class="submission-lead">We're accepting submissions for short films, documentaries, music videos, and animated shorts. Complete the registration form to submit your work for IAC Film Fest-26, our 4th Film Festival.</p>
                <a class="btn submission-primary" href="https://forms.gle/6rJDdPbYW5a2U42aA" target="_blank" rel="noopener noreferrer">Fill Form Now <i class="bi bi-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> (opens Google Forms in a new tab)</span></a>
                <div class="submission-screening"><i class="bi bi-calendar3" aria-hidden="true"></i><div><span>Screening dates</span><strong>9-10-11 December 2026</strong><small>IAC Amphitheatre · Lahore</small></div></div>
            </div>
            <div class="submission-art" role="img" aria-label="Golden IAC Film Festival logo"></div>
        </div>
        <div class="submission-categories" aria-label="Submission categories">
            <span><i class="bi bi-film" aria-hidden="true"></i> Short Film</span>
            <span><i class="bi bi-camera-reels" aria-hidden="true"></i> Documentary</span>
            <span><i class="bi bi-music-note-beamed" aria-hidden="true"></i> Music Video</span>
            <span><i class="bi bi-stars" aria-hidden="true"></i> Animated Shorts</span>
        </div>
        <div class="submission-fees-heading">
            <div><span class="submission-eyebrow">Make your next move</span><h2>Submission &amp; registration fees</h2></div>
            <p>Registration is open for Film Fest-26.<br>Review the submission categories, registration fees, and deadlines below before completing your form.</p>
        </div>
        <div class="submission-fees">
            <article class="submission-fee submission-fee-early">
                <div class="submission-fee-top"><span>01 / Early Bird</span><i class="bi bi-lightning-charge" aria-hidden="true"></i></div>
                <p class="submission-amount"><small>Rs.</small> 1,000</p>
                <h3>A head start for your next story.</h3>
                <p class="submission-deadline"><i class="bi bi-calendar-check" aria-hidden="true"></i> Valid through <strong>25 November 2026</strong></p>
            </article>
            <article class="submission-fee">
                <div class="submission-fee-top"><span>02 / Regular Registration</span><i class="bi bi-camera-reels" aria-hidden="true"></i></div>
                <p class="submission-amount"><small>Rs.</small> 1,500</p>
                <h3>Your place in the festival starts here.</h3>
                <p class="submission-deadline"><i class="bi bi-calendar-check" aria-hidden="true"></i> Deadline: <strong>5 December 2026, 11:59 PM</strong></p>
            </article>
        </div>
        @include('partials.social-night-section')
        <section class="submission-rules" aria-labelledby="submission-rules-title">
            <div class="submission-fees-heading">
                <div><span class="submission-eyebrow">Before you submit</span><h2 id="submission-rules-title">Film submission guidelines</h2></div>
                <p>Check your eligibility and prepare your film before completing the registration form.</p>
            </div>
            <dl class="submission-rules-list">
                <div><dt>Who can submit</dt><dd>Students, independent filmmakers, and emerging artists from around the world are welcome. Collaborative projects between institutions are encouraged.</dd></div>
                <div><dt>Original work</dt><dd>All submitted works must be original and free of copyright infringement.</dd></div>
                <div><dt>File format &amp; quality</dt><dd>Submit your film in MP4 or MOV format at 1080p (Full HD), with good sound quality. Entries with video or audio issues that hinder viewing may be rejected by the selection committee.</dd></div>
                <div><dt>Language &amp; subtitles</dt><dd>Films in languages other than English or Urdu must include English subtitles.</dd></div>
                <div><dt>Submission deadline</dt><dd>All entries must be received by <strong>5 December 2026, 11:59 PM</strong>. Late submissions will not be accepted. Early Bird registration closes on <strong>25 November 2026</strong>.</dd></div>
                <div><dt>Content restrictions</dt><dd>Films containing hate speech, nudity, or content promoting violence, discrimination, or political propaganda will be disqualified.</dd></div>
                <div><dt>Selection process</dt><dd>All entries undergo an initial screening for abusive language. An independent festival jury makes the final selections for screening and awards. Jury decisions are final and binding.</dd></div>
                <div><dt>Screening rights &amp; ownership</dt><dd>By submitting, you grant the festival non-exclusive rights to exhibit your film during the festival and at related promotional events. You retain full ownership of your work.</dd></div>
                <div><dt>Compliance &amp; acceptance</dt><dd>Organizers may reject, re-categorize, or withdraw films that do not comply with the rules or meet festival standards. Participation means you have read, understood, and agreed to the festival rules and regulations.</dd></div>
            </dl>
        </section>
        <div class="submission-register">
            <div><span class="submission-eyebrow"><i class="bi bi-trophy" aria-hidden="true"></i> Cash prizes await</span><h2>Ready to Submit Your Work?</h2><p>Your next step is simple. Fill out the official registration form to submit your short film, documentary, music video, or animated short.</p><p class="submission-entry-notice"><i class="bi bi-info-circle" aria-hidden="true"></i><strong>One member from each participating team is eligible for complimentary entry to Social Night.</strong></p></div>
            <a class="btn submission-primary" href="https://forms.gle/6rJDdPbYW5a2U42aA" target="_blank" rel="noopener noreferrer">Fill Form Now <i class="bi bi-arrow-up-right" aria-hidden="true"></i><span class="visually-hidden"> (opens Google Forms in a new tab)</span></a>
        </div>
    </div>
</section>
@endsection
