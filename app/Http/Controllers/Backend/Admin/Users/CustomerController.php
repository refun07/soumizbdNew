<?php

namespace App\Http\Controllers\Backend\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CustomerNoteLog;
use App\Models\CustomerSegment;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Hash;
use Illuminate\Http\Request;
use League\Csv\Writer;

class CustomerController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:create_customers'])->only(['store']);
        $this->middleware(['permission:view_customers'])->only(['index']);
        $this->middleware(['permission:edit_customers'])->only(['toggleBan']);
        $this->middleware(['permission:delete_customers'])->only(['destroy']);
    }

    # store customer
    public function store(Request $request)
    {
        // Manual validation with custom messages
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|unique:users,phone',
            'password' => 'nullable|string|min:6',
        ], [
            'name.required' => translate('Name is required'),
            'email.email' => translate('Please enter a valid email address'),
            'phone.unique' => translate('The phone number has already been taken'),
            'email.unique' => translate('The email has already been taken'),
            'password.min' => translate('Password must be at least 6 characters'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->getMessages()
            ], 422);
        }

        $customer = null;

        // Check if customer exists by email or phone
        if ($request->email) {
            $customer = User::where('email', $request->email)->first();
        }

        if (is_null($customer) && $request->phone) {
            $customer = User::where('phone', $request->phone)->first();
        }

        if (is_null($customer)) {
            $customer = new User;
            $customer->password = Hash::make($request->password ?? '123456');
            $customer->verification_code = rand(100000, 999999);
            $customer->email_or_otp_verified = 1;
            $customer->email_verified_at = date('Y-m-d H:i:s');
        }

        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->user_type = 'customer';
        $customer->save();

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Customer has been created successfully'),
        ], 200);
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        $searchKey = null;
        $limit     = $request->limit ?? 15;

        $authUser = auth()->user();

        if ($authUser->user_type == "staff") {
            $customers = User::where("assigned_to", $authUser->id)->customers()->with('orders');
        } else {
            $customers = User::customers()->with('orders');
        }
        if ($request->search != null) {
            try {
                Notification::where('link_info', $request->search)->where('type', 'customer-registration')->update([
                    'is_read' => 1
                ]);
            } catch (\Throwable $th) {
            }
            $customers = $customers->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
            $searchKey = $request->search;
        }

        // sort by
        if ($request->sortBy) {
            switch ($request->sortBy) {
                case 'amountHighToLow':
                    $customers = $customers->with(['orders' => function ($query) {
                        $query->select('user_id', \DB::raw('sum(total_amount) as total_amount'))
                            ->groupBy('user_id');
                    }])->orderByDesc(\DB::raw('(select sum(total_amount) from orders where orders.user_id = users.id)'));
                    break;
                case 'amountLowToHigh':
                    $customers = $customers->with(['orders' => function ($query) {
                        $query->select('user_id', \DB::raw('sum(total_amount) as total_amount'))
                            ->groupBy('user_id');
                    }])->orderBy(\DB::raw('(select sum(total_amount) from orders where orders.user_id = users.id)'));
                    break;
                case 'qtyHighToLow':
                    $customers = $customers->withCount('orders')
                        ->orderByDesc('orders_count');
                    break;
                case 'qtyLowToHigh':
                    $customers = $customers->withCount('orders')
                        ->orderBy('orders_count');
                    break;
                default:
                    $customers = $customers->latest();
                    break;
            }
        }

        // customer segments
        $segments = CustomerSegment::orderBy('value', 'DESC')->get();

        $customers = $customers->paginate($limit);
        return view('backend.admin.customers.index', compact('customers', 'searchKey', 'segments'));
    }

    # store customer segment
    public function storeCustomerSegment(Request $request)
    {
        $recentValue = 0;

        $recentSegment = CustomerSegment::latest()->first();
        if ($recentSegment) {
            $recentValue = $recentSegment->value;
        }

        $customerSegment        = new CustomerSegment;
        $customerSegment->name  = "New Segment";
        $customerSegment->value = $recentValue + 500;
        $customerSegment->save();


        $segments     = CustomerSegment::latest()->get();
        return [
            'success'      => true,
            'segmentViews' =>  view('backend.admin.customers.customer-segment', compact('segments'))->render()
        ];
    }
    # remove Customer Segment
    public function removeCustomerSegment(Request $request)
    {
        $recentSegment = CustomerSegment::whereId((int) $request->id)->first();
        if ($recentSegment) {
            $recentSegment->delete();
        }

        $segments     = CustomerSegment::latest()->get();
        return [
            'success'      => true,
            'segmentViews' =>  view('backend.admin.customers.customer-segment', compact('segments'))->render()
        ];
    }
    # update Customer Segment
    public function updateCustomerSegment(Request $request)
    {
        $recentSegment = CustomerSegment::whereId((int) $request->id)->first();
        if ($recentSegment) {
            $field = $request->name;
            $recentSegment->$field = $request->value;
            $recentSegment->save();
        }

        $segments     = CustomerSegment::latest()->get();
        return [
            'success'      => true,
            'segmentViews' =>  view('backend.admin.customers.customer-segment', compact('segments'))->render()
        ];
    }

    # show
    public function show($id)
    {
        $customer = User::findOrFail($id);
        return view('backend.admin.customers.show', compact('customer'));
    }

    # toggle Ban 
    public function toggleBan(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $customer = User::findOrFail($request->id);
        $customer->is_banned = !$customer->is_banned;
        $customer->save();

        return $data;
    }


    # storeCustomerLogs
    public function storeCustomerLog(Request $request)
    {
        // dd($request->all());
        $user = User::findOrFail((int) $request->customer_id);
        if ($request->reorder_reminder_date != null && $request->reorder_reminder_date != '') {
            $user->reorder_reminder_date = Carbon::parse($request->reorder_reminder_date);
        }
        if ($request->next_follow_up_date != null && $request->next_follow_up_date != '') {
            $user->next_follow_up_date = Carbon::parse($request->next_follow_up_date);
        }

        if ($request->assigned_to != null && $request->assigned_to != '') {
            $user->assigned_to = $request->assigned_to;
        }

        $user->status_note           = $request->status_note;
        $user->save();

        if ($request->details && count($request->details) > 0) {
            // Delete existing logs first
            $user->customerNoteLogs()->delete();

            // Create new logs
            foreach ($request->details as $key => $note) {
                $logNote              = new CustomerNoteLog;
                $logNote->customer_id = $user->id;
                $logNote->type        = $request->types[$key];
                $logNote->note        = $note;
                $logNote->save();
            }
        } else {
            // If details is empty, delete all existing logs
            $user->customerNoteLogs()->delete();
        }
        flash(translate('Log has been updated successfully'))->success();
        return redirect()->route('admin.customers.index');
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $customer = User::findOrFail($id);

        try {
            Cart::where('user_id', $id)->delete(); // some changes
        } catch (\Throwable $th) {
            //throw $th;
        }

        $customer->delete();
        flash(translate('Customer has been deleted successfully'))->success();
        return redirect()->route('admin.customers.index');
    }

    # export customers
    public function export(Request $request)
    {
        $authUser = auth()->user();

        if ($authUser->user_type == "staff") {
            $customers = User::where("assigned_to", $authUser->id)->customers()->with('orders');
        } else {
            $customers = User::customers()->with('orders');
        }

        if ($request->search != null) {
            $customers = $customers->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $customers->get();

        $segments = CustomerSegment::orderBy('value', 'DESC')->get();

        $csv = Writer::createFromString('');
        $csv->insertOne([
            'ID',
            'Name',
            'Email',
            'Phone',
            'Segment',
            'Purchase Value',
            'Last Purchase',
            'Assigned To',
            'Reorder Reminder',
            'Next Follow-up',
            'User Balance',
            'Banned',
            'Created At'
        ]);

        foreach ($customers as $customer) {
            $customerSegment = getCustomerSegment($customer->orders(), $segments);
            $firstOrder = $customer->orders()->latest()->first();

            $csv->insertOne([
                $customer->id,
                $customer->name,
                $customer->email ?? '',
                $customer->phone ?? '',
                $customerSegment[0] ? $customerSegment[0]->name : '',
                $customerSegment[2],
                $firstOrder ? $firstOrder->created_at->format('Y-m-d') : '',
                $customer->assignedTo ? $customer->assignedTo->name : '',
                $customer->reorder_reminder_date ? \Carbon\Carbon::parse($customer->reorder_reminder_date)->format('Y-m-d') : '',
                $customer->next_follow_up_date ? \Carbon\Carbon::parse($customer->next_follow_up_date)->format('Y-m-d') : '',
                $customer->user_balance ?? 0,
                $customer->is_banned ? 'Yes' : 'No',
                $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : ''
            ]);
        }

        $filename = 'customers_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    # import customers
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240'
        ], [
            'csv_file.required' => translate('Please upload a CSV file'),
            'csv_file.mimes' => translate('File must be a CSV file'),
            'csv_file.max' => translate('File size must be less than 10MB')
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $header = array_map('strtolower', $header);
        $header = array_map('trim', $header);

        $expectedColumns = ['name', 'email', 'phone', 'password'];
        foreach ($expectedColumns as $col) {
            if (!in_array($col, $header)) {
                return response()->json([
                    'success' => false,
                    'message' => translate('CSV must contain columns: name, email, phone, password'),
                    'errors' => []
                ], 422);
            }
        }

        $created = 0;
        $updated = 0;
        $errors = [];
        $errorCount = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;
            $data = array_combine($header, $row);

            if (empty($data['name'])) {
                if ($errorCount < 10) {
                    $errors[] = "Row {$rowNumber}: Name is required";
                }
                $errorCount++;
                continue;
            }

            if (empty($data['email']) && empty($data['phone'])) {
                if ($errorCount < 10) {
                    $errors[] = "Row {$rowNumber}: Either email or phone is required";
                }
                $errorCount++;
                continue;
            }

            $email = !empty($data['email']) ? trim($data['email']) : null;
            $phone = !empty($data['phone']) ? trim($data['phone']) : null;

            $customer = null;

            if ($email) {
                $customer = User::where('email', $email)->first();
            }

            if (is_null($customer) && $phone) {
                $customer = User::where('phone', $phone)->first();
            }

            if (is_null($customer)) {
                $customer = new User;
                $customer->password = Hash::make($data['password'] ?? '123456');
                $customer->verification_code = rand(100000, 999999);
                $customer->email_or_otp_verified = 1;
                $customer->email_verified_at = date('Y-m-d H:i:s');
                $created++;
            } else {
                $updated++;
            }

            $customer->name = trim($data['name']);
            $customer->email = $email;
            $customer->phone = $phone;

            if (!empty($data['password'])) {
                $customer->password = Hash::make($data['password']);
            }

            $customer->user_type = 'customer';
            $customer->save();
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => translate('Import completed successfully'),
            'result' => [
                'created' => $created,
                'updated' => $updated,
                'errors' => $errors,
                'total_errors' => $errorCount
            ]
        ], 200);
    }

    # download sample CSV
    public function sampleCsv()
    {
        $csv = Writer::createFromString('');
        $csv->insertOne([
            'name',
            'email',
            'phone',
            'password'
        ]);

        $csv->insertOne([
            'John Doe',
            'john@example.com',
            '+1234567890',
            'password123'
        ]);

        $csv->insertOne([
            'Jane Smith',
            'jane@example.com',
            '+0987654321',
            ''
        ]);

        return response((string) $csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="customer_import_sample.csv"');
    }
}
