<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;


class ReportController extends Controller
{
    /**
     * Get total leads assigned to each counselor.
     */
    public function totalLeadsPerCounselor()
    {
        $results = DB::table('assignments')
            ->select('counselor_id', DB::raw('COUNT(*) as total_leads'))
            ->groupBy('counselor_id')
            ->get();

        return response()->json($results);
    }

    /**
     * Get counselors with the highest lead conversion rates.
     */
    public function topConversionCounselors()
    {
        $results = DB::table('assignments as l')
            ->leftJoin('applications as a', 'l.id', '=', 'a.lead_id')
            ->select(
                'l.counselor_id',
                DB::raw('COUNT(a.id) as converted_leads'),
                DB::raw('(COUNT(a.id) / COUNT(l.id)) * 100 as conversion_rate')
            )
            ->groupBy('l.counselor_id')
            ->orderByDesc('conversion_rate')
            ->get();

        return response()->json($results);
    }

 public function mostActiveCounselor(): JsonResponse
    {
        $result = DB::table('applications')
            ->where('created_at', '>=', Carbon::now()->subDays(30)) // Last 30 days
            ->select('counselor_id', DB::raw('COUNT(*) as total_applications'))
            ->groupBy('counselor_id')
            ->orderByDesc('total_applications')
            ->first(); // Get the topmost counselor

        return response()->json([
            'counselor_id' => $result ? $result->counselor_id : null,
            'total_applications' => $result ? $result->total_applications : 0
        ]);
    
}

}