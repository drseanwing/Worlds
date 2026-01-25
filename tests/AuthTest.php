<?php

namespace Worlds\Tests;

use Worlds\Config\Auth;
use Worlds\Config\Database;

/**
 * Auth Test
 *
 * Tests authentication functionality including login, logout,
 * password hashing, user management, and session handling.
 */
class AuthTest extends TestCase
{
    /**
     * Set up before each test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Override Database singleton with test database
        $reflection = new \ReflectionClass(Database::class);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, self::$db);

        // Reset Auth state
        Auth::reset();
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset Auth and Database state
        Auth::reset();
        Database::reset();
    }

    /**
     * Test password hashing
     */
    public function test_password_hashing(): void
    {
        $password = 'test_password_123';
        $hash = Auth::hashPassword($password);

        $this->assertNotEquals($password, $hash);
        $this->assertStringStartsWith('$2y$', $hash); // BCRYPT identifier
    }

    /**
     * Test password verification with correct password
     */
    public function test_password_verification_with_correct_password(): void
    {
        $password = 'test_password_123';
        $hash = Auth::hashPassword($password);

        $result = Auth::verifyPassword($password, $hash);

        $this->assertTrue($result);
    }

    /**
     * Test password verification with incorrect password
     */
    public function test_password_verification_with_incorrect_password(): void
    {
        $password = 'test_password_123';
        $hash = Auth::hashPassword($password);

        $result = Auth::verifyPassword('wrong_password', $hash);

        $this->assertFalse($result);
    }

