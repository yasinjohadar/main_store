<?php
use App\Models\User;
test('account details page renders with the new privacy section', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('frontend.account.details'));
    $response->assertOk();
    $response->assertSee('تنزيل بياناتي');
});
