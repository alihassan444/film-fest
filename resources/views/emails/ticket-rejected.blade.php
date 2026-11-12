<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Ticket request update</title></head>
<body style="margin:0;padding:24px;background:#F8F3E1;font-family:Arial,sans-serif;color:#2A180D">
<div style="max-width:600px;margin:auto;background:#fff;border:1px solid #D2C3A6;border-radius:16px;overflow:hidden">
    <div style="padding:28px;background:#2A180D;color:#F8F3E1"><p style="color:#CEAC73;font-size:12px">FILM FEST-26</p><h1 style="font-size:24px">An update on your ticket request</h1></div>
    <div style="padding:28px;line-height:1.7">
        <p>Hello {{ $ticket->name }},</p>
        <p>Your ticket request <strong>{{ $ticket->ticket_id }}</strong> has been rejected following review.</p>
        <div style="padding:18px;background:#F8F3E1;border-left:3px solid #997548"><strong>Reason for rejection</strong><p style="margin-bottom:0;white-space:pre-line">{{ $ticket->rejection_reason }}</p></div>
        <p>This request is not valid for admission. If you need clarification, contact the festival organisers and quote your ticket ID.</p>
        <p>Film &amp; TV Society<br>Institute for Art and Culture</p>
    </div>
</div>
</body></html>
