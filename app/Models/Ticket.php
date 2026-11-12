<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'student_id',
        'cnic',
        'department',
        'semester',
        'transaction_screenshot',
        'ticket_id',
        'status',
        'verification_token',
        'verified_at',
        'checked_in_at',
        'qr_code_path',
        'rejection_reason',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_id)) {
                $ticket->ticket_id = 'TKT-' . strtoupper(Str::random(10));
            }
        });
    }

    /**
     * Get the verification URL for this ticket.
     *
     * @return string|null
     */
    public function getVerificationUrl(): ?string
    {
        if (!$this->verification_token) {
            return null;
        }

        return route('api.verify-ticket', ['token' => $this->verification_token]);
    }
}

