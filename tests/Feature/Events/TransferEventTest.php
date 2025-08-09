<?php

namespace Tests\Feature\Events;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Mockery;
use Tests\TestCase;

class TransferEventTest extends TestCase
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
     * Test transfer between accounts.
     */
    public function test_transfer_between_accounts(): void
    {
        $originAccount = new Account([
            'id' => '100',
            'balance' => 100,
        ]);
        $destinationAccount = new Account([
            'id' => '200',
            'balance' => 50,
        ]);

        $mockRepository = Mockery::mock(AccountRepository::class);

        $mockRepository->shouldReceive('findById')
            ->with('100')
            ->once()
            ->andReturn($originAccount);

        $mockRepository->shouldReceive('firstOrCreate')
            ->with(Mockery::any())
            ->once()
            ->andReturn($destinationAccount);

        $mockRepository->shouldReceive('update')
            ->with(Mockery::on(function ($account) use ($originAccount) {
                return $account->id === $originAccount->id;
            }), Mockery::any())
            ->once()
            ->andReturnUsing(function ($account, $data) {
                $account->balance = $data['balance'];
                return $account;
            });

        $mockRepository->shouldReceive('update')
            ->with(Mockery::on(function ($account) use ($destinationAccount) {
                return $account->id === $destinationAccount->id;
            }), Mockery::any())
            ->once()
            ->andReturnUsing(function ($account, $data) {
                $account->balance = $data['balance'];
                return $account;
            });

        $this->app->instance(AccountRepository::class, $mockRepository);

        $data = [
            'type' => 'transfer',
            'origin' => '100',
            'destination' => '200',
            'amount' => 30,
        ];

        $response = $this->postJson('/event', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'origin' => [
                'id' => '100',
                'balance' => 70,
            ],
            'destination' => [
                'id' => '200',
                'balance' => 80,
            ]
        ]);
    }

    /**
     * Test transfer from non-existing account.
     */
    public function test_transfer_from_non_existing_account(): void
    {
        $destinationAccount = new Account([
            'id' => '200',
            'balance' => 50,
        ]);

        $mockRepository = Mockery::mock(AccountRepository::class);

        $mockRepository->shouldReceive('findById')
            ->with('999')
            ->once()
            ->andReturn(null);

        $mockRepository->shouldReceive('firstOrCreate')
            ->with(Mockery::any())
            ->once()
            ->andReturn($destinationAccount);

        $this->app->instance(AccountRepository::class, $mockRepository);

        $data = [
            'type' => 'transfer',
            'origin' => '999',
            'destination' => '200',
            'amount' => 30,
        ];

        $response = $this->postJson('/event', $data);

        $response->assertStatus(404);
        $response->assertSeeText('0');
    }

    /**
     * Test transfer to a new account.
     */
    public function test_transfer_to_new_account(): void
    {
        $originAccount = new Account([
            'id' => '100',
            'balance' => 100,
        ]);
        $newAccount = new Account([
            'id' => '300',
            'balance' => 0,
        ]);

        $mockRepository = Mockery::mock(AccountRepository::class);

        $mockRepository->shouldReceive('findById')
            ->with('100')
            ->once()
            ->andReturn($originAccount);

        $mockRepository->shouldReceive('firstOrCreate')
            ->with(Mockery::any())
            ->once()
            ->andReturn($newAccount);

        $mockRepository->shouldReceive('update')
            ->with(Mockery::on(function ($account) use ($originAccount) {
                return $account->id === $originAccount->id;
            }), Mockery::any())
            ->once()
            ->andReturnUsing(function ($account, $data) {
                $account->balance = $data['balance'];
                return $account;
            });

        $mockRepository->shouldReceive('update')
            ->with(Mockery::on(function ($account) use ($newAccount) {
                return $account->id === $newAccount->id;
            }), Mockery::any())
            ->once()
            ->andReturnUsing(function ($account, $data) {
                $account->balance = $data['balance'];
                return $account;
            });

        $this->app->instance(AccountRepository::class, $mockRepository);

        $data = [
            'type' => 'transfer',
            'origin' => '100',
            'destination' => '300',
            'amount' => 30,
        ];

        $response = $this->postJson('/event', $data);

        $response->assertStatus(201);
        $response->assertJson([
            'origin' => [
                'id' => '100',
                'balance' => 70,
            ],
            'destination' => [
                'id' => '300',
                'balance' => 30,
            ]
        ]);
    }
}
