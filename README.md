# ThemePlate HTMX

## Usage

```php
$htmx = new ThemePlate\HTMX(/* identifier */);

add_action( 'init', array( $htmx, 'setup' ) );
add_action( 'wp_enqueue_scripts', array( $htmx, 'assets' ) );
```

> **Identifier** is used as:
>
> * the route namespace
> * swap templates location
>
> **Defaults to `htmx`*
