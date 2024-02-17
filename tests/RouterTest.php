<?php

namespace Tests;

use ThemePlate\HTMX\Helpers;
use ThemePlate\HTMX\Router;
use PHPUnit\Framework\TestCase;
use function Brain\Monkey\Functions\expect;

class RouterTest extends TestCase {
	protected function for_prefix(): array {
		return array(
			'null'  => array( null, Helpers::DEFAULT_NAMEPATH ),
			'empty' => array( '', Helpers::DEFAULT_NAMEPATH ),
			'root'  => array( '/', Helpers::DEFAULT_NAMEPATH ),
		);
	}

	/**
	 * @dataProvider for_prefix
	 */
	public function test_prefix( ?string $prefix, string $expected ): void {
		$this->assertSame( $expected, ( new Router( $prefix ) )->prefix );
	}

	protected function for_is_valid(): array {
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

	/**
	 * @dataProvider for_is_valid
	 */
	public function test_is_valid( string $path, bool $is_valid ): void {
		expect( 'wp_parse_url' )->once()->andReturnUsing(
			function ( ...$args ) {
				return call_user_func_array( 'parse_url', $args );
			}
		);

		if ( $is_valid ) {
			$this->assertTrue( ( new Router( 'test' ) )->is_valid( $path ) );
		} else {
			$this->assertFalse( ( new Router( 'test' ) )->is_valid( $path ) );
		}
	}
}
