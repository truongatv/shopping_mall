<?php
namespace App\Http\Controllers;
use Auth;
use DB;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Address;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Product;
use Mail;
use App\Contracts\CheckoutRepositoryInterface;

class CheckOutController extends Controller
{

    protected $checkoutRepository;

    public function __construct(CheckoutRepositoryInterface $checkoutRepository)
    {
        $this->checkoutRepository = $checkoutRepository;
    }

    public function get_checkout_addresses($order_id)
    {
        $id = Auth::user()->id;
        $address = Address::where('user_id', $id)->first();

        return view('checkout/checkout_addresses', ['address' => $address]);
    }

    public function checkout_addresses(Request $request,$user_id,$order_id)
    {
    	$this->validate($request, [
    		'checkout-shipping-name' => 'required|max:50',
    		'checkout-shipping-address1' => 'required',
    		'checkout-shipping-phone' =>'required|numeric',
    		],[
    		'checkout-shipping-name.required' => trans('errors.name'),
    		'checkout-shipping-name.max' => trans('errors.name_lenght'),
    		'checkout-shipping-address1.required' => trans('errors.address1'),
    		'checkout-shipping-phone.required' => trans('errors.phone'),
    		'checkout-shipping-phone.numeric' => trans('errors.phone_error'),
    		]);
    	$order = Order::where('order_id',$order_id)->first();
    	try {
    		$order->address = $request->input('checkout-shipping-address1').' | '.$request->input('checkout-shipping-address2').' | '.$request->input('checkout-shipping-city');
    		$order->information = $request->input('checkout-shipping-name').' | '.$request->input('checkout-shipping-phone');
    		DB::transaction(function () use($order) {
    			$order->save();
    		});
    		return redirect()->route('checkout_payment', $order_id);
    	} catch (\Exception $e)
    	{
    		return redirect()->back()->withErrors(trans('errors.add_addresses'));
    	}
    }

    public function checkout_payment(Request $request,$user_id,$order_id)
    {
    	if($request->checkout_payments == 'prepaid') {
    		try {
                $payment_info = "1";
                $this->checkoutRepository->savePaymentType($request, $order_id, $payment_info);

    			return redirect()->route('checkout_confirm', $order_id);
    		} catch(\Exception $e) {
    			return redirect()->back()->withErrors(trans('errors.add_payment'));
    		}
    	}
    	if($request->checkout_payments != 'prepaid') {
    		$this->validate($request, [
    			'cardholder_name' => 'required|max:50',
    			],[
    			'cardholder_name.required' => trans('errors.name'),
    			]);
    		try {
                $payment_info = $request->cardholder_name.' | '.$request->checkout_payments;
                $this->checkoutRepository->savePaymentType($request, $order_id, $payment_info);

				return redirect()->route('checkout_confirm', $order_id);
    		} catch(\Exception $e) {
    			return redirect()->back()->withErrors('errors.add_payment');
    		}
    	}
    }

    public function checkout_confirm($order_id)
    {
        $order = Order::where('order_id', $order_id)->first();
        $order_details = $order->order_details()->get();

        return view('checkout/checkout_confirm',compact('order_details'));
    }

    public function checkout_comfirm_done(Request $request, $user_id, $order_id)
    {
        try {
            $order = Order::where('order_id', $order_id)->first();
            $price = $order->total_price;
            $order->status = 1;
            $order_details = $order->order_details;
            try {
                \Stripe\Stripe::setApiKey("sk_test_YTLjkolnAVb1Y8SR20tRnIjI");
                $charge = \Stripe\Charge::create(array(
                    "amount" => (int)$price,
                    "currency" => "usd",
                    "source" => "tok_amex", // obtained with Stripe.js
                    "description" => "Charge for nguyentientruong95@gmail.com and user id ".$user_id .""
                ));
            } catch(Stripe_CardError $e) {
                return redirect()->back();
            } catch (Stripe_InvalidRequestError $e) {
              // Invalid parameters were supplied to Stripe's API
                return redirect()->back();
            } catch (Stripe_AuthenticationError $e) {
              // Authentication with Stripe's API failed
                return redirect()->back();
            } catch (Stripe_ApiConnectionError $e) {
              // Network communication with Stripe failed
                return redirect()->back();
            } catch (Stripe_Error $e) {
              // Display a very generic error to the user, and maybe send
              // yourself an email
                return redirect()->back();
            } catch (Exception $e) {
              // Something else happened, completely unrelated to Stripe
                return redirect()->back();
            }
            //remove quality of products when order success
            foreach ($order_details as $order_detail) {
                DB::transaction(function() use($order_detail) {
                    $product = $order_detail->product;
                    $product->total_quanity -= $order_detail->quality;
                    $product->save();
                });
            }
            DB::transaction(function () use($order) {
                $order->save();
            });
            //create new session for use when order success
            $payment_type = new PaymentType;
            $order = new Order;
            $payment = new Payment;
            DB::transaction(function () use($order, $payment_type, $payment) {
            	$payment_type->information = "1";
                $payment_type->save();
                $payment_type = PaymentType::orderBy('payment_type_id','DESC')->first();
                $payment->payment_type_id = $payment_type->payment_type_id;
                $payment->save();
                $payment = Payment::orderBy('payment_id', 'desc')->first();
                $order->total_price = 0;
                $order->content = 'null';
                $order->user_id = Auth::user()->id;
                $order->payment_id = $payment->payment_id;
                $order->save();
                $order_details = $order->order_details;
                $name = Auth::user()->name;
                $mail = Auth::user()->email;
                $data = ['name' => $name, 'mail' => $mail, 'order_details' => $order_details, 'order' => $order
                ];
                //send mail notification for user
                Mail::send('mail_order', $data, function($message) use ($data) {
                    $message->to($data['mail'], $data['name'])->subject('Shopping Mall');
                });
            });
            $counts = Product::all() -> count();
            $newArrivals = Product::orderBy('products.created_at')
                        ->paginate(3);
            $topSells = Product::orderBy('products.top_product')
                        ->paginate(6);

            return redirect()->route('welcome')->with(compact('counts', 'newArrivals', 'topSells', 'order'))->with('done_payment', trans('success.done_payment'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors( trans('errors.confirm_order'));
        }

    }
}
