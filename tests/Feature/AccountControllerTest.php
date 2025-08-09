<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Mockery;
use Tests\TestCase;

class AccountControllerTest extends TestCase
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
     * Test reset endpoint.
     */
    public function test_reset_endpoint(): void
    {
        $mockRepository = Mockery::mock(AccountRepository::class);
        $mockRepository->shouldReceive('reset')->once()->andReturn(true);

        $this->app->instance(AccountRepository::class, $mockRepository);

        $response = $this->post('/reset');

        $response->assertStatus(200);
        $response->assertSeeText('OK');
    }

    /**
     * Test balance endpoint with existing account.
     */
    public function test_balance_endpoint_with_existing_account(): void
    {
        $account = new Account([
            'id' => '100', 
            'balance' => 50,
        ]);

        $mockRepository = Mockery::mock(AccountRepository::class);
        $mockRepository->shouldReceive('findById')
            ->with('100')
            ->once()
            ->andReturn($account);

        $this->app->instance(AccountRepository::class, $mockRepository);

        $response = $this->get('/balance?account_id=100');

        $response->assertStatus(200);
        $response->assertSeeText('50');
    }

    /**
     * Test balance endpoint with non-existing account.
     */
    public function test_balance_endpoint_with_non_existing_account(): void
    {
        $mockRepository = Mockery::mock(AccountRepository::class);
        $mockRepository->shouldReceive('findById')
            ->with('999')
            ->once()
            ->andReturn(null);

        $this->app->instance(AccountRepository::class, $mockRepository);

        $response = $this->get('/balance?account_id=999');

        $response->assertStatus(404);
        $response->assertSeeText('0');
    }
}
