<?php

namespace Tests\Unit\Services;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use App\Models\User;
use App\Services\UserService;
use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class UserServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     * @return void
     */
    public function it_can_return_a_paginated_list_of_users()
    {
        $users = User::factory()->count(17)->create();
        $userService = $this->app->make(UserService::class);

        $paginator = $userService->list();

        $this->assertTrue(count($paginator) == 10);
        $this->assertTrue($paginator->hasPages());
        $this->assertTrue($paginator->lastPage() == 2);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_store_a_user_to_database()
    {
        $userService = $this->app->make(UserService::class);
        $response = $userService->store([
            'firstname' => 'first',
            'lastname' => 'last',
            'username' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseCount('users', 1);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_find_and_return_an_existing_user()
    {
        User::factory()->create();
        $userService = $this->app->make(UserService::class);
        
        $this->assertNotNull($userService->find(1));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_update_an_existing_user()
    {
        $user = User::factory()->create();
        $userService = $this->app->make(UserService::class);

        $response = $userService->update(1, [
            'firstname' => 'first',
            'lastname' => 'last',
            'username' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user = $userService->find(1);

        $this->assertDatabaseCount('users', 1);
        $this->assertSame('Test User', $user->username);
        $this->assertSame('test@example.com', $user->email);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_soft_delete_an_existing_user()
    {
        User::factory()->count(2)->create();
        $userService = $this->app->make(UserService::class);

        $user = $userService->destroy(1);
       
        $this->assertSoftDeleted($userService->listTrashed()[0]);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_return_a_paginated_list_of_trashed_users()
    {
        $users = User::factory()->count(27)->create([ 'deleted_at' => '2024-02-10' ]);
        $userService = $this->app->make(UserService::class);

        $paginator = $userService->listTrashed();

        $this->assertTrue(count($paginator) == 10);
        $this->assertTrue($paginator->hasPages());
        $this->assertTrue($paginator->lastPage() == 3);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_restore_a_soft_deleted_user()
    {
        $user = User::factory()->create([ 'deleted_at' => '2024-02-10' ]);
        $userService = $this->app->make(UserService::class);

        $userService->restore(1);

        $this->assertNotSoftDeleted($userService->find(1));
    }

    /**
     * @test
     * @return void
     */
    public function it_can_permanently_delete_a_soft_deleted_user()
    {
        $user = User::factory()->create([ 'deleted_at' => '2024-02-10' ]);
        $userService = $this->app->make(UserService::class);

        $userService->delete(1);

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * @test
     * @return void
     */
    public function it_can_upload_photo()
    {
        Storage::fake('avatars');
        $userService = $this->app->make(UserService::class);

        $file = UploadedFile::fake()->image('avatar.jpg');
        
        $path = $userService->upload($file);
        
        Storage::disk('avatars')->assertExists($path);
    }
}
