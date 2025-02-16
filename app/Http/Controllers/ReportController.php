<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Get total leads assigned to each counselor.
     */
    public function totalLeadsPerCounselor()
    {
        return User::where('role', 'counselor')
            ->withCount(['assignments as total_leads' => function ($query) {
                $query->select(DB::raw('count(distinct lead_id)'));
            }])
            ->get();
    
    }

    /**
     * Get counselors with the highest lead conversion rates.
     */
    public function topConversionCounselors()
    {
        return User::where('role', 'counselor')
        ->withCount(['assignments as total_conversions' => function ($query) {
            $query->whereHas('applications');
        }])
        ->orderByDesc('total_conversions')
        ->get();    
    }

    public function mostActiveCounselor()
    {
        return $report = User::where('role', 'counselor')
        ->withCount(['assignments as total_conversions' => function ($query) {
            $query->whereHas('applications', function ($q) {
                $q->where('applications.created_at', '>=', now()->subDays(30));
            });
        }])
        ->orderByDesc('total_conversions')
        ->first();  
    }
}