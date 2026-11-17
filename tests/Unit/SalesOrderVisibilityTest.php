<?php

namespace Tests\Unit;

use App\Models\SalesOrder;
use App\Models\User;
use Tests\TestCase;

class SalesOrderVisibilityTest extends TestCase
{
    public function test_regular_users_are_limited_to_orders_they_created(): void
    {
        $user = new User;
        $user->user_id = 42;
        $user->role = 'user';

        $query = SalesOrder::query()->visibleTo($user);

        $this->assertStringContainsString('created_by', $query->toSql());
        $this->assertSame([42], $query->getBindings());
    }

    public function test_admins_can_see_all_orders(): void
    {
        $admin = new User;
        $admin->user_id = 1;
        $admin->role = 'admin';

        $query = SalesOrder::query()->visibleTo($admin);

        $this->assertStringNotContainsString('created_by', $query->toSql());
        $this->assertSame([], $query->getBindings());
    }
}
