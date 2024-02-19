<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate\HTMX;

use WP;

class Router {

	public readonly string $prefix;
	protected array $routes = array();


	public function __construct( string $prefix = null ) {

		if ( null === $prefix ) {
			$prefix = Helpers::DEFAULT_NAMEPATH;
		}

		$prefix = Helpers::prepare_pathname( $prefix );

		if ( '' === $prefix ) {
			$prefix = Helpers::DEFAULT_NAMEPATH;
		}

		$this->prefix = $prefix;

	}


	public function init(): void {

		add_rewrite_endpoint( $this->prefix, EP_ROOT );

		if ( $this->is_valid( $_SERVER['REQUEST_URI'] ) ) {
			add_action( 'wp', array( $this, 'route' ) );
		}

	}


	public function is_valid( string $endpoint, bool $with_prefix = true ): bool {

		$path  = wp_parse_url( $endpoint, PHP_URL_PATH );
		$clean = Helpers::prepare_pathname( $path );
		$parts = explode( '/', $clean );

		if ( $with_prefix && $parts[0] !== $this->prefix ) {
			return false;
		}

		return count( $parts ) === count( array_filter( $parts ) );

	}


	public function route( WP $wp ): void {

		if (
			isset( $wp->query_vars[ $this->prefix ] ) &&
			Helpers::valid_nonce( $this->prefix )
		) {
			$route = Helpers::prepare_pathname( $wp->query_vars[ $this->prefix ] );

			if ( isset( $this->routes[ $route ] ) ) {
				if ( $this->dispatch( $route ) ) {
					die();
				}
			}
		}

		global $wp_query;

		$wp_query->set_404();
		status_header( 404 );
		nocache_headers();

	}


	public function add( string $route, callable $handler ): bool {

		$route = Helpers::prepare_pathname( $route );

		if ( ! $this->is_valid( $route, false ) ) {
			return false;
		}

		$this->routes[ $route ] = $handler;

		return true;

	}


	public function dispatch( string $route ): bool {

		return call_user_func_array( $this->routes[ $route ], array( $route ) );

	}

}
