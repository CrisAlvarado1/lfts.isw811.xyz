[< Volver al índice](/docs/readme.md)

# Use Caching for Expensive Operations

En este capítulo, se discute la importancia de implementar el caching en operaciones que pueden ser solicitadas frecuentemente y que su contenido cambia rara vez. Como lo es nuestra solicitud a la función `file_get_contents` en el endpoint que carga los posts. Por esto, se va a realizar el caching en la ruta `posts/{post}`, con el fin de mejorar el rendimiento.

## Probar cacheo en los posts

Inicialmente, con Laravel es sencillo llevar a cabo el almacenamiento en caché utilizando `cache()->remember()`. En donde, el primer parámetro es una llave única para el post, el segundo es el tiempo de duración del caché y el tercero la acción a realizar. En este caso, se le permite a la función (acción )hacer uso de path, para poder obtener el contenido con `file_get_contents` y almacenarlo en caché.

```php
Route::get('posts/{post}', function ($slug) {
    $path = __DIR__ . "/../resources/posts/{$slug}.html";

    if (! file_exists($path)) {
        return redirect('/');
    }

    $post = cache()->remember("posts.{$slug}", 5, function () use ($path) {
        var_dump('file_get_contents');
        return file_get_contents($path);
    });

    return view('post', [
        'post' => $post
    ]);
})->where('post', '[A-z_\-]+');
```

El uso de `var_dump('file_get_contents');` nos permite mostrar el momento en el que se realiza la operación de caché (cada 5 segundos), esto se vería de la siguiente forma:

![Prueba de cuando se realiza el cache](images/var-dump-cache-v6.png)

## Aumentar tiempo de caché y limpiar código

Para este punto, se aumentará el tiempo de caché a 1200 segundos y se limpiará el código declarando la variable `$path` dentro de la validación e implementando una función flecha (`fn() =>`) en el tercer parámetro.

```php
Route::get('posts/{post}', function ($slug) {
    if (! file_exists($path = __DIR__ . "/../resources/posts/{$slug}.html")) {
        return redirect('/');
    }

    $post = cache()->remember("posts.{$slug}", 1200, fn() => file_get_contents($path));

    return view('post', [
        'post' => $post
    ]);
})->where('post', '[A-z_\-]+');
```

Una vez finalizados estos cambios, el contenido de los posts se cacheará durante 20 minutos y el tiempo de respuesta al ingresar a los posts mejorará considerablemente.
