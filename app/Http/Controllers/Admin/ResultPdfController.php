<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Voter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ResultPdfController extends Controller
{
    public function __invoke(): Response
    {
        abort_unless(auth()->user()?->can('view_any_result') === true, 403);

        $totalVoters = Voter::count();
        $participation = Voter::where('has_voted', true)->count();
        $turnout = $totalVoters > 0
            ? round(($participation / $totalVoters) * 100, 2)
            : 0.0;

        $positions = Position::query()
            ->with(['candidates' => function ($query) {
                $query->orderByDesc('num_votes')->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        $generatedAt = now();

        $pdf = Pdf::loadView('admin.results-pdf', [
            'positions' => $positions,
            'totalVoters' => $totalVoters,
            'participation' => $participation,
            'turnout' => $turnout,
            'generatedAt' => $generatedAt,
        ])->setPaper('a4', 'portrait');

        $filename = 'election-results-' . $generatedAt->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
