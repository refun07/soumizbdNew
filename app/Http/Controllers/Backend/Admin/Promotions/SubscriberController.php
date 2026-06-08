<?php

namespace App\Http\Controllers\Backend\Admin\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_subscribers'])->only(['index']);
    }

    # resource list
    public function index(Request $request)
    {
        $limit = $request->limit ??  perPage();
        $subscribers = Subscriber::orderBy('created_at', 'desc');

        $searchKey = null;
        if ($request->search != null) {
            $subscribers = $subscribers->where('email', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $subscribers = $subscribers->paginate($limit);
        return view('backend.admin.newsletters.subscribers', compact('subscribers', 'searchKey'));
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();
        flash(translate('Subscriber has been deleted successfully'))->success();
        return redirect()->route('admin.subscribers.index');
    }
}
