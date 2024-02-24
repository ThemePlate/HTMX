<?php

namespace Tests;

use stdClass;
use ThemePlate\HTMX\Handler;
use PHPUnit\Framework\TestCase;

class HandlerTest extends TestCase {
	public function test_execute_registered_method() {
		$method     = 'name';
		$params     = array( 1, 'two' );
		$handler    = new Handler( 'test' );
		$identifier = $handler->identifier;

		$handler->handle(
			$method,
			function ( $i, $p ) use ( $identifier, $params ) {
				$this->assertSame( $identifier, $i );
				$this->assertSame( $params, $p );

				return true;
			}
		);

		$_SERVER[ $handler->header_key() ] = true;

		$this->assertTrue( $handler->execute( $method, $params ) );
	}

	public function test_execute_returns_false_if_method_not_registered() {
		$this->assertFalse( ( new Handler( 'identifier' ) )->execute( 'method', array() ) );
	}

	public function test_execute_return_on_empty_identifier() {
		$handler = new Handler( '' );

		$handler->handle(
			'OPTION',
			function () {
				return true;
			}
		);

		$this->assertTrue( $handler->execute( 'OPTION', array() ) );
		$this->assertArrayNotHasKey( $handler->header_key(), $_SERVER );
		$this->assertNotEmpty( $_SERVER );
	}

	public function test_handle_multiple_methods() {
		$handles    = array(
			'method1' => array( true, array( false, null ) ),
			'method2' => array( false, array( new stdClass() ) ),
		);
		$handler    = new Handler( 'Custom-Request' );
		$identifier = $handler->identifier;

		foreach ( $handles as $method => $data ) {
			$handler->handle(
				$method,
				function ( $i, $p ) use ( $identifier, $data ) {
					$this->assertSame( $identifier, $i );
					$this->assertSame( $data[1], $p );

					return $data[0];
				}
			);

			$_SERVER[ $handler->header_key() ] = true;

			$result = $handler->execute( $method, $data[1] );

			if ( $data[0] ) {
				$this->assertTrue( $result );
			} else {
				$this->assertFalse( $result );
			}
		}
	}
}
