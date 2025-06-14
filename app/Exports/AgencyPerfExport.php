<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use App\Models\Agency;

class AgencyPerfExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $start = $this->request->input('start_date');
        $end = $this->request->input('end_date');
        $agencyFilter = $this->request->input('agency_id');
        $category = $this->request->input('category');

        $agencies = Agency::with(['user', 'inquiries.progressUpdates'])->get();

        $data = $agencies->map(function ($agency) use ($start, $end, $agencyFilter, $category) {
            if ($agencyFilter && $agency->id != $agencyFilter) return null;

            $inquiries = $agency->inquiries->filter(function ($inq) use ($start, $end, $category) {
                $valid = true;
                if ($start) $valid = $valid && $inq->created_at >= $start;
                if ($end) $valid = $valid && $inq->created_at <= $end;
                if ($category) $valid = $valid && $inq->category === $category;
                return $valid;
            });

            $resolved = $inquiries->filter(fn($inq) =>
                $inq->progressUpdates->contains(fn($p) => in_array($p->ProgressStatus, ['Verified as True', 'Identified as Fake']))
            );

            $pending = $inquiries->reject(fn($inq) =>
                $inq->progressUpdates->contains(fn($p) => in_array($p->ProgressStatus, ['Verified as True', 'Identified as Fake']))
            );

            $avgResolveTime = $resolved->map(function ($inq) {
                $start = $inq->created_at;
                $end = $inq->progressUpdates
                    ->whereIn('ProgressStatus', ['Verified as True', 'Identified as Fake'])
                    ->sortByDesc('created_at')
                    ->first();
                return $end ? $start->diffInHours($end->created_at) : null;
            })->filter()->avg();

            $delayed = $resolved->filter(function ($inq) {
                $start = $inq->created_at;
                $end = $inq->progressUpdates
                    ->whereIn('ProgressStatus', ['Verified as True', 'Identified as Fake'])
                    ->sortByDesc('created_at')
                    ->first();
                return $end && $start->diffInDays($end->created_at) > 3;
            });

            return [
                'Agency' => $agency->user->name ?? 'Unknown',
                'Assigned' => $inquiries->count(),
                'Resolved' => $resolved->count(),
                'Pending' => $pending->count(),
                'Delayed' => $delayed->count(),
                'Avg Hours to Resolve' => round($avgResolveTime ?? 0, 2)
            ];
        })->filter();

        return new Collection($data);
    }

    public function headings(): array
    {
        return [
            'Agency',
            'Assigned',
            'Resolved',
            'Pending',
            'Delayed',
            'Avg Hours to Resolve'
        ];
    }
}
