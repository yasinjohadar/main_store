<?php

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Support\Carbon;

test('the reports dashboard renders and shows period-over-period comparison', function () {
    $admin = User::factory()->create();
    $completed = OrderStatus::where('slug', 'completed')->firstOrFail();

    // This period: 2 orders totalling 300.
    Order::factory()->create([
        'order_status_id' => $completed->id,
        'total' => 100,
        'created_at' => Carbon::today(),
    ]);
    Order::factory()->create([
        'order_status_id' => $completed->id,
        'total' => 200,
        'created_at' => Carbon::today(),
    ]);

    // Previous equivalent period: 1 order totalling 100 -> sales should show +200%.
    Order::factory()->create([
        'order_status_id' => $completed->id,
        'total' => 100,
        'created_at' => Carbon::today()->subDay(),
    ]);

    $response = $this->actingAs($admin)->get(route('admin.reports.dashboard', ['preset' => 'today']));

    $response->assertOk();
    $response->assertSee('مقارنة بالفترة السابقة');
});
