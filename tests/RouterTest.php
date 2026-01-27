<?php

namespace Worlds\Tests;

use Worlds\Config\Router;
use Worlds\Config\Request;
use Worlds\Config\Response;

/**
 * Router Test
 *
 * Tests route registration, matching, parameter extraction,
 * and request dispatching functionality.
 */
class RouterTest extends TestCase
{
    private Router $router;

    /**
     * Set up before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->router = new Router();
    }

    /**
     * Test registering a GET route
     */
    public function test_register_get_route(): void
    {
        $this->router->get('/test', function () {
            return 'test response';
        });

        $this->assertTrue($this->router->hasRoute('GET', '/test'));
    }

    /**
     * Test registering a POST route
     */
    public function test_register_post_route(): void
    {
        $this->router->post('/test', function () {
            return 'test response';
        });

        $this->assertTrue($this->router->hasRoute('POST', '/test'));
    }

    /**
     * Test registering a PUT route
     */
    public function test_register_put_route(): void
    {
        $this->router->put('/test', function () {
            return 'test response';
        });

        $this->assertTrue($this->router->hasRoute('PUT', '/test'));
    }

    /**
     * Test registering a DELETE route
     */
    public function test_register_delete_route(): void
    {
        $this->router->delete('/test', function () {
            return 'test response';
        });

        $this->assertTrue($this->router->hasRoute('DELETE', '/test'));
    }

    /**
     * Test method chaining for route registration
     */
    public function test_route_registration_method_chaining(): void
    {
        $result = $this->router
            ->get('/get', fn() => 'get')
            ->post('/post', fn() => 'post')
            ->put('/put', fn() => 'put')
            ->delete('/delete', fn() => 'delete');

        $this->assertInstanceOf(Router::class, $result);
        $this->assertTrue($this->router->hasRoute('GET', '/get'));
        $this->assertTrue($this->router->hasRoute('POST', '/post'));
        $this->assertTrue($this->router->hasRoute('PUT', '/put'));
        $this->assertTrue($this->router->hasRoute('DELETE', '/delete'));
    }

    /**
     * Test dispatching simple route
     */
    public function test_dispatch_simple_route(): void
    {
        $this->router->get('/test', function () {
            return 'test response';
        });

        $request = new Request('GET', '/test');
        $result = $this->router->dispatch($request);

        $this->assertEquals('test response', $result);
    }

    /**
     * Test route with parameter extraction
     */
    public function test_route_with_single_parameter(): void
    {
        $this->router->get('/user/{id}', function (Request $request, array $params) {
            return 'User ID: ' . $params['id'];
        });

        $request = new Request('GET', '/user/42');
        $result = $this->router->dispatch($request);

        $this->assertEquals('User ID: 42', $result);
    }

    /**
     * Test route with multiple parameters
     */
    public function test_route_with_multiple_parameters(): void
    {
        $this->router->get('/campaign/{campaignId}/entity/{entityId}', function (Request $request, array $params) {
            return sprintf('Campaign: %s, Entity: %s', $params['campaignId'], $params['entityId']);
        });

        $request = new Request('GET', '/campaign/10/entity/25');
        $result = $this->router->dispatch($request);

        $this->assertEquals('Campaign: 10, Entity: 25', $result);
    }

    /**
     * Test parameter URL decoding
     */
    public function test_parameter_url_decoding(): void
    {
        $this->router->get('/search/{query}', function (Request $request, array $params) {
            return 'Query: ' . $params['query'];
        });

        $request = new Request('GET', '/search/hello%20world');
        $result = $this->router->dispatch($request);

        $this->assertEquals('Query: hello world', $result);
    }

    /**
     * Test route matching is method-specific
     */
    public function test_route_matching_is_method_specific(): void
    {
        $this->router->get('/test', function () {
            return 'GET response';
        });

        $this->router->post('/test', function () {
            return 'POST response';
        });

        $getRequest = new Request('GET', '/test');
        $postRequest = new Request('POST', '/test');

        $getResult = $this->router->dispatch($getRequest);
        $postResult = $this->router->dispatch($postRequest);

        $this->assertEquals('GET response', $getResult);
        $this->assertEquals('POST response', $postResult);
    }

    /**
     * Test 404 handler for unmatched routes
     */
    public function test_404_handler_for_unmatched_route(): void
    {
        $this->router->setNotFoundHandler(function (Request $request) {
            return 'Custom 404';
        });

        $request = new Request('GET', '/nonexistent');
        $result = $this->router->dispatch($request);

        $this->assertEquals('Custom 404', $result);
    }

    /**
     * Test default 404 page
     */
    public function test_default_404_page(): void
    {
        ob_start();
        $request = new Request('GET', '/nonexistent');
        $this->router->dispatch($request);
        $output = ob_get_clean();

        $this->assertStringContainsString('404', $output);
        $this->assertStringContainsString('Page Not Found', $output);
    }

    /**
     * Test route pattern normalization (adding leading slash)
     */
    public function test_route_pattern_normalization(): void
    {
        $this->router->get('test', function () {
            return 'normalized';
        });

        $request = new Request('GET', '/test');
        $result = $this->router->dispatch($request);

        $this->assertEquals('normalized', $result);
    }

    /**
     * Test parsing query string
     */
    public function test_parse_query_string(): void
    {
        $queryString = 'name=John&age=30&city=New%20York';
        $parsed = $this->router->parseQueryString($queryString);

        $this->assertEquals('John', $parsed['name']);
        $this->assertEquals('30', $parsed['age']);
        $this->assertEquals('New York', $parsed['city']);
    }

