<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
   /**
    * @test
    */
   public function test_it_gives_a_broadcast_channel_for_given_user_id()
   {
       $expectedChannel = 'App.Models.User.1';
       $this->assertEquals($expectedChannel, User::getPrivateBroadcastChannelForUserId(1));
   }
}
