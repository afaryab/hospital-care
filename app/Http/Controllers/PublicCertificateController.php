<?php

namespace App\Http\Controllers;

use App\Models\BirthCertificate;
use App\Models\DeathCertificate;
use Illuminate\View\View;

/**
 * Unauthenticated, token-gated verification pages for Death and Birth
 * certificates — reached by scanning the QR code printed on each document.
 * Access control is the unguessable `verification_token`, not a login wall.
 */
class PublicCertificateController extends Controller
{
    public function deathCertificate(string $token): View
    {
        $certificate = DeathCertificate::with(['serviceOrder.patient'])
            ->where('verification_token', $token)
            ->firstOrFail();

        return view('public.death-certificate', [
            'certificate' => $certificate,
            'patient' => $certificate->serviceOrder->patient,
        ]);
    }

    public function birthCertificate(string $token): View
    {
        $certificate = BirthCertificate::with(['serviceOrder.patient', 'attendingDoctor'])
            ->where('verification_token', $token)
            ->where('is_locked', true)
            ->firstOrFail();

        return view('public.birth-certificate', [
            'certificate' => $certificate,
            'patient' => $certificate->serviceOrder->patient,
        ]);
    }
}
