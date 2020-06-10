<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Events\MonthlyCostWasChanged;
use App\Setting;
use App\Slideshow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Настройки',
            'slideshows' => Slideshow::all()
        ];

        return view('admin.settings.index', $data);
    }

    public function update(Request $request)
    {
        $v = $this->validateForm($request);

        if($v->fails()) {
            return redirect()->back()->withErrors($v->errors())
                ->withInput()->with(['status' => 'danger', 'message' => 'Проверьте поля на ошибки']);
        }

        if($request->input('prices.salon.verification_cost') != setting('salon.verification_cost', 'prices')) {
            event(new MonthlyCostWasChanged($request->input('prices.salon.verification_cost'), 'salon'));
        }

        if($request->input('prices.individual.verification_cost') != setting('individual.verification_cost', 'prices')) {
            event(new MonthlyCostWasChanged($request->input('prices.individual.verification_cost'), 'individual'));
        }

        Setting::truncate();

        foreach ($request->except(['_token', '_method']) as $type => $items) {
            if(!empty($items)) {
                foreach ($items as $key => $value) {
                    Setting::create([
                        'set_key' => $key,
                        'set_value' => is_array($value) ? serialize($value) : $value,
                        'set_type' => $type
                    ]);
                }
            }
        }

        return redirect()->route('admin.settings.index')
            ->with(['status' => 'success', 'message' => 'Настройки успешно обновлены']);
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(),[
            'general.vk_link' => 'nullable|url',
            'general.instagram_link' => 'nullable|url',
            'general.youtube_link' => 'nullable|url',
            'tickets.subjects.*.name' => 'required|max:150',
            'tickets.subjects.*.complaint' => 'count_in_request:complaint,1,1',
            'prices.*.profiles.*' => 'required|numeric',
            'prices.salon.activate' => 'required|numeric',
            'prices.salon.rise' => 'required|numeric',
            'prices.*.verification_cost' => 'required|numeric',
            'prices.salon.index_banner' => 'required|numeric',
            'prices.salon.index_banner_country' => 'required|numeric',
            'counts.*.max_profiles' => 'required|numeric'
        ]);

        return $v;
    }
}
