[< Volver al índice](/docs/readme.md)

# How to Extract a Dropdown Blade Component

En este episodio, nos dedicaremos a extraer el menú despegable en diferentes componentes reutilizables, con esto indirectamente aislaremos todo el código específico de Alpine.js en un componente.

## Crear componentes y realizar cambios necesarios

### Componente `dropdown.blade.php`

Crearemos un nuevo archivo `/resources/views/components/dropdown.blade.php` para almacenar el nuevo componente `x-dropdown`. Por lo que moveremos el contenido HTML y Alpine.js a este nuevo archivo, y elaboraremos algunos ajustes necesarios, dando como resultado lo siguiente:

```html
@props(['trigger'])

<div x-data="{show: false}" @click.away="show = false">
    <!-- Trigger -->
    <div @click="show = ! show">{{ $trigger }}</div>

    <!-- Links -->
    <div
        x-show="show"
        class="py-2 absolute bg-gray-100 mt-2 rounded-xl w-full z-50"
        style="display: none;"
    >
        {{ $slot }}
    </div>
</div>
```

Conmo resultado del componente, podemos definir su área de `trigger` (en este caso el botón) y el `slot` (contenido del menú desplegable).

### Componente `dropdown-item.blade.php`

Crearemos el componente que represente cada ítem (enlace) del menú desplegable, por el motivo de no repetir constantemente las clases CSS. Por lo que se crea el archivo de componente `/resources/views/components/dropdown-item.blade.php` y quedaría de la siguiente manera:

```html
@props(['active' => false])

@php
    $classes = 'block text-left px-3 text-sm leading-6 hover:bg-blue-500 focus:bg-blue-500 hover:text-white focus:text-white';

    if ($active) $classes .= ' bg-blue-500 text-white';
@endphp

<a {{ $attributes(['class' => $classes]) }}>
    {{ $slot }}
</a>
```

### Asignar nombre a los endpoints

Para una verificación que se hará en unos pasos a continuación, es de interés darle un nombre a las siguientes rutas en el archivo `web.php`:

```php
Route::get('/', function () {
    return view('posts', [
        'posts' => Post::latest()->get(),
        'categories' => Category::all()
    ]);
})->name('home');


Route::get('categories/{category:slug}', function (Category $category) {
    return view('posts', [
        'posts' => $category->posts,
        'currentCategory' => $category,
        'categories' => Category::all()
    ]);
})->name('category');
```

### Componente `icon.blade.php`

Con este componente podremos almacenar todos esos `SVG`, los cuales en ciertas ocasiones son realmente incómodos o feos. Básicamente, se solicita el icono por su nombre y, si el nombre existe, se retornará.

```html
@props(['name'])

@if ($name == 'down-arrow')
    <svg {{ $attributes(['class' => 'transform -rotate-90']) }} width="22" height="22" viewBox="0 0 22 22">
        <g fill="none" fill-rule="evenodd">
            <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
            </path>
            <path fill="#222" d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
        </g>
    </svg>
@endif
```

## Refactorizar el archivo `_post-header.blade.php`

A lo largo de este episodio hemos ido modificando y limpiando el código mediante la creación de componentes, y todo esto se ha ido adaptando en el siguiente archivo `_post-header`. Este quedó de la siguiente manera:

```html
<header class="max-w-xl mx-auto mt-20 text-center">
    <h1 class="text-4xl">
        Latest <span class="text-blue-500">Laravel From Scratch</span> News
    </h1>

    <h2 class="inline-flex mt-2">By Lary Laracore <img src="/images/lary-head.svg" alt="Head of Lary the mascot"></h2>

    <p class="text-sm mt-14">
        Another year. Another update. We're refreshing the popular Laravel series with new content.
        I'm going to keep you guys up to speed with what's going on!
    </p>

    <div class="space-y-2 lg:space-y-0 lg:space-x-4 mt-8">
        <!-- Category: -->
        <div class="relative lg:inline-flex bg-gray-100 rounded-xl">
            <x-dropdown>
                <x-slot name="trigger">
                    <button class="py-2 pl-3 pr-9 text-sm font-semibold w-32 text-left flex lg:inline-flex">
                        {{ isset($currentCategory) ? ucwords($currentCategory->name) :'Categories' }}
                        <x-icon name="down-arrow" class="absolute pointer-events-none" style="right: 12px;" />
                    </button>
                </x-slot>
                <x-dropdown-item href="/" :active="request()->routeIs('home')">All</x-dropdown-item>
                @foreach ($categories as $category)
                <x-dropdown-item href="/categories/{{ $category->slug }}" :active='request()->is("categories/{$category->slug}") '>
                    {{ ucwords( $category->name )}}
                </x-dropdown-item>
                @endforeach
            </x-dropdown>
        </div>

        <!-- Other Filters -->
        <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl">

            <select class="flex-1 appearance-none bg-transparent py-2 pl-3 pr-9 text-sm font-semibold">
                <option value="category" disabled selected>Other Filters
                </option>
                <option value="foo">Foo
                </option>
                <option value="bar">Bar
                </option>
            </select>

            <svg class="transform -rotate-90 absolute pointer-events-none" style="right: 12px;" width="22" height="22" viewBox="0 0 22 22">
                <g fill="none" fill-rule="evenodd">
                    <path stroke="#000" stroke-opacity=".012" stroke-width=".5" d="M21 1v20.16H.84V1z">
                    </path>
                    <path fill="#222" d="M13.854 7.224l-3.847 3.856 3.847 3.856-1.184 1.184-5.04-5.04 5.04-5.04z"></path>
                </g>
            </svg>
        </div>

        <!-- Search -->
        <div class="relative flex lg:inline-flex items-center bg-gray-100 rounded-xl px-3 py-2">
            <form method="GET" action="#">
                <input type="text" name="search" placeholder="Find something" class="bg-transparent placeholder-black font-semibold text-sm">
            </form>
        </div>
    </div>
</header>
```

Debo recalcar que los cambios elaborados fueron en la sección `Category` y donde ahora podemos encontrar la verificación por medio del nombre del endpoint `home`. Además, podemos visualizar los diferentes componentes aplicados, funcionando correctamente.