<?php

namespace Tests\Feature;

use App\Http\Resources\ImageResource;
use App\Jobs\RetrieveImageJob;
use App\Models\Image;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class ImagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    /**
     * @test
     */
    public function test_a_logged_in_user_can_upload_a_uri_and_create_an_image_model()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->postJson(route('images.store'), ['image_uri' => $this->getTestImagePath('cat.jpg')])
            ->assertSuccessful()
            ->assertJson([
                'uri' => $this->getTestImagePath('cat.jpg'),
                'filename' => null,
                'path' => null,
            ]);

        $this->assertDatabaseHas('images', [
            'user_id' => $user->id,
            'uri' => $this->getTestImagePath('cat.jpg'),
            'filename' => null,
            'path' => null,
        ]);
    }

    /**
     * @test
     * @dataProvider invalid_uris
     */
    public function test_invalid_uris($image_uri, $error)
    {
        $this->actingAs(User::factory()->create());

        $this->postJson(route('images.store'), ['image_uri' => $image_uri])
            ->assertJsonValidationErrors(['image_uri' => $error]);
    }

    public function invalid_uris()
    {
        return [
            'required' => ['', 'required'],
            'max:255' => ['https://text.example.com./' . Str::random(255) . 'jpg', 'greater than 255 characters'],
            'min:5' => ['.jpg', 'at least 5 characters'],
            'ends_with' => ['test.pdf', 'end with'],
        ];
    }

    /**
     * @test
     */
    public function test_posting_uri_dispatches_retrieve_image_job()
    {
        Queue::assertNothingPushed();

        $this->actingAs(User::factory()->create());

        $response = $this->postJson(route('images.store'), ['image_uri' => $this->getTestImagePath('cat.jpg')])
            ->assertSuccessful();
        $imageAttributes = $response->getOriginalContent();

        Queue::assertPushed(fn (RetrieveImageJob $job) => $job->image->id === $imageAttributes['id']);
    }

    /**
     * @test
     */
    public function test_user_can_get_list_of_images()
    {
        $user = User::factory()->create();
        $images = Image::factory(10)->for($user)->create();
        $request = Request::create(route('images.index'), 'GET');
        $expectedResults = ImageResource::collection($images)->toArray($request);

        $this->actingAs($user)
            ->getJson(route('images.index'))
            ->assertOk()
            ->assertExactJson($expectedResults);

    }

    /**
     * @test
     */
    public function test_user_cannot_access_another_users_images()
    {
        $otherUser = User::factory()->create();
        Image::factory()->for($otherUser)->create();

        $this->actingAs(User::factory()->create())
            ->getJson(route('images.index'))
            ->assertSuccessful()
            ->assertExactJson([]);
    }
}
