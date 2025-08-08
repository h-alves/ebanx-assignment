<?php

namespace Tests\Feature\Events;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Mockery;
use Tests\TestCase;

class DepositEventTest extends TestCase
{
    /**
     * Setup method to run before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear down method to run after each test.
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test deposit event to new account.
     */
    public function test_deposit_event_to_new_account(): void
    {
        $newAccount = new Account([
            'id' => '100', 
            'balance' => 50,
        ]);

        $mockRepository = Mockery::mock(AccountRepository::class);
        $mockRepository->shouldReceive('findById')
            ->with('100')
            ->once()
            ->andReturn(null);

        $mockRepository->shouldReceive('create')
            ->with(Mockery::any())
            ->once()
            ->andReturn($newAccount);

        $this->app->instance(AccountRepository::class, $mockRepository);

        $data = [
            'type' => 'deposit',
            'destination' => '100',
            'amount' => 50,
        ];

        $response = $this->postJson('/api/event', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'destination' => [
                'id' => '100',
                'balance' => 50,
            ]
        ]);
    }

    /**
     * Test deposit to an existing account.
     */
    public function test_deposit_to_existing_account(): void
    {
        $existingAccount = new Account([
            'id' => '100',
            'balance' => 50,
        ]);

        $mockRepository = Mockery::mock(AccountRepository::class);
        $mockRepository->shouldReceive('findById')
            ->with('100')
            ->once()
            ->andReturn($existingAccount);

        $mockRepository->shouldReceive('update')
            ->with(Mockery::any(), Mockery::any())
            ->once()
            ->andReturnUsing(function ($account, $data) use ($existingAccount) {
                $existingAccount->balance = $data['balance'];
                return $existingAccount;
            });


        $this->app->instance(AccountRepository::class, $mockRepository);

        $data = [
            'type' => 'deposit',
            'destination' => '100',
            'amount' => 30
        ];

        $response = $this->postJson('/api/event', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'destination' => [
                'id' => '100',
                'balance' => 80
            ]
        ]);
    }
}
