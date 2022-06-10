<?php

namespace Tests\Feature;

use App\Events\ImageRetrievedEvent;
use App\Jobs\RetrieveImageJob;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class RetrieveImageJobTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('images');

        Event::fake();

        $uuid = Str::uuid();
        Str::createUuidsUsing(fn () => $uuid);
    }

    protected function tearDown(): void
    {
        Str::createUuidsNormally();
        parent::tearDown();
    }

    /**
     * @test
     */
    public function test_it_can_retrieve_the_image_from_a_url_and_store_it()
    {
        $image = Image::factory()->create([
            'uri' => $this->getTestImagePath('cat.jpg'),
            'filename' => null,
            'path' => null,
        ]);

        $expectedFilename = Str::uuid() . '.jpg';

        Storage::disk('images')->assertMissing($expectedFilename);

        (new RetrieveImageJob($image))->handle();

        Storage::disk('images')->assertExists($expectedFilename);

        $this->assertEquals('cat.jpg', $image->refresh()->filename);
        $this->assertEquals(Storage::disk('images')->path($expectedFilename), $image->path);
    }

    /**
     * @test
     */
    public function test_it_dispatches_an_event_when_complete()
    {
        Event::assertNothingDispatched();

        $image = Image::factory()->create([
            'uri' => $this->getTestImagePath('cat.jpg'),
            'filename' => null,
            'path' => null,
        ]);

        (new RetrieveImageJob($image))->handle();

        Event::assertDispatched(function (ImageRetrievedEvent $event) use ($image) {
            return $image->is($event->image);
        });

    }
}
