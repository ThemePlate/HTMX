<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\HTMX;

class Handler {

	public readonly string $identifier;
	protected array $handles = array();


	public function __construct( string $identifier ) {

		$this->identifier = $identifier;

	}


	public function handle( string $method, callable $action ): void {

		$this->handles[ $method ] = $action;

	}


	public function execute( string $method, array $params ): bool {

		if ( empty( $this->handles[ $method ] ) ) {
			return false;
		}

		return call_user_func_array( $this->handles[ $method ], array( $this->identifier, $params ) );

	}

}
