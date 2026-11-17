<?php

namespace Tests\Unit;

use App\Models\SalesOrder;
use App\Models\User;
use PHPUnit\Framework\TestCase;

class SalesOrderOwnershipTest extends TestCase
{
    public function test_order_owner_matches_even_when_database_ids_have_different_types(): void
    {
        $user = new User;
        $user->user_id = 42;

        $order = new SalesOrder;
        $order->created_by = '42';

        $this->assertTrue($order->isOwnedBy($user));
    }

    public function test_order_owner_does_not_match_another_user(): void
    {
        $user = new User;
        $user->user_id = 42;

        $order = new SalesOrder;
        $order->created_by = 43;

        $this->assertFalse($order->isOwnedBy($user));
    }
}
