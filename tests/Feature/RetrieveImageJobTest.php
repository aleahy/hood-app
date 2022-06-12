<?php

namespace Tests\Feature;

use App\Events\ImageRetrievalFailedEvent;
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
        ]);

        $expectedFilename = Str::uuid() . '.jpg';

        Storage::disk('images')->assertMissing($expectedFilename);

        $job = new RetrieveImageJob($image);
        app()->call([$job, 'handle']);

        Storage::disk('images')->assertExists($expectedFilename);

        $this->assertEquals($expectedFilename, $image->refresh()->filename);
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
        ]);

        $job = new RetrieveImageJob($image);
        app()->call([$job, 'handle']);

        Event::assertDispatched(function (ImageRetrievedEvent $event) use ($image) {
            return $image->is($event->image);
        });
    }

    /**
     * @test
     */
    public function test_it_dispatches_an_event_on_failure()
    {
        Event::assertNothingDispatched();

        $image = Image::factory()->create([
            'uri' => 'http://www.example.com',
            'filename' => null,
        ]);

        $job = new RetrieveImageJob($image);
        app()->call([$job, 'handle']);

        Event::assertDispatched(function (ImageRetrievalFailedEvent $event) use ($image) {
            return $image->is($event->image);
        });
    }

    /**
     * @test
     */
    public function test_it_dispatches_failure_event_for_invalid_mime_type()
    {
        Event::assertNothingDispatched();

        $image = Image::factory()->create([
            'uri' => $this->getTestImagePath('placeholder.pdf'),
            'filename' => null,
        ]);

        $job = new RetrieveImageJob($image);
        app()->call([$job, 'handle']);

        Event::assertDispatched(function (ImageRetrievalFailedEvent $event) use ($image) {
            return $image->is($event->image);
        });
    }
}
