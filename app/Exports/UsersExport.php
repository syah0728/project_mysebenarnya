<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Agency;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $startDate, $endDate, $role, $agencyId;

    public function __construct($startDate = null, $endDate = null, $role = null, $agencyId = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->role      = $role;
        $this->agencyId  = $agencyId;
    }

    public function collection()
    {
        $query = User::query();

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->role) {
            $query->where('role', $this->role);
        }

        if ($this->agencyId) {
            $agency = Agency::find($this->agencyId);
            if ($agency && $agency->user) {
                $query->where('id', $agency->user->id);
            }
        }

        // Select only relevant fields
        return $query->get([
            'id',
            'name',
            'email',
            'role',
            'created_at',
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Role',
            'Registered At',
        ];
    }
}
