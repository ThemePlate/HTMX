<?php

declare(strict_types=1);

namespace Tests;

use Brain\Monkey;
use PHPUnit\Framework\Attributes\DataProvider;
use ThemePlate\HTMX\Handler;
use ThemePlate\HTMX\Helpers;
use ThemePlate\HTMX\Router;
use PHPUnit\Framework\TestCase;
use function Brain\Monkey\Functions\expect;

final class RouterTest extends TestCase {
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	protected function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	public static function for_prefix(): array {
		return array(
			'null'  => array( null, Helpers::DEFAULT_NAMEPATH ),
			'empty' => array( '', Helpers::DEFAULT_NAMEPATH ),
			'root'  => array( '/', Helpers::DEFAULT_NAMEPATH ),
		);
	}

	#[DataProvider( 'for_prefix' )]
	public function test_prefix( ?string $prefix, string $expected ): void {
		$router = null === $prefix ? new Router() : new Router( $prefix );

		$this->assertSame( $expected, $router->prefix );
	}

	protected function stub_wp_parse_url( int $count = 1 ): void {
		expect( 'wp_parse_url' )->times( $count )->andReturnUsing(
			function ( ...$args ) {
				return call_user_func_array( 'parse_url', $args );
			}
		);
	}

	public static function for_init(): array {
		defined( 'EP_ROOT' ) || define( 'EP_ROOT', 64 );

		return array(
			'known'   => array( true ),
			'unknown' => array( false ),
		);
	}

	#[DataProvider( 'for_init' )]
	public function test_init( bool $wanted ): void {
		$prefix = 'test';
		$router = new Router( $prefix );

		$_SERVER['REQUEST_URI'] = $wanted ? $prefix : 'unknown';

		expect( 'add_rewrite_endpoint' )->once()->with( $prefix, EP_ROOT );

		$this->stub_wp_parse_url();
		$router->init();
		$this->assertSame( $wanted ? 10 : false, has_action( 'wp', array( $router, 'route' ) ) );
	}

	public static function for_is_valid(): array {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'base' => array( 'test', true ),
			'sub' => array( 'test/this', true ),
			'slashed' => array( '/test/this/', true ),
			'extras' => array( '//test//this', false ),
			'deep' => array( '/test/this/please// ', true ),
			'unknown' => array( 'tester', false ),
			'empty' => array( '', false ),
			'root' => array( '/', false ),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}

	#[DataProvider( 'for_is_valid' )]
	public function test_is_valid( string $path, bool $is_valid ): void {
		$this->stub_wp_parse_url();

		$result = ( new Router( 'test' ) )->is_valid( $path );

		if ( $is_valid ) {
			$this->assertTrue( $result );
		} else {
			$this->assertFalse( $result );
		}
	}

	public static function for_add_route(): array {
		$values = self::for_is_valid();

		$values['known'] = $values['unknown'];

		$values['known'][1] = true;

		unset( $values['unknown'] );

		return $values;
	}

	#[DataProvider( 'for_add_route' )]
	public function test_add_route( string $path, bool $is_valid ): void {
		$this->stub_wp_parse_url();

		$result = ( new Router( 'test' ) )->add( $path, new Handler( 'test' ) );

		if ( $is_valid ) {
			$this->assertTrue( $result );
		} else {
			$this->assertFalse( $result );
		}
	}

	#[DataProvider( 'for_init' )]
	public function test_dispatch( bool $is_known ): void {
		$this->stub_wp_parse_url();

		$p_id_r  = 'test';
		$router  = new Router( $p_id_r );
		$handler = new Handler( $p_id_r );

		$handler->handle(
			'POST',
			function (): true {
				return true;
			}
		);
		$router->add( $p_id_r, $handler );

		if ( $is_known ) {
			$_SERVER[ $handler->header_key() ] = true;

			$this->assertTrue( $router->dispatch( $p_id_r, 'POST' ) );
		} else {
			$this->assertFalse( $router->dispatch( $p_id_r, 'GET' ) );
		}
	}

	#[DataProvider( 'for_init' )]
	public function test_basic_routes( bool $is_known ): void {
		$route  = 'tester';
		$router = new Router();

		if ( $is_known ) {
			$this->stub_wp_parse_url( count( Helpers::HTTP_METHODS ) );

			foreach ( Helpers::HTTP_METHODS as $method ) {
				$router->$method(
					$route,
					function (): true {
						return true;
					}
				);
			}
		}

		$_SERVER[ Helpers::header_key( $router->prefix ) ] = true;

		$return = $router->dispatch( $route, $is_known ? $method : 'OPTION' );

		if ( $is_known ) {
			$this->assertTrue( $return );
		} else {
			$this->assertFalse( $return );
		}
	}
}
