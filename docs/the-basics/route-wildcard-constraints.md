[< Volver al índice](/docs/readme.md)

# Route Wildcard Constraints

En Laravel, existen una gran variedad de restricciones que se pueden aplicar a un wildcard en las rutas. Esto nos permite limitar y asegurar que solo se acepten valores deseados o específicos en las rutas. Para este capítulo, añadiremos una restricción que acepte valores de la A la z, el carácter del guion `-` y el del guion bajo `_` en el endpoint `posts/{post}`. De esta manera limitaremos los valores que puedan ser ingresados y tomados por la variable `$slug`.

## Añadir una restricción a un endpoint

Para añadir una restricción (constraint) a un endpoint con un wildcard en Laravel es bastante sencillo, únicamente se agrega debajo de la definición de la ruta el método `where()`.

```php
Route::get('posts/{nombre-del-wildcard}', function () {
    ...
})->where('nombre-del-wildcard', 'expresión-irregular');
```

## Ejemplos de restricciones

### Aceptar cualquier letra (A-z) con expresión regular

Acepta cualquier valor de la A a la z, ya sea en mayúsculas o minúsculas.

```php
Route::get('posts/{post}', function ($slug) {
    ...
})->where('post', '[A-z]+');
```

### Aceptar letras y guiones (A-z, '-' y '\_') con expresión regular

Acepta cualquier valor de la A a la z, ya sea en mayúsculas o minúsculas, y también acepta los caracteres `-` y `_`.

```php
Route::get('posts/{post}', function ($slug) {
    ...
})->where('post', '[A-z_\-]+');
```

### Aceptar letras (A-z) con `whereAlpha()`

Acepta cualquier letra de la A a la Z, ya sea en mayúsculas o minúsculas.

```php
Route::get('posts/{post}', function ($slug) {
    ...
})->whereAlpha('post');
```

### Aceptar letras y números (A-Z y 0-9) con `whereAlphaNumeric()`

Acepta cualquier letra de la A a la Z, ya sea en mayúsculas o minúsculas, y también números.

```php
Route::get('posts/{post}', function ($slug) {
    ...
})->whereAlphaNumeric('post');
```

### Aceptar números (0-9) con `whereNumber()`

Acepta únicamente números.

```php
Route::get('posts/{post}', function ($slug) {
    ...
})->whereNumber('post');
```

## Restricción utilizada

En nuestro caso, la restricción que utilizaremos nos permite ingresar en el slug cualquier letra, ya sea en mayúscula o minúscula, y los caracteres de `-` y `_`. Y el endpoint `posts/{post}` resultaría en lo siguiente:

```php
Route::get('posts/{post}', function ($slug) {
    $path = __DIR__ . "/../resources/posts/{$slug}.html";

    if (! file_exists($path)) {
        return redirect('/');
    }

    $post = file_get_contents($path);

    return view('post', [
        'post' => $post
    ]);
})->where('post', '[A-z_\-]+');
```
