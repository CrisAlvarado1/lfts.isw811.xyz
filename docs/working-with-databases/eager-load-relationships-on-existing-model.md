[< Volver al índice](/docs/readme.md)

# Eager Load Relationships on an Existing Model

En este episodio, aprenderemos a utilizar la carga de forma predeterminada de relaciones (Eager Load Relationships) en un modelo existente y exploraremos sus ventajas y desventajas, para poder descubrir cuándo realmente es útil o no deseado.

Para asegurarnos de que no haya problemas de carga al acceder a los posts por categoría, se añadirán más posts con la categoría `quo`:

```php
App\Models\Post::factory(10)->create(['category_id' => 1]);
```

Ahora, al acceder a los posts por la categoría `quo` y comprobar las cargas, podremos ver que se presenta nuevamente el problema N+1.

## Solucionar el problema

Para solucionar el problema N+1, podemos ajustar de la siguiente manera las rutas problemáticas del archivo `route.web`:

```php
Route::get('categories/{category:slug}', function (Category $category) {
    return view('posts', [
        'posts' => $category->posts->load(['category', 'author'])
    ]);
});

Route::get('authors/{author:username}', function (User $author) {
    return view('posts', [
        'posts' => $author->posts->load(['category', 'author'])
    ]);
});
```

En este caso, añadimos el método `load()` a las rutas que trabajaban con el modelo directamente. Estos cambios aseguran que las relaciones `category` y `author` se carguen de una manera moderada, solucionando el problema N+1.

## Cargar de forma predeterminada

En caso de que quisiéramos que al cargar los posts, siempre incluyan su autor y categoría, en el archivo `/app/Models/Post.php` añadimos la siguiente propiedad:

```php
protected $with = ['category', 'author'];
```

Después, modificamos el archivo de rutas `routes.web`, el cual resultaría así:

```php
Route::get('/', function () {
    return view('posts', [
        'posts' => Post::latest()->get()
    ]);
});


Route::get('posts/{post:slug}', function (Post $post) {
    return view('post', [
        'post' => $post
    ]);
});

Route::get('categories/{category:slug}', function (Category $category) {
    return view('posts', [
        'posts' => $category->posts
    ]);
});

Route::get('authors/{author:username}', function (User $author) {
    return view('posts', [
        'posts' => $author->posts
    ]);
});
```

De cierta manera, la estructura de las rutas se simplifica un poco, evitando la necesidad de indicar las relaciones que deben cargarse junto a los posts.

### Ventajas y desventajas

La carga predeterminada elimina el problema N+1, pero hay ocasiones en que su funcionalidad no es tan deseada. Como cuando no es necesario obtener todas las relaciones. Por ejemplo, en casos como el anterior y que tengamos la carga predeterminado activa, podemos utilizar el siguiente método:

```php
App\Models\Post::without(['author', 'category'])->first();
```

Otra opción es no utilizar la propiedad `with` y utilizar métodos auxiliares. Sin embargo, en nuestro caso, hasta el momento este tipo de carga es suficiente.
