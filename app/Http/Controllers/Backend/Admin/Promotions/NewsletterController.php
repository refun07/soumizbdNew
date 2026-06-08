<?php

namespace App\Http\Controllers\Backend\Admin\Promotions;

use App\Http\Controllers\Controller;
use App\Mail\EmailManager;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Http\Request;
use Mail;

class NewsletterController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:send_newsletters'])->only(['index', 'store']);
    }

    # newsletter form
    public function index(Request $request)
    {
        $users  = User::customers()->where('email', '!=', null)->get();
        $subscribers = Subscriber::all();
        return view('backend.admin.newsletters.index', compact('subscribers', 'users'));
    }

    # send newsletter
    public function store(Request $request)
    {
        if (config('app.mail_username') != null) {
            //sends newsletter to selected users
            if ($request->has('user_emails')) {
                foreach ($request->user_emails as $key => $email) {
                    $array['view'] = 'emails.newsletter';
                    $array['subject'] = $request->subject;
                    $array['from'] = env('MAIL_FROM_ADDRESS');
                    $array['content'] = $request->content;

                    try {
                        Mail::to($email)->queue(new EmailManager($array));
                    } catch (\Exception $e) {
                        //dd($e);
                    }
                }
            }

            //sends newsletter to subscribers
            if ($request->has('subscriber_emails')) {
                foreach ($request->subscriber_emails as $key => $email) {
                    $array['view'] = 'emails.newsletter';
                    $array['subject'] = $request->subject;
                    $array['from'] = env('MAIL_FROM_ADDRESS');
                    $array['content'] = $request->content;
                    try {
                        Mail::to($email)->queue(new EmailManager($array));
                    } catch (\Exception $e) {
                        //dd($e);
                    }
                }
            }
        } else {
            flash(translate('Please configure SMTP first'))->error();
            return back();
        }

        flash(translate('Newsletter has been sent'))->success();
        return redirect()->route('admin.newsletters.index');
    }
}
