<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Services\UserServiceInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return array_merge($this->container->make(
            UserServiceInterface::class
        )->rules($this->user),[
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);            
    }
}
