<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Grechanyuk\MainSms\Facades\MainSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class VerificationController extends Controller
{
    public function sms(Request $request)
    {
        $json['success'] = false;
        $v = \Validator::make($request->all(), [
            'telephone' => 'required|telephone|size:10',
            'telephone_code' => 'required'
        ]);

        if ($v->fails()) {
            $json['errors'] = $v->errors();
            return Response::json($json);
        }

        $smsCode = rand(1000, 9999);
        $user = Auth::user();
        $sms = MainSms::sendSms($request->input('telephone_code') . $request->input('telephone'),
            'Ваш код подтверждения:' . $smsCode);

        if ($sms) {
            $json['sms_code'] = Hash::make($smsCode . $user->created_at . $request->input('telephone'));
            $json['success'] = true;
            if(config('mainsms.testMode')) {
                $json['testMode'] = $smsCode;
            }
        } else {
            $json['errors']['telephone'][] = 'Что-то пошло не так... Попробуйте позже';
        }

        return Response::json($json);
    }

    public function confirm(Request $request)
    {
        $json['success'] = false;
        $v = \Validator::make($request->all(), [
            'sms_code' => 'required',
            'telephone' => 'required|telephone',
            'code' => 'required|hash_check:' . $request->input('sms_code') . ',' . Auth::user()->created_at . $request->input('telephone')
        ]);

        if ($v->fails()) {
            $json['errors'] = $v->errors();
            return Response::json($json);
        }

        Auth::user()->telephones()
            ->where('telephone_code', $request->input('telephone_code'))
            ->where('telephone', $request->input('telephone'))
            ->update([
            'telephone_confirmed' => 1
        ]);

        $json['success'] = true;

        return Response::json($json);
    }
}
