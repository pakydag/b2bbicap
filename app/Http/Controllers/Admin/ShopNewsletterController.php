<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShopNewsletterController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query()->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('coupon_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'confirmed') {
                $query->where('confirmed', true)->where('active', true);
            } elseif ($request->status === 'pending') {
                $query->where('confirmed', false);
            } elseif ($request->status === 'unsubscribed') {
                $query->where('active', false);
            }
        }

        $subscribers = $query->paginate(25)->withQueryString();
        $totalCount = NewsletterSubscriber::count();
        $confirmedCount = NewsletterSubscriber::where('confirmed', true)->where('active', true)->count();
        $pendingCount = NewsletterSubscriber::where('confirmed', false)->count();

        return view('admin.shop.newsletter.index', compact(
            'subscribers',
            'totalCount',
            'confirmedCount',
            'pendingCount'
        ));
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return redirect()->route('admin.shop.newsletter.index')->with('success', 'Iscritto rimosso con successo.');
    }

    public function export(): StreamedResponse
    {
        $filename = 'newsletter_iscritti_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 per corretta visualizzazione in Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['ID', 'Email', 'Stato Verifica', 'Data Conferma', 'Coupon Assegnato', 'Indirizzo IP', 'Lingua', 'Stato Iscrizione', 'Data Registrazione'], ';');

            NewsletterSubscriber::orderBy('created_at', 'desc')->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    $statoVerifica = $row->confirmed ? 'Confermato (Double Opt-In)' : 'In attesa di conferma email';
                    $statoIscrizione = $row->active ? 'Attivo' : 'Disiscritto';

                    fputcsv($handle, [
                        $row->id,
                        $row->email,
                        $statoVerifica,
                        $row->confirmed_at ? $row->confirmed_at->format('d/m/Y H:i:s') : 'N/D',
                        $row->coupon_code ?: 'N/D',
                        $row->ip_address ?: 'N/D',
                        strtoupper($row->locale ?: 'IT'),
                        $statoIscrizione,
                        $row->created_at ? $row->created_at->format('d/m/Y H:i:s') : 'N/D',
                    ], ';');
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
