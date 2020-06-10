<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Services\Admin\UserFilters;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use jeremykenedy\LaravelRoles\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Пользователи',
            'users' => UserFilters::createQuery($request, User::with([]))->paginate(25)
                ->appends(Input::except('page'))
        ];

        return view('admin.users.index', array_merge($data, UserFilters::getVariables($request)));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'title' => 'Создать пользователя',
            'roles' => Role::all(),
            'countries' => Country::all()
        ];

        return view('admin.users.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);
        $user = User::create($request->all());
        $user->attachRole($request->input('role_id'));
        $user->telephones()->create($request->merge([
            'main' => 1
        ])->all());

        return redirect()->route('admin.users.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $data = [
            'title' => 'Редактирование пользователя',
            'user' => $user,
            'roles' => Role::all(),
            'countries' => Country::all()
        ];

        return view('admin.users.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validateForm($request, $user->id);
        $user->update($request->all());
        $user->detachAllRoles();
        $user->attachRole($request->input('role_id'));
		if($request->telephone){
			$user->telephones()->updateOrCreate([
				'main' => 1
			], $request->all());
		}

        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        $json['success'] = false;
        try {
            $user->delete();
            $json['success'] = true;
        } catch (\Exception $e) {
            \Log::warning('Cant remove user', ['message' => $e->getMessage(), 'user' => $user]);
        }

        return Response::json($json);
    }

    public function search(Request $request)
    {
        $json['success'] = false;
        if ($request->has('user')) {
            $role = [];
            if($request->has('role')) {
                $role = [
                    ['roles', 'slug', $request->input('role')]
                ];
            }

            $json['users'] = User::search($request->input('user'), ['username', 'email'], $role)
                ->with(['city'])->limit(5)->get();
            $json['success'] = true;
        }

        return Response::json($json);
    }

    public function loginAs(User $user)
    {
        Auth::login($user);
        return redirect()->route('account.index');
    }

    private function validateForm(Request $request, int $user_id = null)
    {
        $v = \Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user_id,
            'username' => 'required|latin_num_dash|max:255|unique:users,username,' . $user_id,
            'telephone' => 'nullable|telephone',
            'role_id' => 'required'
        ]);

        $v->sometimes('password', ['required', 'confirmed', 'between:6,255'], function ($input) use($user_id) {
            return !empty($input->password) || !$user_id;
        });

        $v->validate();
    }
}
