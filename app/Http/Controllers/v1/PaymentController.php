<?php

namespace App\Http\Controllers\v1;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    //
    // generating unique reference
    public function generateReference()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeLength = 15;

        $reference = '';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $codeLength; $i++) {
            $reference .= $characters[rand(0, $charactersLength - 1)];
        }

        return $reference;
    }

    public function initiate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orderID' => 'required|integer',
            'amount' => 'required|numeric',
            'payment_method' => 'required|string'
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }

        $reference = $this->generateReference();

        $payment = new Payment([
            'userID' => $request->user()->id,
            'orderID' => $request->orderID,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'reference' => $reference,
            'status' => 'Unpaid',
        ]);

        $payment->save();
        return response()->json(['status'=>true, 'message' => 'payment has been initiated', 'data'=> $payment], 200);
        
    }

    private function getAuthorization()
    {
        $key = base64_encode(env('MONNIFY_KEY') . ":" . env('MONNIFY_SECRET_KEY'));
        // echo $key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.monnify.com/api/v1/auth/login");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Basic $key",
            'Content-type' => 'application/json',
        ));
        // $EPIN_response = curl_exec($ch);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        // echo $EPIN_response;
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $obj = json_decode($response);
            // echo $response;
            if ($obj->requestSuccessful) {
                if ($obj->responseMessage == 'success') {
                    // $amount = ($obj->data->amount - $obj->data->fees) / 100;
                    return $obj->responseBody->accessToken;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    public function verifyPayment($reference)
    {
        $token = $this->getAuthorization();
        // verifying payment
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.monnify.com/api/v2/merchant/transactions/query?paymentReference=$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer " . $token,
                "Cache-Control: no-cache",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        // echo $response;
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $obj = json_decode($response);
            // echo $response;
            if ($obj->requestSuccessful) {
                if ($obj->responseMessage == 'success') {
                    // $amount = ($obj->data->amount - $obj->data->fees) / 100;
                    
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        // return true;
    }

    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
        ]);


        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()], 400);
        }

        $verify = $this->verifyPayment($request->reference);
        if ($verify) 
        {
            // update payment
            $payment = Payment::where('reference', $request->reference)->first();
            $payment->update([
                'gateway' => 'Monnify',
                "payment_date"=> Carbon::now(),
                'status' => 'Paid',
            ]);
            // update order
            $order = Order::find($payment->orderID);
            $order->update([
                'payment_status' => 'Paid',
                'status' => 'Active',
            ]);

            return response()->json(['status'=>true, 'message' => 'payment has been verified', 'data'=> $payment], 200);
            
        }else
        {
            return response()->json(['status'=>false, 'message' => 'failed to verify your payment'], 403);
        }


    }
}
