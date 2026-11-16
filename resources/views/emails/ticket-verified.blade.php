<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Your verified ticket</title></head>
<body style="margin:0;padding:24px;background:#F8F3E1;font-family:Arial,sans-serif;color:#2A180D">
<div style="max-width:600px;margin:auto;background:#fff;border:1px solid #D2C3A6;border-radius:16px;overflow:hidden">
    <div style="padding:28px;background:#2A180D;color:#F8F3E1"><p style="color:#CEAC73;font-size:12px">FILM FEST-26</p><h1 style="font-size:26px">Your ticket is verified.</h1></div>
    <div style="padding:28px;line-height:1.7">
        <p>Hello {{ $ticket->name }},</p>
        <p>Your payment has been approved. Your verified ticket and QR code are ready.</p>
        <div style="padding:18px;background:#F8F3E1;border:1px dashed #B89866;text-align:center"><span style="font-size:12px">YOUR TICKET ID</span><p style="font-size:22px;font-weight:bold;margin:8px 0">{{ $ticket->ticket_id }}</p></div>
        @if ($qrMime === 'image/png')
            <div style="text-align:center;padding:24px 0"><img src="{{ $message->embedData($qrData, 'ticket-qr-code.png', 'image/png') }}" width="240" height="240" alt="Your ticket QR code" style="max-width:100%;height:auto"></div>
        @endif
        <p>Your QR code is also attached to this email. Save it on your phone and present it at the entrance.</p>
        <p><strong>Venue:</strong> IAC Amphitheatre, Institute for Art and Culture, Lahore.</p>
        <p><strong>Festival dates:</strong> 9-10-11 December 2026.</p>
        <div style="margin:24px 0;padding:20px;background:#F8F3E1;border:1px solid #D2C3A6;border-radius:12px;color:#432813">
            <h2 style="margin:0 0 12px;font-size:18px;line-height:1.5;color:#432813">Prohibited Items &amp; Festival Safety</h2>
            <ul style="margin:0;padding-left:20px;font-size:14px;line-height:1.8;color:#59432F">
                <li style="margin-bottom:10px">For everyone's safety, all attendees, including students, filmmakers, and visitors, are strictly prohibited from bringing drugs, alcohol, outside food or beverages, firearms, knives, explosives, or any other dangerous or restricted items into the festival venue.</li>
                <li style="margin-bottom:10px">Anyone found carrying such items may be denied entry or removed from the premises.</li>
                <li style="margin-bottom:10px">Participating filmmakers may also be disqualified, and their films may be withdrawn from screening.</li>
                <li>All attendees must comply with venue security checks and festival safety regulations.</li>
            </ul>
        </div>
        <p>Please keep your ticket and QR code private. We look forward to seeing you at the festival.</p>
        <p>Film &amp; TV Society<br>Institute for Art and Culture</p>
    </div>
</div>
</body></html>
