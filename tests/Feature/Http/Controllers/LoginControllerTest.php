<?php

namespace Tests\Feature\dashboard\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginControllerTest extends TestCase
{
    // use RefreshDatabase;

    // public function setUp(): void
    // {
    //     parent::setUp();
    //     $this->user = User::factory()->create();
    // }

    /**
     * открытие страницы логина
     *
     * @return void
     */
    public function testLoginViewPage()
    {
        $response = $this->get(route('dashboard.login'));
        $response->assertSuccessful();
        $response->assertViewIs('dashboard::pages.login');
    }

    public function testUserCannotViewLoginWhenAuthenticated()
    {
        $user = User::factory()->make();
        $response = $this->actingAs($user, 'dashboard')->get(route('dashboard.login'));
        $response->assertRedirect(route('dashboard.home'));
    }
}
