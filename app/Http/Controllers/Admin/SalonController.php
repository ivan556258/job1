<?php

namespace App\Http\Controllers\Admin;

use App\Facades\SalonService;
use App\Http\Controllers\Account\SalonController as FrontSalonController;
use App\Salon;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class SalonController extends FrontSalonController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Список салонов',
            'salons' => Salon::with(['metros', 'images', 'user'])->paginate(20)
        ];

        return view('admin.salons.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Добавить салон',
            'user' => Auth::user()
        ];

        return view('admin.salons.create', $data);
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
        $salon = $this->getUser($request)->salon()->create($request->all());
        SalonService::createExtraData($request, $salon);

        return redirect()->route('admin.salons.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Salon  $salon
     * @return \Illuminate\Http\Response
     */
    public function edit(Salon $salon)
    {
        $salon->load(['user', 'videos', 'images', 'metros']);

        $data = [
            'title' => 'Редактирование салона',
            'salon' => $salon,
            'user' => $salon->user
        ];

        return view('admin.salons.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Salon  $salon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Salon $salon)
    {
        $this->validateForm($request);
        $salon->update($request->all());
        SalonService::createExtraData($request, $salon);

        return redirect()->route('admin.salons.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Salon  $salon
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Salon $salon)
    {
        $json['success'] = false;

        try {
            $salon->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Can\'t remove salon', ['message' => $e->getMessage(), 'salon' => $salon]);
        }

        return Response::json($json);
    }

    private function getUser(Request $request)
    {
        if($request->input('user_id')) {
            return User::whereKey($request->input('user_id'))->firstOrFail();
        }

        return Auth::user();
    }
}
