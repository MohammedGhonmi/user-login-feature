<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\UserServiceInterface;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class UserService implements UserServiceInterface
{
    /**
     * The model instance.
     *
     * @var App\User
     */
    protected $model;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Constructor to bind model to a repository.
     *
     * @param \App\User                $model
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(User $model, Request $request)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Define the validation rules for the model.
     *
     * @param  int $id
     * @return array
     */
    public function rules($id = null)
    {
        return [
            'prefixname' => ['string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable','string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'suffixname' => ['nullable','string', 'max:255'],
            'type' => ['nullable','string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 
                Rule::unique(User::class)->ignore($this->request->user())],
            'image' => 'image|mimes:png,jpg,jpeg|max:2048'
        ];
    }

    /**
     * Retrieve all resources and paginate.
     */
    public function list(): LengthAwarePaginator
    {
        return $this->model->paginate(10);
    }

    /**
     * Create model resource.
     */
    public function store(array $attributes): User
    {
        $this->model->fill($attributes);
        $this->model->password = $this->hash($this->model->password);
        $this->model->save();

        return $this->model;
    }

    /**
     * Retrieve model resource details.
     * Abort to 404 if not found.
     */
    public function find(int $id): User
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Update model resource.
     */
    public function update(int $id, array $attributes): bool
    {
        $this->model = $this->model->find($id);
 
        $this->model->fill($attributes);

        if($this->request->hasFile('image'))
            $this->model->photo = $this->upload($this->request->image);

        return $this->model->save();
    }

    /**
     * Soft delete model resource.
     */
    public function destroy(int|array $id): void
    {
        $user = $this->model->find($id);
        $user->delete();
    }

    /**
     * Include only soft deleted records in the results.
     */
    public function listTrashed(): LengthAwarePaginator
    {
        return $this->model->onlyTrashed()->paginate(10);
    }

    /**
     * Restore model resource.
     */
    public function restore(int|array $id): void
    {
        $this->model->withTrashed()->where('id', $id)
            ->restore();
    }

    /**
     * Permanently delete model resource.
     */
    public function delete(int|array $id): void
    {
        $this->model->withTrashed()->where('id', $id)
            ->forceDelete();
    }

    /**
     * Generate random hash key.
     */
    public function hash(string $key): string
    {
        return Hash::make($key);
    }

    /**
     * Upload the given file.
     */
    public function upload(UploadedFile $file): string|null
    {
        return $file->storeAs('avatars');
    }
}
