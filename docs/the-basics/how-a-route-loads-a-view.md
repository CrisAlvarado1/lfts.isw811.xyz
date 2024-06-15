[< Volver al índice](/docs/readme.md)

# How a Route Loads a View

En Laravel las rutas se configuran en el archivo `routes/web.php` y las vistas se encuentran en el directorio `resources/views`.

Por ejemplo con el siguiente fragmento se renderiza la vista _Welcome_. No es necesario incluir la parte de `.blade.php`, ya que Laravel lo interpreta.

```php
Route::get('/', function () {
    return view('welcome');
});
```

Además, se puede configurar el endpoint para que la vista _Welcome_ se vea en una ruta distinta, por ejemplo, en este caso será accesible por medio de `http://lfts.isw811.xyz/hello`.

```php
Route::get('/hello', function () {
    return view('welcome');
});
```

Laravel puede retorna cualquier tipo de contenido, por ejemplo, código HTML.

```php
Route::get('/html', function () {
    return '<h1>Esto es HTML</h1>';
});
```

También es posible retornar valores de tipo string.

```php
Route::get('/', function () {
    return 'hello world';
});
```

Incluso puede retornar JSON (muy útil para construir APIs).

```php
Route::get('/json', function () {
    return ['foo' => 'bar'];
});
```
