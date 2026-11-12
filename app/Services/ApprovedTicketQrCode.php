<?php

namespace App\Services;

use App\Models\Ticket;
use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ApprovedTicketQrCode
{
    /** @return array{data: string, mime: string, extension: string} */
    public function generate(Ticket $ticket): array
    {
        if (! in_array($ticket->status, ['verified', 'checked_in'], true) || ! $ticket->verification_token) {
            throw new \LogicException('QR delivery is only available for verified tickets.');
        }

        $url = route('admin.tickets.show', ['ticket_id' => $ticket->ticket_id]);
        if (! extension_loaded('gd')) {
            return ['data' => (string) QrCode::format('svg')->size(320)->margin(4)->generate($url), 'mime' => 'image/svg+xml', 'extension' => 'svg'];
        }

        // Render one lossless PNG without requiring Imagick or temporary files.
        $matrix = Encoder::encode($url, ErrorCorrectionLevel::H())->getMatrix();
        $scale = 8;
        $margin = 4;
        $size = ($matrix->getWidth() + $margin * 2) * $scale;
        $image = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocate($image, 0, 0, 0);
        imagefill($image, 0, 0, $white);
        for ($y = 0; $y < $matrix->getHeight(); $y++) {
            for ($x = 0; $x < $matrix->getWidth(); $x++) {
                if ($matrix->get($x, $y) === 1) {
                    $left = ($x + $margin) * $scale;
                    $top = ($y + $margin) * $scale;
                    imagefilledrectangle($image, $left, $top, $left + $scale - 1, $top + $scale - 1, $black);
                }
            }
        }
        ob_start();
        try {
            imagepng($image);
            $data = ob_get_contents();
        } finally {
            ob_end_clean();
            imagedestroy($image);
        }

        return ['data' => $data, 'mime' => 'image/png', 'extension' => 'png'];
    }
}
