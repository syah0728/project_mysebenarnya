<?php
namespace App\Exports;

use App\Models\Inquiry;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class InquiryExport implements FromCollection, WithHeadings
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        $query = Inquiry::query();

        if ($this->month && $this->year) {
            $query->whereMonth('created_at', $this->month)
                  ->whereYear('created_at', $this->year);
        } elseif ($this->year) {
            $query->whereYear('created_at', $this->year);
        }

        return $query->select('id', 'NewsTitle', 'InquiryStatus', 'created_at')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Title', 'Status', 'Created At'];
    }
}
