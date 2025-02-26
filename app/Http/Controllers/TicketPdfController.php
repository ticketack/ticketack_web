<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class TicketPdfController extends Controller
{
    public function generate(Ticket $ticket)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasPermissionTo('tickets.generate_pdf')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            Log::info('Début de la génération du QR code');
            
            Log::info('Génération du QR code');
            $qrUrl = url('/tickets/' . $ticket->id);
            Log::info('URL du QR code : ' . $qrUrl);
            
            // Générer le QR code directement en base64
            $qrData = QrCode::size(150)->generate($qrUrl); // Format SVG par défaut
            Log::info('Taille des données QR code brutes: ' . strlen($qrData) . ' octets');
            
            $qrCodeBase64 = base64_encode($qrData);
            Log::info('Taille des données QR code en base64: ' . strlen($qrCodeBase64) . ' octets');
            Log::info('Début des données base64: ' . substr($qrCodeBase64, 0, 50));
            
            $pdf = PDF::loadView('pdf.ticket', [
                'ticket' => $ticket->load(['creator', 'assignees', 'status', 'category', 'comments.user', 'documents', 'logs.user']),
                'qrCodeBase64' => $qrCodeBase64
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du QR code : ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            // Générer le PDF sans QR code en cas d'erreur
            $pdf = PDF::loadView('pdf.ticket', [
                'ticket' => $ticket->load(['creator', 'assignees', 'status', 'category', 'comments.user', 'documents', 'logs.user']),
                'qrCodeBase64' => null
            ]);
        }


        return response()->make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ticket-' . $ticket->id . '.pdf"'
        ]);
    }
}
