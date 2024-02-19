# ThemePlate HTMX

## Usage

```php
$htmx = new ThemePlate\HTMX(/* identifier */);

add_action( 'init', array( $htmx, 'setup' ) );
add_action( 'wp_enqueue_scripts', array( $htmx->cdn(/* version */), 'assets' ) );
```

> **Identifier** is used as:
>
> * the route namespace
> * swap templates location
>
> **Defaults to `htmx`*

### Local script copy

```php
add_action( 'wp_enqueue_scripts', function() use ( $htmx ) {
	wp_enqueue_script( 'htmx', '/path/to/htmx.min.js' );
	$htmx->assets();
} );
```
