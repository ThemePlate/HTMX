<?php

/**
 * @package ThemePlate
 */

namespace ThemePlate;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ThemePlate\HTMX\Handler;
use ThemePlate\HTMX\Helpers;
use ThemePlate\HTMX\Router;
use ThemePlate\HTMX\Loader;

class HTMX {

	public readonly Router $router;
	public readonly Loader $loader;
	protected ?string $cdn = null;


	public function __construct( string $identifier = null ) {

		$this->router = new Router( $identifier );
		$this->loader = new Loader( Helpers::caller_path() . $this->router->prefix );

	}


	public function setup(): void {

		$iterator = new RecursiveDirectoryIterator( $this->loader->location );

		foreach ( new RecursiveIteratorIterator( $iterator ) as $item ) {
			if ( ! $item->isFile() || $item->getExtension() !== 'php' ) {
				continue;
			}

			$path = str_replace(
				array(
					$this->loader->location . DIRECTORY_SEPARATOR,
					'.php',
				),
				'',
				$item->getPathname()
			);

			$handler = new Handler( $path );

			foreach ( Helpers::HTTP_METHODS as $method ) {
				$handler->handle( $method, array( $this->loader, 'load' ) );
			}

			$this->router->add( $path, $handler );
		}

		$this->router->init();

	}


	public function cdn( string $version = 'latest' ): HTMX {

		$this->cdn = $version;

		return $this;

	}


	public function assets(): void {

		if ( $this->cdn ) {
			$version = '@' . $this->cdn;

			if ( '@latest' === $version ) {
				$version = '';
			}

			wp_enqueue_script(
				'htmx',
				'https://unpkg.com/htmx.org' . $version,
				array(),
				$this->cdn,
				array(
					'in_footer' => true,
				)
			);
		}

		$config = sprintf(
			"document.body.addEventListener('htmx:configRequest', function(evt) {evt.detail.headers['HX-Nonce'] = '%s'});",
			wp_create_nonce( $this->router->prefix )
		);

		wp_add_inline_script( 'htmx', $config );

	}

}
