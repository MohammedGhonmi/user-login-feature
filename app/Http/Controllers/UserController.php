<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserCreateRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class UserController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected UserService $userService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        return view('user.index',[
            'users' => $this->userService->list()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        return view('user.show',[
            'user' => $this->userService->find($id)
        ]);
    }

    /**
     * Display the user's user form.
     */
    public function edit(Request $request): View
    {
        return view('user.edit', [
            'user' => $request->user()
        ]);    
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(UserCreateRequest $request): RedirectResponse
    {
        $user = $this->userService->store($request->input());

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Update the user's user information.
     */
    public function update(UserUpdateRequest $request): RedirectResponse
    {
        $attributes = $request->input();
        $user = $this->userService->update(Auth::user()->id, $attributes);
        
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        return Redirect::route('user.edit')->with('status', 'user-updated');
    }

    /**
     * Soft delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $this->userService->destroy(Auth::user()->id);
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Get all soft deleted user's.
     */
    public function trashed(Request $request): View
    {
        return view('user.trashed',[
            'users' => $this->userService->listTrashed()
        ]);
    }

    /**
     * Get all soft deleted user's.
     */
    public function restore ($id): RedirectResponse
    {   
        $this->userService->restore($id);
        return Redirect::to('/users/trashed');
    }

    /**
     * Permanently delete the user.
     */
    public function delete ($id): RedirectResponse
    {   
        $this->userService->delete($id);
        return Redirect::to('/users/trashed');
    }
}
