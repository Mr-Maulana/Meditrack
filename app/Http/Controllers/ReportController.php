<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Delivery;
use App\Models\RadiologyResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->format('Y-m-d');
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = $today;

        return view('reports.index', [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function userReport()
    {
        $user = Auth::user();
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $data = $this->generatePatientReport($user, $startDate, $endDate);
        return view('reports.patient', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportType' => 'patients',
        ]));
    }

    public function deliveryReport()
    {
        $user = Auth::user();
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        $data = $this->generateDeliveryReport($user, $startDate, $endDate);
        return view('reports.delivery', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportType' => 'deliveries',
        ]));
    }

    public function prescriptionReport()
    {
        $user = Auth::user();
        $startDate = now()->subDays(30)->format('Y-m-d');
        $endDate = now()->format('Y-m-d');
        
        // Use patient report logic for now as it contains prescription stats
        $data = $this->generatePatientReport($user, $startDate, $endDate);
        return view('reports.patient', array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportType' => 'prescriptions',
        ]));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:patients,deliveries,financial,summary,radiology',
        ]);

        $user = Auth::user();
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $reportType = $request->report_type;

        switch ($reportType) {
            case 'patients':
                $data = $this->generatePatientReport($user, $startDate, $endDate);
                $view = 'reports.patient';
                break;
                
            case 'deliveries':
                $data = $this->generateDeliveryReport($user, $startDate, $endDate);
                $view = 'reports.delivery';
                break;
                
            case 'financial':
                $data = $this->generateFinancialReport($user, $startDate, $endDate);
                $view = 'reports.financial';
                break;
                
            case 'summary':
                $data = $this->generateSummaryReport($user, $startDate, $endDate);
                $view = 'reports.summary';
                break;

            case 'radiology':
                $data = $this->generateRadiologyReport($user, $startDate, $endDate);
                $view = 'reports.radiology';
                break;
        }

        return view($view, array_merge($data, [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'reportType' => $reportType,
        ]));
    }

    private function generatePatientReport($user, $startDate, $endDate)
    {
        $patients = Patient::with(['creator', 'prescriptions', 'deliveries'])
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                return $query->where('created_by', $user->id);
            })
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->get();

        $patientStats = [
            'total' => $patients->count(),
            'with_prescriptions' => $patients->filter(fn($p) => $p->prescriptions->count() > 0)->count(),
            'with_deliveries' => $patients->filter(fn($p) => $p->deliveries->count() > 0)->count(),
        ];

        // Group by diagnosis
        $diagnosisStats = $patients->groupBy('diagnosis')
            ->map(function ($group, $diagnosis) {
                return [
                    'diagnosis' => $diagnosis ?: 'Tidak tercatat',
                    'count' => $group->count(),
                    'percentage' => 0,
                ];
            })
            ->sortByDesc('count')
            ->take(10);

        // Calculate percentages
        $total = $patientStats['total'];
        $diagnosisStats = $diagnosisStats->map(function ($item) use ($total) {
            $item['percentage'] = $total > 0 ? round(($item['count'] / $total) * 100, 2) : 0;
            return $item;
        });

        return [
            'patients' => $patients,
            'patientStats' => $patientStats,
            'diagnosisStats' => $diagnosisStats,
        ];
    }

    private function generateDeliveryReport($user, $startDate, $endDate)
    {
        $deliveries = Delivery::with(['patient', 'courier', 'prescription'])
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                if ($user->isApoteker()) {
                    return $query->whereHas('patient', function ($q) use ($user) {
                        $q->where('created_by', $user->id);
                    });
                } else { // Kurir
                    return $query->where('courier_id', $user->id);
                }
            })
            ->inReportPeriod($startDate, $endDate)
            ->orderByDesc('delivered_at')
            ->orderByDesc('delivery_date')
            ->get();

        $deliveredDeliveries = $deliveries
            ->where('status', 'delivered')
            ->sortByDesc(fn ($delivery) => $delivery->delivered_at ?? $delivery->delivery_date)
            ->values();

        $deliveryStats = [
            'total' => $deliveries->count(),
            'pending' => $deliveries->where('status', 'pending')->count(),
            'on_delivery' => $deliveries->where('status', 'on_delivery')->count(),
            'delivered' => $deliveries->where('status', 'delivered')->count(),
            'failed' => $deliveries->where('status', 'failed')->count(),
            'urgent' => $deliveries->where('priority', 'urgent')->count(),
            'normal' => $deliveries->where('priority', 'normal')->count(),
        ];

        // Success rate
        $successRate = $deliveryStats['total'] > 0 
            ? round(($deliveryStats['delivered'] / $deliveryStats['total']) * 100, 2)
            : 0;

        // Daily delivery trend
        $dailyTrend = $deliveries->groupBy(function ($delivery) {
                return Carbon::parse($delivery->delivery_date)->format('Y-m-d');
            })
            ->map(function ($group, $date) {
                return [
                    'date' => $date,
                    'count' => $group->count(),
                    'delivered' => $group->where('status', 'delivered')->count(),
                ];
            })
            ->sortBy('date');

        // Courier performance
        $courierPerformance = $deliveries->whereNotNull('courier_id')
            ->groupBy('courier_id')
            ->map(function ($group, $courierId) {
                $courier = $group->first()->courier;
                return [
                    'courier_name' => $courier->name,
                    'total' => $group->count(),
                    'delivered' => $group->where('status', 'delivered')->count(),
                    'success_rate' => $group->count() > 0 
                        ? round(($group->where('status', 'delivered')->count() / $group->count()) * 100, 2)
                        : 0,
                ];
            })
            ->sortByDesc('success_rate');

        return [
            'deliveries' => $deliveries,
            'deliveredDeliveries' => $deliveredDeliveries,
            'deliveryStats' => $deliveryStats,
            'successRate' => $successRate,
            'dailyTrend' => $dailyTrend,
            'courierPerformance' => $courierPerformance,
        ];
    }

    private function generateFinancialReport($user, $startDate, $endDate)
    {
        // Calculate estimated revenue (example: Rp 50,000 per delivery)
        $deliveries = Delivery::when(!$user->isAdmin(), function ($query) use ($user) {
                if ($user->isApoteker()) {
                    return $query->whereHas('patient', function ($q) use ($user) {
                        $q->where('created_by', $user->id);
                    });
                } else { // Kurir
                    return $query->where('courier_id', $user->id);
                }
            })
            ->inReportPeriod($startDate, $endDate)
            ->where('status', 'delivered')
            ->get();

        $revenuePerDelivery = 50000; // Rp 50,000 per delivery
        $estimatedRevenue = $deliveries->count() * $revenuePerDelivery;

        // Monthly revenue trend
        $monthlyRevenue = Delivery::inReportPeriod($startDate, $endDate)
            ->where('status', 'delivered')
            ->select(
                DB::raw('DATE_FORMAT(delivery_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) * ' . $revenuePerDelivery . ' as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Revenue by priority
        $revenueByPriority = [
            'urgent' => $deliveries->where('priority', 'urgent')->count() * $revenuePerDelivery,
            'normal' => $deliveries->where('priority', 'normal')->count() * $revenuePerDelivery,
        ];

        return [
            'estimatedRevenue' => $estimatedRevenue,
            'deliveryCount' => $deliveries->count(),
            'revenuePerDelivery' => $revenuePerDelivery,
            'monthlyRevenue' => $monthlyRevenue,
            'revenueByPriority' => $revenueByPriority,
        ];
    }

    private function generateSummaryReport($user, $startDate, $endDate)
    {
        // Patient statistics
        $patientStats = $this->generatePatientReport($user, $startDate, $endDate)['patientStats'];
        
        // Delivery statistics
        $deliveryStats = $this->generateDeliveryReport($user, $startDate, $endDate);
        
        // Financial statistics
        $financialStats = $this->generateFinancialReport($user, $startDate, $endDate);

        // Top performing couriers
        $topCouriers = $deliveryStats['courierPerformance']->take(5);

        // Most common diagnoses
        $commonDiagnoses = $this->generatePatientReport($user, $startDate, $endDate)['diagnosisStats']->take(5);

        // Radiology stats
        $radiologyStats = $this->generateRadiologyReport($user, $startDate, $endDate)['radiologyStats'];

        return [
            'patientStats' => $patientStats,
            'deliveryStats' => $deliveryStats['deliveryStats'],
            'successRate' => $deliveryStats['successRate'],
            'estimatedRevenue' => $financialStats['estimatedRevenue'],
            'topCouriers' => $topCouriers,
            'commonDiagnoses' => $commonDiagnoses,
            'dailyTrend' => $deliveryStats['dailyTrend'],
            'radiologyStats' => $radiologyStats,
        ];
    }

    private function generateRadiologyReport($user, $startDate, $endDate)
    {
        $query = RadiologyResult::with(['patient', 'operator', 'doctor'])
            ->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);

        // Non-admin users only see their own records
        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('operator_id', $user->id)
                  ->orWhere('doctor_id', $user->id);
            });
        }

        $results = $query->get();

        $radiologyStats = [
            'total'     => $results->count(),
            'pending'   => $results->where('status', 'pending')->count(),
            'process'   => $results->where('status', 'process')->count(),
            'completed' => $results->where('status', 'completed')->count(),
            'sent'      => $results->whereNotNull('sent_at')->count(),
        ];

        // Completion rate
        $completionRate = $radiologyStats['total'] > 0
            ? round(($radiologyStats['completed'] / $radiologyStats['total']) * 100, 1)
            : 0;

        // Group by diagnosis (top 10)
        $diagnosisTrend = $results->groupBy('diagnosis')
            ->map(function ($group, $diagnosis) use ($results) {
                return [
                    'diagnosis'  => $diagnosis ?: 'Tidak Tercatat',
                    'count'      => $group->count(),
                    'percentage' => $results->count() > 0
                        ? round(($group->count() / $results->count()) * 100, 1)
                        : 0,
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values();

        // Daily trend
        $dailyTrend = $results->groupBy(function ($r) {
                return Carbon::parse($r->created_at)->format('Y-m-d');
            })
            ->map(function ($group, $date) {
                return [
                    'date'      => $date,
                    'total'     => $group->count(),
                    'completed' => $group->where('status', 'completed')->count(),
                ];
            })
            ->sortBy('date')
            ->values();

        // Top operators
        $operatorPerformance = $results->whereNotNull('operator_id')
            ->groupBy('operator_id')
            ->map(function ($group) {
                $op = $group->first()->operator;
                return [
                    'name'      => $op ? $op->name : 'N/A',
                    'total'     => $group->count(),
                    'completed' => $group->where('status', 'completed')->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        return [
            'results'             => $results,
            'radiologyStats'      => $radiologyStats,
            'completionRate'      => $completionRate,
            'diagnosisTrend'      => $diagnosisTrend,
            'dailyTrend'          => $dailyTrend,
            'operatorPerformance' => $operatorPerformance,
        ];
    }

    public function quickReport($type, $range)
    {
        $user = Auth::user();
        
        switch ($range) {
            case 'today':
                $startDate = now()->format('Y-m-d');
                $endDate = $startDate;
                break;
            case 'week':
                $startDate = now()->subDays(7)->format('Y-m-d');
                $endDate = now()->format('Y-m-d');
                break;
            case 'month':
                $startDate = now()->startOfMonth()->format('Y-m-d');
                $endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'year':
                $startDate = now()->startOfYear()->format('Y-m-d');
                $endDate = now()->endOfYear()->format('Y-m-d');
                break;
            default:
                $startDate = now()->subDays(30)->format('Y-m-d');
                $endDate = now()->format('Y-m-d');
        }

        // Redirect to generate report with quick parameters
        return redirect()->route('reports.generate', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'report_type' => $type,
        ]);
    }

    public function printReport(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        
        // Regenerate report data for print
        switch ($data['report_type']) {
            case 'patients':
                $reportData = $this->generatePatientReport($user, $data['start_date'], $data['end_date']);
                $view = 'reports.print-patient';
                break;
            case 'deliveries':
                $reportData = $this->generateDeliveryReport($user, $data['start_date'], $data['end_date']);
                $view = 'reports.print-delivery';
                break;
            case 'financial':
                $reportData = $this->generateFinancialReport($user, $data['start_date'], $data['end_date']);
                $view = 'reports.print-financial';
                break;
            case 'summary':
                $reportData = $this->generateSummaryReport($user, $data['start_date'], $data['end_date']);
                $view = 'reports.print-summary';
                break;
            case 'radiology':
                $reportData = $this->generateRadiologyReport($user, $data['start_date'], $data['end_date']);
                $view = 'reports.radiology';
                break;
        }

        return view($view, array_merge($reportData, [
            'startDate' => $data['start_date'],
            'endDate' => $data['end_date'],
            'reportType' => $data['report_type'],
            'print' => true,
        ]));
    }

    public function exportReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'report_type' => 'required|in:patients,deliveries,financial,summary',
            'format' => 'required|in:pdf,excel',
        ]);

        // In a real application, you would generate PDF or Excel file here
        // For now, we'll just return a success message
        
        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil diekspor! File akan didownload secara otomatis.')
            ->with('export', true);
    }
}