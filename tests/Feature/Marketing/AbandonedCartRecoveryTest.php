<?php

use App\Jobs\AbandonedCartRecoveryJob;
use App\Models\EmailTemplate;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use App\Models\User;
use App\Services\EmailSender;

beforeEach(function () {
    EmailTemplate::create([
        'key' => 'abandoned_cart_ar',
        'event' => 'abandoned_cart',
        'locale' => 'ar',
        'name' => 'تذكير بسلة مهجورة',
        'subject' => 'نسيت شيئاً في سلتك يا {{ customer_name }}؟',
        'body_html' => '<p>{{ customer_name }}</p><p>{{ cart_items_list }}</p><p>{{ cart_url }}</p>',
        'is_active' => true,
    ]);
});

test('sends a reminder for a cart abandoned past the configured threshold', function () {
    $user = User::factory()->create();
    $cart = ShoppingCart::factory()->forUser($user)->create();
    ShoppingCartItem::factory()->create(['shopping_cart_id' => $cart->id, 'quantity' => 2]);
    $cart->timestamps = false;
    $cart->updated_at = now()->subHours(5);
    $cart->save();

    $emailSender = Mockery::mock(EmailSender::class);
    $emailSender->shouldReceive('send')->once()->with('abandoned_cart', Mockery::on(fn ($recipient) => $recipient->is($user)), Mockery::any());

    (new AbandonedCartRecoveryJob())->handle($emailSender);

    expect($cart->fresh()->reminder_sent_at)->not->toBeNull();
});

test('does not remind a cart that is still within the threshold', function () {
    $user = User::factory()->create();
    $cart = ShoppingCart::factory()->forUser($user)->create();
    ShoppingCartItem::factory()->create(['shopping_cart_id' => $cart->id]);

    $emailSender = Mockery::mock(EmailSender::class);
    $emailSender->shouldNotReceive('send');

    (new AbandonedCartRecoveryJob())->handle($emailSender);

    expect($cart->fresh()->reminder_sent_at)->toBeNull();
});

test('does not remind an empty cart', function () {
    $user = User::factory()->create();
    $cart = ShoppingCart::factory()->forUser($user)->create();
    $cart->timestamps = false;
    $cart->updated_at = now()->subHours(5);
    $cart->save();

    $emailSender = Mockery::mock(EmailSender::class);
    $emailSender->shouldNotReceive('send');

    (new AbandonedCartRecoveryJob())->handle($emailSender);
});

test('skips a cart when the customer already ordered since abandoning it', function () {
    $user = User::factory()->create();
    $cart = ShoppingCart::factory()->forUser($user)->create();
    ShoppingCartItem::factory()->create(['shopping_cart_id' => $cart->id]);
    $cart->timestamps = false;
    $cart->updated_at = now()->subHours(5);
    $cart->save();

    Order::factory()->create([
        'user_id' => $user->id,
        'order_status_id' => OrderStatus::where('slug', 'pending')->value('id'),
        'created_at' => now()->subHours(1),
    ]);

    $emailSender = Mockery::mock(EmailSender::class);
    $emailSender->shouldNotReceive('send');

    (new AbandonedCartRecoveryJob())->handle($emailSender);

    expect($cart->fresh()->reminder_sent_at)->not->toBeNull();
});
