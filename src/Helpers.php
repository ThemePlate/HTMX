<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\HTMX;

class Helpers {

	public const DEFAULT_NAMEPATH = 'htmx';

	public const HTTP_METHODS = array(
		'GET',
		'POST',
		'PUT',
		'PATCH',
		'DELETE',
	);


	public static function prepare_pathname( string $value ): string {

		$value = str_replace( '\\', '/', $value );

		return trim( $value, '/ ' );

	}


	public static function caller_path(): string {

		$traced = debug_backtrace(); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_debug_backtrace

		return dirname( $traced[1]['file'] ) . DIRECTORY_SEPARATOR;

	}


	public static function valid_nonce( string $value ): bool {

		return isset( $_SERVER['HTTP_HX_NONCE'] ) && wp_verify_nonce( $_SERVER['HTTP_HX_NONCE'], $value );

	}

}