    /**
     * Test creating a new user
     */
    public function test_create_user(): void
    {
        $userId = Auth::createUser(
            'testuser',
            'password123',
            'test@example.com',
            'Test User'
        );

        $this->assertIsInt($userId);
        $this->assertGreaterThan(0, $userId);
        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'username' => 'testuser',
            'email' => 'test@example.com'
        ]);
    }

    /**
     * Test creating admin user
     */
    public function test_create_admin_user(): void
    {
        $userId = Auth::createUser(
            'admin',
            'admin123',
            'admin@example.com',
            'Admin User',
            true
        );

        $this->assertGreaterThan(0, $userId);

        $stmt = self::$db->prepare('SELECT is_admin FROM users WHERE id = ?');
        $stmt->execute([$userId]);
        $result = $stmt->fetch();

        $this->assertEquals(1, $result['is_admin']);
    }

    /**
     * Test successful login attempt
     */
    public function test_successful_login_attempt(): void
    {
        // Create a test user
        $password = 'password123';
        $stmt = self::$db->prepare('
            INSERT INTO users (username, email, password_hash)
            VALUES (?, ?, ?)
        ');
        $stmt->execute(['testuser', 'test@example.com', Auth::hashPassword($password)]);

        // Attempt login
        $result = Auth::attempt('testuser', $password);

        $this->assertTrue($result);
        $this->assertTrue(Auth::check());
    }

    /**
     * Test failed login with incorrect password
     */
    public function test_failed_login_with_incorrect_password(): void
    {
        // Create a test user
        $stmt = self::$db->prepare('
            INSERT INTO users (username, email, password_hash)
            VALUES (?, ?, ?)
        ');
        $stmt->execute(['testuser', 'test@example.com', Auth::hashPassword('password123')]);

        // Attempt login with wrong password
        $result = Auth::attempt('testuser', 'wrong_password');

        $this->assertFalse($result);
        $this->assertFalse(Auth::check());
    }

    /**
     * Test failed login with non-existent user
     */
    public function test_failed_login_with_non_existent_user(): void
    {
        $result = Auth::attempt('nonexistent', 'password123');

        $this->assertFalse($result);
        $this->assertFalse(Auth::check());
    }

    /**
     * Test logout functionality
     */
    public function test_logout(): void
    {
        // Create and login a user
        $userId = Auth::createUser('testuser', 'password123');
        Auth::loginAs($userId);

        $this->assertTrue(Auth::check());

        // Logout
        Auth::logout();

        $this->assertFalse(Auth::check());
        $this->assertNull(Auth::user());
    }

    /**
     * Test getting authenticated user
     */
    public function test_get_authenticated_user(): void
    {
        // Create and login a user
        $userId = Auth::createUser(
            'testuser',
            'password123',
            'test@example.com',
            'Test User'
        );
        Auth::loginAs($userId);

        $user = Auth::user();

        $this->assertIsArray($user);
        $this->assertEquals($userId, $user['id']);
        $this->assertEquals('testuser', $user['username']);
        $this->assertEquals('test@example.com', $user['email']);
        $this->assertEquals('Test User', $user['display_name']);
        $this->assertFalse($user['is_admin']);
    }

    /**
     * Test getting user when not authenticated returns null
     */
    public function test_get_user_when_not_authenticated_returns_null(): void
    {
        $user = Auth::user();

        $this->assertNull($user);
    }

    /**
     * Test getting user ID
     */
    public function test_get_user_id(): void
    {
        $userId = Auth::createUser('testuser', 'password123');
        Auth::loginAs($userId);

        $id = Auth::id();

        $this->assertEquals($userId, $id);
    }

    /**
     * Test getting user ID when not authenticated returns null
     */
    public function test_get_user_id_when_not_authenticated_returns_null(): void
    {
        $id = Auth::id();

        $this->assertNull($id);
    }

    /**
     * Test checking if user is admin
     */
    public function test_is_admin_check(): void
    {
        // Create and login admin user
        $adminId = Auth::createUser('admin', 'admin123', null, null, true);
        Auth::loginAs($adminId);

        $this->assertTrue(Auth::isAdmin());
    }

    /**
     * Test checking if regular user is not admin
     */
    public function test_regular_user_is_not_admin(): void
    {
        // Create and login regular user
        $userId = Auth::createUser('testuser', 'password123', null, null, false);
        Auth::loginAs($userId);

        $this->assertFalse(Auth::isAdmin());
    }

    /**
     * Test updating user password
     */
    public function test_update_password(): void
    {
        $userId = Auth::createUser('testuser', 'old_password');

        $result = Auth::updatePassword($userId, 'new_password');

        $this->assertTrue($result);

        // Verify new password works
        $loginResult = Auth::attempt('testuser', 'new_password');
        $this->assertTrue($loginResult);
    }

    /**
     * Test deleting user
     */
    public function test_delete_user(): void
    {
        $userId = Auth::createUser('testuser', 'password123');

        $result = Auth::deleteUser($userId);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('users', ['id' => $userId]);
    }

    /**
     * Test deleting non-existent user returns false
     */
    public function test_delete_non_existent_user_returns_false(): void
    {
        $result = Auth::deleteUser(99999);

        $this->assertFalse($result);
    }

    /**
     * Test checking if username exists
     */
    public function test_username_exists(): void
    {
        Auth::createUser('testuser', 'password123');

        $exists = Auth::usernameExists('testuser');
        $notExists = Auth::usernameExists('nonexistent');

        $this->assertTrue($exists);
        $this->assertFalse($notExists);
    }

    /**
     * Test checking username exists with exclusion
     */
    public function test_username_exists_with_exclusion(): void
    {
        $userId = Auth::createUser('testuser', 'password123');

        // Should return false when excluding the user's own ID
        $exists = Auth::usernameExists('testuser', $userId);

        $this->assertFalse($exists);
    }

    /**
     * Test login as user (for testing/admin purposes)
     */
    public function test_login_as_user(): void
    {
        $userId = Auth::createUser('testuser', 'password123');

        $result = Auth::loginAs($userId);

        $this->assertTrue($result);
        $this->assertTrue(Auth::check());
        $this->assertEquals($userId, Auth::id());
    }

    /**
     * Test login as non-existent user returns false
     */
    public function test_login_as_non_existent_user_returns_false(): void
    {
        $result = Auth::loginAs(99999);

        $this->assertFalse($result);
        $this->assertFalse(Auth::check());
    }

    /**
     * Test setting active campaign
     */
    public function test_set_active_campaign(): void
    {
        $userId = Auth::createUser('testuser', 'password123');
        Auth::loginAs($userId);

        Auth::setActiveCampaignId(42);

        $this->assertEquals(42, Auth::getActiveCampaignId());
        $this->assertTrue(Auth::hasActiveCampaign());
    }

    /**
     * Test getting active campaign when not set returns null
     */
    public function test_get_active_campaign_when_not_set_returns_null(): void
    {
        $userId = Auth::createUser('testuser', 'password123');
        Auth::loginAs($userId);

        $campaignId = Auth::getActiveCampaignId();

        $this->assertNull($campaignId);
        $this->assertFalse(Auth::hasActiveCampaign());
    }

    /**
     * Test clearing active campaign
     */
    public function test_clear_active_campaign(): void
    {
        $userId = Auth::createUser('testuser', 'password123');
        Auth::loginAs($userId);
        Auth::setActiveCampaignId(42);

        Auth::clearActiveCampaignId();

        $this->assertNull(Auth::getActiveCampaignId());
        $this->assertFalse(Auth::hasActiveCampaign());
    }

    /**
     * Test auth check returns false when not logged in
     */
    public function test_check_returns_false_when_not_logged_in(): void
    {
        $result = Auth::check();

        $this->assertFalse($result);
    }

    /**
     * Test auth check returns true when logged in
     */
    public function test_check_returns_true_when_logged_in(): void
    {
        $userId = Auth::createUser('testuser', 'password123');
        Auth::loginAs($userId);

        $result = Auth::check();

        $this->assertTrue($result);
    }

    /**
     * Test session persistence across auth checks
     */
    public function test_session_persistence(): void
    {
        $userId = Auth::createUser('testuser', 'password123', 'test@example.com');
        Auth::loginAs($userId);

        // First check
        $user1 = Auth::user();

        // Second check (should use cached session data)
        $user2 = Auth::user();

        $this->assertEquals($user1, $user2);
        $this->assertEquals($userId, $user2['id']);
        $this->assertEquals('testuser', $user2['username']);
    }
}
