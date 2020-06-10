<?php

namespace App\Http\Controllers\Admin;

use App\Convenience;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class ConvenienceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'title' => 'Удобства',
            'conveniences' => Convenience::paginate(25)
        ];

        return view('admin.conveniences.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Добавить что-то'
        ];

        return view('admin.conveniences.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);
        Convenience::create($request->all());

        return redirect()->route('admin.conveniences.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Convenience $convenience
     * @return \Illuminate\Http\Response
     */
    public function edit(Convenience $convenience)
    {
        $data = [
            'title' => 'Редактирование чего-то',
            'convenience' => $convenience
        ];

        return view('admin.conveniences.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Convenience $convenience
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Convenience $convenience)
    {
        $this->validateForm($request);
        $convenience->update($request->all());

        return redirect()->route('admin.conveniences.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Convenience $convenience
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Convenience $convenience)
    {
        $json['success'] = false;
        try {
            $convenience->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove convenience', ['message' => $e->getMessage(), 'convenience' => $convenience]);
        }

        return Response::json($json);
    }

    private function validateForm(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255'
        ]);
    }
}
