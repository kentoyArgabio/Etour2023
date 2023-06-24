<?php

namespace App\Http\Controllers;

require_once('../vendor/autoload.php');

use Exception;
use Stripe\Plan;
use \Stripe\Stripe;
use App\Models\User;
use App\Models\SubsPerk;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;
use App\Models\TravelPackage;
use Laravel\Cashier\Subscription;
use App\Models\Plan as ModelsPlan;
use Illuminate\Support\Facades\Auth;

class SubscriptionsController extends Controller
{

    public function index()
    {

        $subscriptions = Subscription::count();
        $packages = TravelPackage::count();
        $users = User::where('stripe_id', '!=', null)->count();

        return view('subscriptions.index', [
            'intent' => auth()->user()->createSetupIntent(),
            'plans' => ModelsPlan::all(),
            'subscriptions_count' => $subscriptions,
            'packages_count' => $packages,
            'users_count' => $users
        ]);
    }

    public function singleCharge(Request $request)
    {
        $amount = $request->amount;
        $paymentMethodId = $request->payment_method;
        $user = auth()->user();
        $user->createOrGetStripeCustomer();
        // $paymentMethod = $user->addPaymentMethod($paymentMethod);
        $user->charge($amount*100, $paymentMethodId);

        return back();
    }

    public function create()
    {
        if(auth()->user()->type != 'admin'){
            abort(403);
        }

        return view('subscriptions.create');
    }

    public function store(Request $request)
    {
         $request->validate([
            'name' => ['required', 'min:3'],
            'amount' => ['required', 'numeric', 'min:1'],
            'currency' => ['nullable'],
            'billing_period' => ['required'],
            'interval_count' => ['required', 'integer', 'min:1']
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $amount = ($request->amount * 100);
        try {
            $plan = Plan::create([  
                'product' => [
                    'name' => $request->name
                ], 
                'amount' => $amount,
                'currency' => $request->currency ?? 'usd',
                'interval' => $request->billing_period,
                'interval_count' => $request->interval_count
            ]);

            ModelsPlan::create([
                'plan_id' => $plan->id,
                'name' => $request->name,
                'price' => $plan->amount,
                'billing_period' => $plan->interval,
                'currency' => $plan->currency,
                'interval_count' => $plan->interval_count,
            ]);


        } catch (Exception $ex) {
            dd($ex->getMessage());
        }

        toast('Subscription Plan created successfully','success');
        return redirect('subscription-plans');
    }

    public function display()
    {
        if(auth()->user()->type != 'agency' || auth()->user()->stripe_id != null){
            abort(403);
        }

        $basic = ModelsPlan::where('name', 'Basic')->first();
        $plus = ModelsPlan::where('name', 'Plus')->first();
        $premium = ModelsPlan::where('name', 'Premium')->first();

        return view('subscriptions.display', [
            'basic' => $basic,
            'plus' => $plus,
            'premium' => $premium
        ]);
    }

    public function subscribe($name)
    {
        
        return view('subscriptions.subscribe', [
            'intent' => auth()->user()->createSetupIntent(),
            'plan' => ModelsPlan::where('name', 'like', '%'. $name .'%')->first()
        ]);
    }

    public function process(Request $request)
    {

        $request->validate([
            'plan_id' => ['required'],
            'payment_method' => ['required']
        ]);


        $user = auth()->user();
        $user->createOrGetStripeCustomer();
        if($request->payment_method != null){
            $user->addPaymentMethod($request->payment_method);
        }

        $subscription = $user->newSubscription(strtolower($request->name), $request->plan_id)->create($request->payment_method);

        $package_counter = 0;
        if($request->name == 'Basic'){
            $package_counter = 5;
        } else if($request->name == 'Plus'){
            $package_counter = 10;
        } else {
            $package_counter = 30;
        }

        SubsPerk::create([
            'subscription_id' => $subscription->id,
            'user_id' => auth()->user()->id,
            'package_counter' => $package_counter 
        ]);

        toast('Thank you for subscribing to our '. $request->name . ' plan','success');
        return to_route('package.index');
    }

    public function details()
    {
        return view('subscriptions.details', [
            'subscriptions' => Subscription::where('user_id', auth()->user()->id)->get()
        ]);
    }

    public function cancel($name)
    {
        $user = auth()->user();
        $user->subscription->cancel();

        toast('Subscription cancelled','warning');
        return back();
    }

    public function resume($name)   
    {
        $user = auth()->user();
        $user->subscription->resume();

        toast('Subscription resume','success');
        return back();
    }
}
