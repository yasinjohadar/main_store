<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\User;

test('the admin product create page renders with the wholesale and bundle fields', function () {
    $admin = User::factory()->create();
    Category::factory()->create();

    $response = $this->actingAs($admin)->get(route('admin.products.create'));

    $response->assertOk();
    $response->assertSee('سعر الجملة');
    $response->assertSee('هذا المنتج عبارة عن حزمة');
});

test('the admin customer show page renders with the wholesale toggle', function () {
    $admin = User::factory()->create();
    $customer = User::factory()->create();
    Order::factory()->create(['user_id' => $customer->id]);

    $response = $this->actingAs($admin)->get(route('admin.customers.show', $customer));

    $response->assertOk();
    $response->assertSee('تفعيل تسعير الجملة');
});
