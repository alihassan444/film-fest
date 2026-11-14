@extends('layouts.frontend')

@section('content')
<section class="ticket-section section-padding booking-page" aria-labelledby="booking-title">
    <div class="container">
        <header class="booking-heading">
            <a href="{{ route('index') }}" class="booking-back"><i class="bi bi-arrow-left" aria-hidden="true"></i> Back to the festival</a>
            <span class="booking-eyebrow">Film Fest-26 / Ticket booking</span>
            <h1 id="booking-title">Your festival starts here.</h1>
            <p>Share your details, make your payment, and upload your receipt. We'll take it from there.</p>
        </header>
        <form class="custom-form ticket-form ticket-booking" action="{{ route('tickets.store') }}" method="post" enctype="multipart/form-data">
            @csrf
            @if ($errors->any())
                <div class="booking-errors alert alert-danger" role="alert">
                    <strong>Please check your details</strong>
                    <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    <p class="mb-0 mt-2">Please select your receipt again before submitting.</p>
                </div>
            @endif
            @if (session('success'))
                <div class="booking-errors alert alert-success" role="status">{{ session('success') }}</div>
            @endif
            <div class="booking-details">
                <div class="booking-section-heading"><span>01</span><div><h2>Your details</h2><p>Let us know who's joining the festival.</p></div></div>
                <p class="booking-required-note">Fields marked * are required.</p>
                @php
                    $ticketFields = [
                        ['name' => 'name', 'label' => 'Full name', 'type' => 'text', 'required' => true, 'placeholder' => 'Your full name', 'max' => 255, 'autocomplete' => 'name'],
                        ['name' => 'email', 'label' => 'Email address', 'type' => 'email', 'required' => true, 'placeholder' => 'you@example.com', 'max' => 255, 'autocomplete' => 'email'],
                        ['name' => 'phone', 'label' => 'Phone number', 'type' => 'tel', 'required' => false, 'placeholder' => '0300-0000000', 'max' => 20, 'autocomplete' => 'tel'],
                        ['name' => 'student_id', 'label' => 'Student ID / Roll number', 'type' => 'text', 'required' => true, 'placeholder' => 'Your student ID', 'max' => 50, 'autocomplete' => 'off'],
                        ['name' => 'department', 'label' => 'Department', 'type' => 'text', 'required' => true, 'placeholder' => 'Your department', 'max' => 100, 'autocomplete' => 'off'],
                        ['name' => 'semester', 'label' => 'Semester', 'type' => 'text', 'required' => true, 'placeholder' => 'e.g. 3', 'max' => 20, 'autocomplete' => 'off'],
                        ['name' => 'cnic', 'label' => 'Outsider CNIC', 'type' => 'text', 'required' => false, 'placeholder' => 'CNIC, if applicable', 'max' => 20, 'autocomplete' => 'off'],
                    ];
                @endphp
                <div class="booking-fields">
                    @foreach ($ticketFields as $field)
                        <div class="booking-field">
                            <label for="booking-{{ $field['name'] }}">{{ $field['label'] }} @if ($field['required'])<span aria-hidden="true">*</span>@else<small>Optional</small>@endif</label>
                            <input id="booking-{{ $field['name'] }}" name="{{ $field['name'] }}" type="{{ $field['type'] }}"
                                class="form-control @error($field['name']) is-invalid @enderror" value="{{ old($field['name']) }}"
                                placeholder="{{ $field['placeholder'] }}" maxlength="{{ $field['max'] }}" autocomplete="{{ $field['autocomplete'] }}"
                                @required($field['required']) @error($field['name']) aria-invalid="true" aria-describedby="error-{{ $field['name'] }}" @enderror>
                            @error($field['name'])<div class="invalid-feedback" id="error-{{ $field['name'] }}">{{ $message }}</div>@enderror
                        </div>
                    @endforeach
                </div>
                <div class="booking-email-note"><i class="bi bi-envelope-check" aria-hidden="true"></i><p>Use an email you check regularly. Your ticket confirmation will be sent there after verification.</p></div>
            </div>
            <div class="booking-payment">
                <div class="booking-section-heading"><span>02</span><div><h2>Payment &amp; receipt</h2><p>Transfer your payment, then share the receipt.</p></div></div>
                <div class="booking-bank">
                    <div class="booking-bank-header"><span><i class="bi bi-bank" aria-hidden="true"></i> Bank transfer</span><strong>SadaPay</strong></div>
                    <dl><div><dt>Account holder</dt><dd>Faisal Nadeem</dd></div><div><dt>Account number</dt><dd class="booking-account-number">03319957331</dd></div></dl>
                    <a href="{{ route('index') }}#pricing">View ticket prices <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                </div>
                <label class="booking-receipt-label" for="ticket-form-account-screenshot">Payment receipt <span aria-hidden="true">*</span></label>
                <small id="receipt-upload-help" class="booking-upload-help">JPEG or PNG / Maximum 5 MB</small>
                <div class="receipt-upload">
                    <input type="file" name="transaction_screenshot" id="ticket-form-account-screenshot" class="receipt-upload-input"
                        accept="image/jpeg,image/png" required aria-label="Choose payment receipt" aria-describedby="receipt-upload-help receipt-upload-name @error('transaction_screenshot') receipt-upload-error @enderror"
                        @error('transaction_screenshot') aria-invalid="true" @enderror>
                    <span class="receipt-upload-icon" aria-hidden="true"><i class="bi bi-cloud-arrow-up"></i></span>
                    <div class="receipt-upload-copy"><strong>Upload your screenshot</strong><span id="receipt-upload-name" role="status">No file selected</span></div>
                    <span class="receipt-upload-button" aria-hidden="true">Choose file <i class="bi bi-plus-lg"></i></span>
                </div>
                @error('transaction_screenshot')<p class="booking-upload-error" id="receipt-upload-error">{{ $message }}</p>@enderror
                <div class="booking-review-note"><i class="bi bi-shield-check" aria-hidden="true"></i><div><strong>Reviewed by our team</strong><p>Your ticket stays pending until your payment receipt is verified.</p></div></div>
            </div>
            <div class="booking-footer">
                <p><i class="bi bi-ticket-perforated" aria-hidden="true"></i> Your ticket ID will appear after submission.</p>
                <button type="submit" class="btn booking-submit">Submit ticket request <i class="bi bi-arrow-right" aria-hidden="true"></i></button>
            </div>
        </form>
    </div>
</section>
    <script>
        const receiptInput = document.getElementById('ticket-form-account-screenshot');
        function validateReceiptSize() {
            const receipt = receiptInput.files[0];
            receiptInput.setCustomValidity(receipt && receipt.size > 5 * 1024 * 1024
                ? 'Please select a receipt no larger than 5 MB.' : '');
        }
        receiptInput.addEventListener('change', function () {
            const selectedReceipt = receiptInput.files[0];
            document.getElementById('receipt-upload-name').textContent = selectedReceipt
                ? selectedReceipt.name + ' · ' + (selectedReceipt.size / (1024 * 1024)).toFixed(2) + ' MB'
                : 'Select your transaction screenshot';
            receiptInput.closest('.receipt-upload').classList.toggle('has-file', Boolean(selectedReceipt));
            validateReceiptSize();
            receiptInput.reportValidity();
        });
        receiptInput.form.addEventListener('submit', function (event) {
            validateReceiptSize();
            if (!receiptInput.checkValidity()) {
                event.preventDefault();
                receiptInput.reportValidity();
            }
        });
    </script>
@endsection