<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\HTMX;

class Loader {

	public readonly string $location;


	public function __construct( string $location = Helpers::DEFAULT_NAMEPATH ) {

		if ( ! path_is_absolute( $location ) ) {
			$location = Helpers::prepare_pathname( $location );

			if ( '' === $location ) {
				$location = Helpers::DEFAULT_NAMEPATH;
			}

			$location = Helpers::caller_path() . $location;
		}

		$this->location = $location;

	}


	protected function file_path( string $name ): string {

		return realpath( $this->location . DIRECTORY_SEPARATOR . $name . '.php' );

	}


	// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
	public function load( string $template, array $params ): bool {

		if ( ! $this->is_valid( $template ) ) {
			return false;
		}

		return ( function () {
			$params = func_get_arg( 0 );

			return include $this->file_path( func_get_arg( 1 ) );
		} )( $params, $template );

	}


	public function is_valid( string $template ): bool {

		$template = Helpers::prepare_pathname( $template );

		return file_exists( $this->file_path( $template ) );

	}

}