    /**
     * Test parsing empty query string
     */
    public function test_parse_empty_query_string(): void
    {
        $parsed = $this->router->parseQueryString('');

        $this->assertIsArray($parsed);
        $this->assertEmpty($parsed);
    }

    /**
     * Test clearing routes
     */
    public function test_clear_routes(): void
    {
        $this->router->get('/test1', fn() => 'test1');
        $this->router->post('/test2', fn() => 'test2');

        $this->assertTrue($this->router->hasRoute('GET', '/test1'));
        $this->assertTrue($this->router->hasRoute('POST', '/test2'));

        $this->router->clear();

        $this->assertFalse($this->router->hasRoute('GET', '/test1'));
        $this->assertFalse($this->router->hasRoute('POST', '/test2'));
    }

    /**
     * Test getting all registered routes
     */
    public function test_get_all_routes(): void
    {
        $this->router->get('/route1', fn() => 'r1');
        $this->router->post('/route2', fn() => 'r2');
        $this->router->get('/route3', fn() => 'r3');

        $routes = $this->router->getRoutes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('POST', $routes);
        $this->assertCount(2, $routes['GET']);
        $this->assertCount(1, $routes['POST']);
    }

    /**
     * Test route with special characters in pattern
     */
    public function test_route_with_special_characters_in_pattern(): void
    {
        $this->router->get('/api/v1.0/users', function () {
            return 'API response';
        });

        $request = new Request('GET', '/api/v1.0/users');
        $result = $this->router->dispatch($request);

        $this->assertEquals('API response', $result);
    }

    /**
     * Test route parameter cannot contain forward slash
     */
    public function test_route_parameter_cannot_contain_forward_slash(): void
    {
        $this->router->get('/file/{name}', function (Request $request, array $params) {
            return 'File: ' . $params['name'];
        });

        // This should NOT match because parameter can't contain /
        $request = new Request('GET', '/file/path/to/file.txt');

        ob_start();
        $this->router->dispatch($request);
        $output = ob_get_clean();

        // Should get 404 instead of matching
        $this->assertStringContainsString('404', $output);
    }

    /**
     * Test duplicate parameter names throw exception
     */
    public function test_duplicate_parameter_names_throw_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Duplicate parameter name');

        $this->router->get('/user/{id}/friend/{id}', fn() => 'test');
    }

    /**
     * Test route matching is case-sensitive for path
     */
    public function test_route_matching_is_case_sensitive_for_path(): void
    {
        $this->router->get('/Test', function () {
            return 'capital T';
        });

        $requestLower = new Request('GET', '/test');

        ob_start();
        $this->router->dispatch($requestLower);
        $output = ob_get_clean();

        // Should not match - paths are case-sensitive
        $this->assertStringContainsString('404', $output);
    }

    /**
     * Test route matching is case-insensitive for HTTP method
     */
    public function test_route_matching_is_case_insensitive_for_method(): void
    {
        $this->router->get('/test', function () {
            return 'response';
        });

        // Request methods are normalized to uppercase
        $request = new Request('get', '/test');
        $result = $this->router->dispatch($request);

        $this->assertEquals('response', $result);
    }

    /**
     * Test accessing request object in route handler
     */
    public function test_access_request_object_in_handler(): void
    {
        $this->router->get('/test', function (Request $request) {
            return 'Method: ' . $request->getMethod() . ', Path: ' . $request->getPath();
        });

        $request = new Request('GET', '/test');
        $result = $this->router->dispatch($request);

        $this->assertEquals('Method: GET, Path: /test', $result);
    }

    /**
     * Test route with no parameters works correctly
     */
    public function test_route_with_no_parameters_receives_empty_array(): void
    {
        $this->router->get('/test', function (Request $request) {
            return 'No params needed';
        });

        $request = new Request('GET', '/test');
        $result = $this->router->dispatch($request);

        $this->assertEquals('No params needed', $result);
    }

    /**
     * Test getting current request object
     */
    public function test_get_current_request_object(): void
    {
        $this->router->get('/test', function () {
            return 'test';
        });

        $request = new Request('GET', '/test');
        $this->router->dispatch($request);

        $currentRequest = $this->router->getRequest();

        $this->assertInstanceOf(Request::class, $currentRequest);
        $this->assertEquals('/test', $currentRequest->getPath());
    }

    /**
     * Test root path route
     */
    public function test_root_path_route(): void
    {
        $this->router->get('/', function () {
            return 'home page';
        });

        $request = new Request('GET', '/');
        $result = $this->router->dispatch($request);

        $this->assertEquals('home page', $result);
    }

    /**
     * Test nested path routes
     */
    public function test_nested_path_routes(): void
    {
        $this->router->get('/api/v1/users/profile', function () {
            return 'profile';
        });

        $request = new Request('GET', '/api/v1/users/profile');
        $result = $this->router->dispatch($request);

        $this->assertEquals('profile', $result);
    }

    /**
     * Test route parameter with numbers
     */
    public function test_route_parameter_with_numbers(): void
    {
        $this->router->get('/entity/{id}', function (Request $request, string $id) {
            return 'ID: ' . $id;
        });

        $request = new Request('GET', '/entity/12345');
        $result = $this->router->dispatch($request);

        $this->assertEquals('ID: 12345', $result);
    }

    /**
     * Test route parameter with alphanumeric
     */
    public function test_route_parameter_with_alphanumeric(): void
    {
        $this->router->get('/user/{username}', function (Request $request, string $username) {
            return 'Username: ' . $username;
        });

        $request = new Request('GET', '/user/john_doe123');
        $result = $this->router->dispatch($request);

        $this->assertEquals('Username: john_doe123', $result);
    }
}
