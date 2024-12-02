<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiProvider;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderReportController extends Controller
{
    public function index()
    {
        $pageTitle = 'SMM Order Statistics';
        $providers =  ApiProvider::active()->orderBy('name')->whereHas('order')->get();
        return view('admin.statistics.index', compact('pageTitle', 'providers'));
    }

    public function orderStatistics(Request $request)
    {
        $chartData = [];

        $statuses = ['pending', 'processing', 'completed', 'cancelled', 'refunded'];
        $statusData = [];
        if ($request->time == 'month') {

            foreach (getDaysOfMonth() as $day) {

                foreach ($statuses as $status) {
                    $statusData[$status] = Order::{$status}()->whereYear('created_at', now())->whereMonth('created_at', now())->whereDay('created_at', $day)->selectRaw('DATE(created_at) as date , count(*) as count')->groupBy('created_at')->first()->count ?? 0;
                }
                $chartData[$day] = $statusData;
            }
        }

        if ($request->time == 'week') {

            // Get the start of the week (default is Monday)
            $startOfWeek = now()->startOfWeek()->toDateTimeString();

            // Get the end of the week
            $endOfWeek = now()->endOfWeek()->toDateTimeString();

            foreach (["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"] as $day) {
                foreach ($statuses as $status) {
                    $statusData[$status] = Order::{$status}()->whereBetween('created_at', [$startOfWeek, $endOfWeek])->whereDay('created_at', dayNameToDate($day))->selectRaw('DATE(created_at) as date , count(*) as count')->groupBy('created_at')->first()->count ?? 0;
                }
                $chartData[$day] = $statusData;
            }
        }


        if ($request->time == 'year') {

            foreach (months() as $month) {
                $parsedMonth = Carbon::parse("1 $month");
                foreach ($statuses as $status) {
                    $statusData[$status] = Order::{$status}()->whereYear('created_at', now())->whereMonth('created_at', $parsedMonth)->selectRaw('MONTH(created_at) as month , count(*) as count')->groupBy('month')->first()->count ?? 0;
                }
                $chartData[$month] = $statusData;
            }
        }



        return response()->json([
            'chart_data' => $chartData,
            'statuses' => $statuses
        ]);
    }




    public function orderStatisticsByAPI(Request $request)
    {
        if ($request->time == 'year') {
            $time = now()->startOfYear();
        } elseif ($request->time == 'month') {
            $time = now()->startOfMonth();
        } elseif ($request->time == 'week') {
            $time = now()->startOfWeek();
        } else {
            $time = Carbon::parse('0000-00-00 00:00:00');
        }

        $orderChart = Order::with('provider')->where('created_at', '>=', $time)->groupBy('api_provider_id')->selectRaw("SUM(price) as orderPrice, api_provider_id")->orderBy('orderPrice', 'desc')->get();
        return [
            'order_data'  => $orderChart,
            'total_order' => $orderChart->sum('orderPrice'),
        ];
    }
}
