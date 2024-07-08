[< Volver al índice](/docs/readme.md)

# Extract Form-Specific Blade Components

En este capítulo, nos dedicaremos a limpiar el código del formulario de publicaciones, extrayendo fragmentos de código de los formularios en componentes de Blade, de esta manera tendremos una serie de piezas reutilizables y aplicaremos un poco la filosofía de DRY.

## 1. Componentes de Blade

### Crear directorio `form`

Con el fin de ordenar un poco los componentes. Añadiremos el directorio `/resources/views/components/form` en donde guardaremos todos los componentes relacionados a un formulario.

### Crear las piezas más pequeñas:

Con la creación de las siguientes tres piezas, evitaremos repetir una cantidad considerable de código en los componentes que crearemos en la [siguiente sección](#crear-los-componentes-de-formulario).

#### A. Componente para manejar la directiva `@error` de cada entrada, área de texto y dropdown

Para evitar la repetición de la directiva `@error` en cada componente de formulario, crearemos `/resources/views/components/form/error.blade.php`. Este nuevo componente resultará de la siguiente manera:

```html
@props(['name']) 

@error($name)
    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
@enderror
```

#### B. Componente para el label de cada entrada, área de texto y dropdown

Por la misma razón que en el punto anterior, crearemos el componente `/resources/views/components/form/label.blade.php`:

```html
@props(['name'])

<label
    class="block mb-2 uppercase font-bold text-xs text-gray-700"
    for="{{ $name }}"
>
    {{ ucwords($name) }}
</label>
```

De esta forma, podremos mostrar el título únicamente enviando el nombre de la entrada, área de texto o dropdown deseado.

#### C. Componente para el contenedor de cada entrada, área de texto y dropdown

Para finalizar con los componentes pequeños, crearemos el componente `/resources/views/components/form/field.blade.php`:

```html
<div class="mt-6">
    {{ $slot }}
</div>
```

Este nos permitirá eliminar la pequeña repetición de código del contenedor y agregarle un margen superior. Además, en la parte del `$slot` podrá recibir todo el código que deseemos dentro de este.

### Crear los componentes de formulario:

Ahora, crearemos los componentes de formulario de las etiquetas HTML `input` y `textarea` utilizando las piezas pequeñas o componentes creados [anteriormente](#crear-las-piezas-más-pequeñas). Además, moveremos y adaptaremos el componente `/resources/views/components/submit-button.blade.php` al directorio creado en la sección de [crear directorio form](#crear-directorio-form).

#### A. Input de formulario

Crear archivo de componente `/resources/views/components/form/input.blade.php`:

```html
@props(['name', 'type' => 'text'])

<x-form.field>
    <x-form.label name="{{ $name }}" />

    <input
        class="border border-gray-400 p-2 w-full"
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name) }}"
        required
    />

    <x-form.error name="{{ $name }}" />
</x-form.field>
```

De esta forma, podremos reutilizar una entrada de formulario de tipo texto o del tipo que sea enviado; si no se envía ningún tipo por defecto, será de texto. Además, utilizamos los componentes `x-form.field` para añadir y envolver todo con un `div` con un margen superior, `x-form.label` para agregar un label para la entrada y `x-form.error` para el manejo de errores que vengan del lado del servidor.

#### B. Textarea de formulario

Crear archivo de componente `/resources/views/components/form/textarea.blade.php`:

```html
@props(['name'])

<x-form.field>
    <x-form.label name="{{ $name }}" />

    <textarea
        class="border border-gray-400 p-2 w-full"
        name="{{ $name }}"
        id="{{ $name }}"
        required
    >
        {{ old($name) }}</textarea
    >

    <x-form.error name="{{ $name }}" />
</x-form.field>
```

Con esta implementación, podremos reutilizar este código para generar un `textarea` para manejar distintos valores. Además, utilizamos los componentes `x-form.field` para añadir y envolver todo con un `div` con un margen superior, `x-form.label` para agregar un label para el área de texto y `x-form.error` para el manejo de errores que vengan del lado del servidor.

#### C. Mover `submit-button.blade.php` a `/resources/views/components/form`

Moveremos el archivo de componente `/resources/views/components/submit-button.blade.php` a `/resources/views/components/form`. Una vez realizado esto, lo renombraremos a `button.blade.php` y le envolveremos con la pieza pequeña "field" para añadirle un margen superior interesante:

```html
<x-form.field>
    <button
        type="submit"
        class="bg-blue-500 text-white uppercase font-semibold text-xs py-2 px-10 rounded-2xl hover:bg-blue-600"
    >
        {{ $slot }}
    </button>
</x-form.field>
```

Una vez realizado esto, debemos asegurarnos de cambiar la antigua referencia a este componente en `/resources/views/posts/_add-comment-form.blade.php` y actualizarla.

## 2 Actualizar formulario de `/resources/views/posts/create.blade.php`

Por último, en el archivo de vista `posts/create` en el formulario implementaremos los componentes creados:

```html
<form method="POST" action="/admin/posts" enctype="multipart/form-data">
    @csrf

    <x-form.input name="title" />
    <x-form.input name="slug" />
    <x-form.input name="thumbnail" type="file" />
    <x-form.textarea name="excerpt" />
    <x-form.textarea name="body" />

    <x-form.field>
        <x-form.label name="category" />

        <select name="category_id" id="category_id">
            @foreach (\App\Models\Category::all() as $category)
                <option
                    value="{{ $category->id }}"
                    {{ old('category_id') == $category->id ? 'selected' : '' }}
                >{{ ucwords($category->name) }}</option>
            @endforeach
        </select>

        <x-form.error name="category" />
    </x-form.field>

    <x-form.button>Publish</x-form.button>
</form>
```

**Puntos interesantes de la implementación:**

-   Utilizamos `x-form.input` para los valores `title` y `slug` sin especificar el tipo. Por ende, serán de tipo texto.

-   Utilizamos `x-form.input` para el valor `thumbnail` especificando el tipo. Por ende, será del tipo dado (archivo).

-   Utilizamos `x-form.textarea` para los valores `excerpt` y `body` para generar las áreas de texto.

-   En el caso del `select`, no realizamos un componente ya que no es algo que se esté duplicando. Sin embargo, implementamos el `x-form.field` y `x-form.label`, para eliminar las duplicaciones de código en el contenedor que lo envolvía y con en el `label`.

## Resultado final

En este episodio, nos dedicamos a limpiar el código del formulario ubicado en `/resources/views/posts/create.blade.php`. Eliminando las repeticiones de código e implementando el principio DRY indirectamente. Es destacable que el resultado visual es exactamente el mismo, pero internamente se aplicaron una serie de buenas prácticas y una limpieza muy buena.
