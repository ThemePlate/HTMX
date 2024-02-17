<?php

namespace Tests;

use ThemePlate\HTMX\Helpers;
use ThemePlate\HTMX\Loader;
use PHPUnit\Framework\TestCase;
use function Brain\Monkey\Functions\expect;

class LoaderTest extends TestCase {
	protected function for_location(): array {
		return array(
			'null'  => array( null, Helpers::DEFAULT_NAMEPATH ),
			'empty' => array( '', Helpers::DEFAULT_NAMEPATH ),
			'root'  => array( '/', Helpers::DEFAULT_NAMEPATH ),
		);
	}

	/**
	 * @dataProvider for_location
	 */
	public function test_location( ?string $location, string $expected ): void {
		expect( 'path_is_absolute' )->once()->andReturn( false );

		$this->assertSame( __DIR__ . DIRECTORY_SEPARATOR . $expected, ( new Loader( $location ) )->location );
	}

	protected function for_is_valid_template(): array {
		return array(
			array( 'HelpersTest', true ),
			array( 'RouterTest', true ),
			array( 'nonexistent', false ),
		);
	}

	/**
	 * @dataProvider for_is_valid_template
	 */
	public function test_is_valid_template_absolute( string $name, bool $is_valid ): void {
		expect( 'path_is_absolute' )->once()->andReturn( true );

		if ( $is_valid ) {
			$this->assertTrue( ( new Loader( __DIR__ ) )->is_valid( $name ) );
		} else {
			$this->assertFalse( ( new Loader( __DIR__ ) )->is_valid( $name ) );
		}
	}

	/**
	 * @dataProvider for_is_valid_template
	 */
	public function test_is_valid_template_relative( string $name, bool $is_valid ): void {
		expect( 'path_is_absolute' )->once()->andReturn( false );

		if ( $is_valid ) {
			$this->assertTrue( ( new Loader( '../tests' ) )->is_valid( $name ) );
		} else {
			$this->assertFalse( ( new Loader( '../tests' ) )->is_valid( $name ) );
		}
	}
}
