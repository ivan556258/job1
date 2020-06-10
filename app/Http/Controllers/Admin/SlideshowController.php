<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Slideshow;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class SlideshowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Слайдшоу',
            'slideshows' => Slideshow::with(['country', 'city'])->get()
        ];

        return view('admin.slideshow.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Создать слайдшоу',
            'countries' => Country::with(['regions.cities'])->get()
        ];

        return view('admin.slideshow.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);

        $slideshow = Slideshow::create($request->all());
        foreach ($request->input('images') as $image) {
            $slideshow->images()->create($image);
        }

        return redirect()->route('admin.slideshow.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Slideshow  $slideshow
     * @return \Illuminate\Http\Response
     */
    public function edit(Slideshow $slideshow)
    {
        $data = [
            'title' => 'Редактирование слайдера',
            'slideshow' => $slideshow,
            'countries' => Country::with(['regions.cities'])->get()
        ];

        return view('admin.slideshow.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Slideshow  $slideshow
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slideshow $slideshow)
    {
        $this->validateForm($request);

        $slideshow->update($request->all());
        $slideshow->images()->delete();
        foreach ($request->input('images') as $image) {
            $slideshow->images()->create($image);
        }

        return redirect()->route('admin.slideshow.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Slideshow  $slideshow
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Slideshow $slideshow)
    {
        $json['success'] = false;
        try {
            $slideshow->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Can not remove slider', ['message' => $e->getMessage(), 'slideshow' => $slideshow]);
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $v = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'images' => 'required',
            'images.*.image' => 'required',
            'button_text' => 'max:255',
            'text' => 'max:255'
        ]);

        $v->sometimes('localisation', 'required', function ($input) {
            return empty($input->coutry_id) && empty($input->city_id);
        });

        $v->validate();
    }
}
