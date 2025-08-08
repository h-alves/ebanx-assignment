<?php

namespace Tests\Feature\Events;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Mockery;
use Tests\TestCase;

class WithdrawEventTest extends TestCase
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
     * Test withdraw event from an existing account.
     */
    public function test_withdraw_from_existing_account(): void
    {
        $existingAccount = new Account([
            'id' => '100', 
            'balance' => 100,
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
            'type' => 'withdraw',
            'origin' => '100',
            'amount' => 30,
        ];

        $response = $this->postJson('/api/event', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'origin' => [
                'id' => '100',
                'balance' => 70,
            ]
        ]);
    }

    /**
     * Test withdraw from non-existing account.
     */
    public function test_withdraw_from_non_existing_account(): void
    {
        $mockRepository = Mockery::mock(AccountRepository::class);
        $mockRepository->shouldReceive('findById')
            ->with('999')
            ->once()
            ->andReturn(null);

        $this->app->instance(AccountRepository::class, $mockRepository);

        $data = [
            'type' => 'withdraw',
            'origin' => '999',
            'amount' => 30,
        ];

        $response = $this->postJson('/api/event', $data);

        $response->assertStatus(404);
        $response->assertSeeText('0');
    }
}
