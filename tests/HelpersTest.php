<?php

namespace Tests;

use ThemePlate\HTMX\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase {
	protected function for_prepare_pathname(): array {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return array(
			'correct' => array( 'test', 'test' ),
			'prefixed' => array( '/test', 'test' ),
			'suffixed' => array( 'test/', 'test' ),
			'windows' => array( 'C:\folder\test', 'C:/folder/test' ),
			'extras' => array( '/test// ', 'test' ),
			'deep'  => array( ' //deep/test', 'deep/test' ),
			'empty' => array( '', '' ),
			'root'  => array( '/', '' ),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	}

	/**
	 * @dataProvider for_prepare_pathname
	 */
	public function test_prepare_pathname( string $value, string $expected ): void {
		$this->assertSame( $expected, Helpers::prepare_pathname( $value ) );
	}
}
