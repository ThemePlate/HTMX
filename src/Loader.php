<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\HTMX;

class Loader {

	public readonly string $location;


	public function __construct( string $location = null ) {

		$location = null === $location ? Helpers::DEFAULT_NAMEPATH : $location;

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


	public function load( string $template ): void {

		if ( ! $this->is_valid( $template ) ) {
			return;
		}

		include $this->file_path( $template );

	}


	public function is_valid( string $template ): bool {

		$template = Helpers::prepare_pathname( $template );

		return file_exists( $this->file_path( $template ) );

	}

}
