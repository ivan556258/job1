<?php

namespace App\Http\Controllers\Account;

use App\Facades\NotificationServiceFacade;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $data = [
            'meta_title' => "Настройка уведомлений",
            'notifications' => Auth::user()->notificationSettings->groupBy(['notification', 'channel']),
            'allNotifications' => NotificationServiceFacade::notifications()
        ];

        return view('front.account.notifications.index', $data);
    }

    public function update(Request $request)
    {
        NotificationServiceFacade::setData($request->input('notification'));

        return redirect()->route('account.notifications.index')
            ->with('message', 'Успешно сохранено!');
    }
}
