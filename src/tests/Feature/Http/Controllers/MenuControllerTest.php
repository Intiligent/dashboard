<?php

namespace Tests\Feature\dashboard\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class MenuControllerTest extends TestCase
{
    // use RefreshDatabase;

    // public function setUp(): void
    // {
    //     parent::setUp();
    //     $this->user = User::factory()->create();
    // }

    public function testRedirectDashboardMenuPageByGuest()
    {
        $response = $this->get(route('dashboard.menu'));
        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard.login'));
    }

    public function testViewDashboardMenuPageByAuth()
    {
        $user = User::factory()->make();
        $response = $this->actingAs($user, 'dashboard')->get(route('dashboard.menu'));
        $response->assertSuccessful();
        $this->assertAuthenticatedAs($user);
    }

    public function testValidationCreateMenuGroup()
    {
        $user = User::factory()->make();
        $response = $this
            ->actingAs($user, 'dashboard')
            ->postJson(route('api.dashboard.postMenuGroup'));

        $response->assertStatus(422);
        $response->assertJsonStructure([
            ERR,
            CODE,
            MSG,
            DATA => [
                '*' => ['field', MSG],
            ],
        ]);
        $response->assertJsonFragment([
            CODE => 140001,
            ERR => 422,
            DATA => [
                0 => [
                    'field' => 'name',
                    'message' => 'Поле Имя обязательно для заполнения, когда id не указано.',
                ],
            ],
        ]);
    }

    public function testSuccessCreateMenuGroup()
    {
        $user = User::factory()->make();
        $response = $this
            ->actingAs($user, 'dashboard')
            ->postJson(route('api.dashboard.postMenuGroup'), [
                'name' => 'test',
            ])
            ->assertCreated()
            ->assertJsonStructure([
                DATA => ['name', 'code', 'id'],
            ]);
        $this->assertDatabaseHas('menu_items', ['code' => 'test']);

        $response = $this
            ->actingAs($user, 'dashboard')
            ->postJson(route('api.dashboard.postMenuGroup'), [
                'name' => 'test',
                'code' => 'test',
            ])
            ->assertStatus(422);

        // запись дублирующего значения по code
        // dump($response->getContent());
    }

    // public function testSuccessUpdateMenuGroup()
    // {
    //     $user = User::factory()->make();
    //     $response = $this
    //         ->actingAs($user, 'dashboard')
    //         ->postJson(route('api.dashboard.postMenuGroup'), [
    //             'name' => 'test',
    //         ]);
    //
    //     dump($response->getContent());
    //     $response->assertCreated();
    //
    //     // запись дублирующего значения по code
    // }
}
