<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderNotificationMail;
use Twilio\Rest\Client;
use Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as Image;


class NotificationController extends Controller
{
    public function sendWhatsApp(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'phone'   => 'required',
            'message' => 'required',
                ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        
        if($request->hasFile('file')){
            $validatorF = Validator::make(request()->all(), [
                                'file' => 'required|image|mimes:jpeg,png,jpg,gif'
                            ]);
     
            if($validatorF->fails()){
                return response()->json($validatorF->errors()->toJson(), 400);
            }

            $image = $request->file('file');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            
            // Guardar imagen original
            $path = public_path('uploads/' . $filename);
            Image::make($image->getRealPath())->save($path);
            

            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
            $phone = substr($request->phone, 1);
            $twilio->messages->create(
                "whatsapp:+" .'593'.$phone,
                [
                    // necesita estar en HTTPS
                    "mediaUrl" => ["https://images.unsplash.com/photo-1431250620804-78b175d2fada?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1600&h=900&fit=crop&ixid=eyJhcHBfaWQiOjF9"],
                    // "mediaUrl" => url('uploads/' . $filename),
                    "from" => env('TWILIO_FROM'),
                    "body" => $request->message
                ]
            );

            return response()->json(['success' => 200]);
        }else{
            $twilio = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
            $phone = substr($request->phone, 1);
            $twilio->messages->create(
                "whatsapp:+" .'593'.$phone,
                [
                    "from" => env('TWILIO_FROM'),
                    "body" => $request->message
                ]
            );

            return response()->json(['success' => $request->all()]);
        }

    }


    public function sendEmail(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'email' => 'required|email',
            'subject' => 'required',
            'message' => 'required',
                ]);
        
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        Mail::to($request->email)
            ->send(new OrderNotificationMail(
                $request->subject,
                $request->message
        ));

 

        return response()->json(['success' => true]);
    }

}
