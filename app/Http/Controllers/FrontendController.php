<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class FrontendController extends Controller
{
    
    public function index()
    {
        return view('index');
    }
    
    public function form()
    {
        return view('form');
    }

    /**
     * Display ticket details
     *
     * @param string $ticket_id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
  
    
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:tickets,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'student_id' => ['nullable', 'string', 'max:50'],
            'cnic' => ['nullable', 'string', 'max:20'],
            'department' => ['nullable', 'string', 'max:100'],
            'semester' => ['nullable', 'string', 'max:20'],
            'transaction_screenshot' => ['required', 'image', 'mimes:jpeg,jpg,png', 'max:5120'], // 5MB max
        ], [
            'email.unique' => 'This email address has already been used to purchase a ticket. Please use a different email address.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'transaction_screenshot.max' => 'Transaction screenshot must not exceed 5 MB.',
            'transaction_screenshot.uploaded' => 'The receipt could not be uploaded. Please select a JPEG or PNG no larger than 5 MB.',
        ]);

        // Handle file upload - store directly in public folder
        $screenshotPath = null;
        if ($request->hasFile('transaction_screenshot')) {
            $file = $request->file('transaction_screenshot');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Create tickets directory in public folder if it doesn't exist
            $publicPath = public_path('tickets');
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            
            // Move file to public/tickets folder
            $file->move($publicPath, $filename);
            $screenshotPath = 'tickets/' . $filename;
        }

        // Create ticket
        $ticket = Ticket::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'student_id' => $validated['student_id'] ?? null,
            'cnic' => $validated['cnic'] ?? null,
            'department' => $validated['department'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'transaction_screenshot' => $screenshotPath,
            'status' => 'pending',
        ]);

        // Generate QR code for the ticket
        $qrCodePath = $this->generateQrCode($ticket);
        
        // Update ticket with QR code path
        $ticket->update([
            'qr_code_path' => $qrCodePath
        ]);

        return redirect()->to(route('index').'#ticket-confirmation')->with([
            'success' => 'Your ticket request has been submitted successfully.',
            'submitted_ticket_id' => $ticket->ticket_id,
        ]);
    }

    /**
     * Generate QR code for a ticket
     *
     * @param Ticket $ticket
     * @return string
     */
    private function generateQrCode(Ticket $ticket): string
    {
        // Create qr-codes directory in public folder if it doesn't exist
        $publicPath = public_path('qr-codes');
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        // Generate QR code with ticket detail page URL
        $ticketUrl = route('admin.tickets.show', ['ticket_id' => $ticket->ticket_id]);
        
        // Check if Imagick extension is available for PNG generation
        $usePng = extension_loaded('imagick');
        
        if ($usePng) {
            // Generate PNG QR code (requires Imagick extension)
            $filename = 'qr_' . $ticket->ticket_id . '_' . time() . '.png';
            $filePath = $publicPath . '/' . $filename;
            
            QrCode::format('png')
                ->size(300)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($ticketUrl, $filePath);
        } else {
            // Fallback to SVG if Imagick is not available
            $filename = 'qr_' . $ticket->ticket_id . '_' . time() . '.svg';
            $filePath = $publicPath . '/' . $filename;
            
            QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->generate($ticketUrl, $filePath);
        }

        // Return relative path for storage
        return 'qr-codes/' . $filename;
    }
    
}
