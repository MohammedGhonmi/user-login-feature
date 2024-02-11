<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_return_a_paginated_list_of_users(): void
    {
        $users = User::factory()->count(17)->create();

        $response1 = $this->get('/users');
        $response1->assertViewHas('users', function ($users) {
            return count($users) == 10;
        });
            
        $response2 = $this->get("/users?page=2");
        $response2->assertViewHas('users', function ($users) {
            return count($users) == 7;
        });

        $response3 = $this->get("/users?page=3");
        $response3->assertViewHas('users', function ($users) {
            return count($users) == 0;
        });
    }

    public function test_can_store_a_user_to_database(): void
    {
        $response = $this->post('/users', [
            'firstname' => 'first',
            'lastname' => 'last',
            'username' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseCount('users', 1);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_user_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/user');

        $response->assertOk();
    }

    public function test_user_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/user', [
                'username' => 'Test User',
                'firstname' => 'first',
                'lastname' => 'last',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/user');

        $user->refresh();

        $this->assertSame('Test User', $user->username);
        $this->assertSame('test@example.com', $user->email);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/user', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        //$this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/user')
            ->delete('/user', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/user');

        $this->assertNotNull($user->fresh());
    }
}
