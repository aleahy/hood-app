<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     */
    public function test_user_can_its_own_user_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('users.self'))
            ->assertOk()
            ->assertExactJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'privateChannel' => $user->privateChannel,
            ]);
    }

    /**
     * @test
     */
    public function test_guest_receives_unauthorized_if_getting_own_data()
    {
        $this->assertGuest()
            ->getJson(route('users.self'))
            ->assertUnauthorized();
    }

    /**
     * @test
     */
    public function test_it_gives_itself_a_broadcast_channel()
    {
        $user = User::factory()->create();
        $expectedChannel = 'App.Models.User.' . $user->id;
        $this->assertEquals($expectedChannel, $user->privateChannel);
    }
}
