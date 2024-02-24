<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\HTMX;

class Handler {

	public readonly string $identifier;
	protected array $handles = array();


	public function __construct( string $identifier ) {

		$this->identifier = Helpers::prepare_header( $identifier );

	}


	public function handle( string $method, callable $action ): void {

		$this->handles[ $method ] = $action;

	}


	public function header_key(): string {

		$header = strtoupper( $this->identifier );
		$header = str_replace( '-', '_', $header );

		return 'HTTP_' . $header;

	}


	public function is_valid(): bool {

		$header = $this->header_key();

		return isset( $_SERVER[ $header ] ) && $_SERVER[ $header ];

	}


	public function execute( string $method, array $params ): bool {

		if (
			! $this->is_valid() ||
			empty( $this->handles[ $method ] )
		) {
			return false;
		}

		return call_user_func_array( $this->handles[ $method ], array( $this->identifier, $params ) );

	}

}
