<?php

namespace App\Http\Controllers\Backend\Api\Goal;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    # get data
    public function index()
    {
        $shop = shop();
        $goalAmount = $shop->monthly_goal_amount;
        if ($goalAmount != 0) {
            // get data of goal
            $monthStart     = Carbon::now()->startOfMonth();
            $monthEnd       = Carbon::now()->endOfMonth();
            $soldAmount     = Order::shopOrders()->whereDate('created_at', '>=', $monthStart)->sum('total_amount');

            $amountPercentage   = 0;
            $title              = '';
            $text               = '';
            if ($soldAmount >= $goalAmount) {
                $amountPercentage = 100;
                $title  = translate('Congratulations');
                $text   = translate('You have reached your goal');
            } else {
                $amountPercentage =  ($soldAmount * 100) / $goalAmount;
                $title  = translate('Your goal is all set!');
                $text   = translate('Track your progress on daily basis.');
                if ($amountPercentage > 100) {
                    $amountPercentage =  100;
                }
            }
            $startDate      = date('d M, Y', strtotime($monthStart));
            $endDate        = date('d M, Y', strtotime($monthEnd));

            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'goalAmount'        => $goalAmount,
                    'soldAmount'        => $soldAmount,
                    'amountPercentage'  => number_format($amountPercentage, 2, '.', ','),
                    'title'             => $title,
                    'text'              => $text,
                    'startDate'         => $startDate,
                    'endDate'           => $endDate,
                ]
            ];
        } else {
            $data = [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'goalAmount'        => $goalAmount,
                ]
            ];
        }

        return $data;
    }

    # set data
    public function store(Request $request)
    {
        $shop = shop();
        $shop->monthly_goal_amount = $request->monthlyGoalAmount;
        $shop->save();
        return $this->index();
    }
}
