<?php

namespace Tests\Feature;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    private function createMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'EMAIL' => 'member@example.com',
            'PASSWORD' => Hash::make('password123'),
            'TEL' => '090-1234-5678',
            'BIRTH' => '1990-01-01',
            'SHOW_BIRTH' => false,
            'GENDER' => 0,
            'SHOW_GENDER' => false,
            'IDENTITY_IMAGE' => 'identifications/test.png',
            'USERNAME' => 'テストユーザー',
            'SELF_INTRODUCTION' => '自己紹介',
            'ICON_IMAGE' => 'default_icon.png',
            'ACCOUNT_STATUS' => 0,
        ], $overrides));
    }

    public function test_login_returns_user(): void
    {
        $member = $this->createMember([
            'EMAIL' => 'login@example.com',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $member->EMAIL,
            'password' => 'password123',
            'remember' => false,
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'ログインしました。']);
    }

    public function test_me_requires_authentication(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    public function test_me_returns_authenticated_user(): void
    {
        $member = $this->createMember([
            'EMAIL' => 'me@example.com',
        ]);

        Sanctum::actingAs($member, ['*']);

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonPath('user.email', $member->EMAIL);
    }

    public function test_logout_returns_success_message(): void
    {
        $member = $this->createMember([
            'EMAIL' => 'logout@example.com',
        ]);

        Sanctum::actingAs($member, ['*']);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'ログアウトしました。']);
    }
}
