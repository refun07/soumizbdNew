<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmailMessage;
use App\Jobs\SendSMSMessage;
use App\Jobs\SendWhatsappMessage;
use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Models\CustomerSegment;
use App\Models\User;
use Illuminate\Http\Request;
use Str;

class OfferManagementController extends Controller
{
    # construct
    public function __construct()
    {
        // $this->middleware(['permission:view_brands'])->only(['index']);
        // $this->middleware(['permission:create_brands'])->only(['store']);
    }


    # Display a listing of the resource.
    public function index(Request $request)
    {
        $customerSegmentId = $request->customer_segment_id ?? null;
        $customerSegments  = CustomerSegment::orderBy('value', 'DESC')->get();

        $customerQuery = User::customers();
        if ($customerSegmentId) {
            $customerSegment  = CustomerSegment::find($customerSegmentId);
            if (!$customerSegment) {
                flash(translate('Customer Segment not found'))->error();
                return redirect()->route('admin.offerManagement');
            }

            $conditionValue = $customerSegment->value;
            $closedHigherValue = $customerSegments->filter(function ($segment) use ($customerSegment) {
                return $segment->value > $customerSegment->value;
            })->min('value');

            $customerQuery = $customerQuery->whereIn('id', function ($sub) use ($conditionValue, $closedHigherValue) {
                $sub->select('user_id')
                    ->from('orders')
                    ->where('payment_status', 'paid')
                    ->groupBy('user_id')
                    ->havingRaw('SUM(total_amount) >= ?', [$conditionValue]);

                if (!is_null($closedHigherValue)) {
                    $sub->havingRaw('SUM(total_amount) < ?', [$closedHigherValue]);
                }
            });
        }

        $customers = $customerQuery->orderBy('name', 'ASC')->get();
        return view('backend.admin.offer-management.index', compact('customers', 'customerSegments', 'customerSegmentId'));
    }


    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        // dd($request->all());
        $customerSegmentId = $request->customer_segment_id ?? null;
        $customerSegments  = CustomerSegment::orderBy('value', 'DESC')->get();

        $customerQuery = User::customers();
        if ($customerSegmentId) {
            $customerSegment  = CustomerSegment::find($customerSegmentId);
            if (!$customerSegment) {
                flash(translate('Customer Segment not found'))->error();
                return redirect()->route('admin.offerManagement');
            }

            $conditionValue = $customerSegment->value;
            $closedHigherValue = $customerSegments
                ->filter(function ($segment) use ($customerSegment) {
                    return $segment->value > $customerSegment->value;
                })
                ->min('value');

            $customerQuery = $customerQuery->whereIn('id', function ($sub) use ($conditionValue, $closedHigherValue) {
                $sub->select('user_id')
                    ->from('orders')
                    ->where('payment_status', 'paid')
                    ->groupBy('user_id')
                    ->havingRaw('SUM(total_amount) >= ?', [$conditionValue]);

                if (!is_null($closedHigherValue)) {
                    $sub->havingRaw('SUM(total_amount) < ?', [$closedHigherValue]);
                }
            });
        }
        $customers = $customerQuery->orderBy('name', 'ASC')->get();
        // send whatsapp message to customers
        foreach ($customers as $customer) {
            $phone = normalizeBangladeshiNumber($customer->phone);
            $message = str_replace('{name}', $customer->name, $request->message);
            switch ($request->engine) {
                case 'whatsapp':
                    if (empty($customer->phone)) {
                        // do nothing if phone is not available
                    } else {
                        SendWhatsappMessage::dispatch($phone, $message);
                    }
                    // dispatch job to send whatsapp message
                    break;
                case 'email':
                    if (empty($customer->email)) {
                        // skip if email is not available
                    } else {
                        SendEmailMessage::dispatch($customer->email, $message);
                    }
                    break;
                case 'sms':
                    if (empty($customer->phone)) {
                        // do nothing if phone is not available
                    } else {
                        SendSMSMessage::dispatch($phone, $message);
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }

        flash(translate('Offer will be sent successfully'))->success();
        return redirect()->route('admin.offerManagement');
    }
}
