<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


use Illuminate\Support\Facades\Queue;
use App\ElevatorRequest;
use App\Elevator;
use App\ElevatorService;
use App\Jobs\ProcessElevatorRequest;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
         $response = $this->actingAs($user)
                 ->withSession(['foo' => 'bar'])
                 ->get('/');

        $response->assertStatus(200);
    }







        use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();

        $this->json('DELETE', '/api/reset');
    }

    public function enableElevatorTestingMode()
    {
        Elevator::where('id', ElevatorService::ELEVATOR_ID)->update(['testing' => 1]);
    }

    public function testRequestJobIsPushed()
    {
        Queue::fake();

        $response = $this->json('post', '/api/request', ['from' => 1, 'to' => 3]);
        $response
            ->assertStatus(200)
            ->assertJson([
                'request_id' => true,
            ]);

        Queue::assertPushed(ProcessElevatorRequest::class);
    }

    public function testSingleRequest()
    {
        $this->enableElevatorTestingMode();

        $response = $this->json('post', '/api/request', ['from' => 1, 'to' => 6]);

        $response = $this->json('GET', '/api/status');
        $response
            ->assertStatus(200)
            ->assertJson([
                'signal' => 'closed',
                'direction' => 'stand',
                'current_floor' => 6,
                'request' => null,
            ]);
    }

    public function testDefaultStatus()
    {
        $response = $this->json('GET', '/api/status');
        $response
            ->assertStatus(200)
            ->assertJson([
                'signal' => 'closed',
                'direction' => 'stand',
                'current_floor' => 1,
                'request' => null,
            ]);
    }

    public function testReset()
    {
        Queue::fake();

        $response = $this->json('POST', '/api/request', ['from' => 1, 'to' => 3]);
        $response = $this->json('DELETE', '/api/reset');
        $response
            ->assertStatus(200)
            ->assertJson([
                'signal' => 'closed',
                'direction' => 'stand',
                'current_floor' => 1,
                'request' => null,
            ]);

        $this->assertEquals(0, ElevatorRequest::all()->count());
    }
}
