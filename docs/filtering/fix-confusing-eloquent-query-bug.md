[< Volver al índice](/docs/readme.md)

# Fix a Confusing Eloquent Query Bug

Para cerrar la sección de _filtering_, en este episodio solucionaremos un error de filtrado ubicado en el método `filter()` de nuestro modelo Eloquent `Post`. Por lo que, primero revisaremos la consulta SQL problemática y luego corregiremos el error de raíz.

## ¿Cuál es el problema?

Si utilizamos Clockwork para analizar la consulta SQL generada al seleccionar una categoría y luego realizar una búsqueda, nos proporcionará algo similar a lo siguiente:

```sql
SELECT
    *
FROM
    `posts`
WHERE
    (
        `title` LIKE '%Et%'
        OR `body` LIKE '%Et%'
        AND EXISTS(
            SELECT
                *
            FROM
                `categories`
            WHERE
                `posts`.`category_id` = `categories`.`id`
                AND `slug` = 'quis-dolorem-quia-eos'
        )
    )
ORDER BY
    `created_at` DESC;
```

Examinando esta consulta, podemos encontrar que el problema radica en que tanto la validación del texto de búsqueda como la validación de la categoría se encuentra en la misma cláusula `where`, lo cual no es lo que realmente queremos, ya que nos puede filtrar publicaciones de otras categorías distintas, pero que coincidan con el texto de búsqueda.

### Comportamiento deseado

El comportamiento deseado es manejar ambas validaciones en cláusulas separadas, lo cual se puede reflejar con la siguiente consulta SQL:

```sql
SELECT
    *
FROM
    `posts`
WHERE
    (
        `title` LIKE '%Et%'
        OR `body` LIKE '%Et%'
    )
    AND EXISTS(
        SELECT
            *
        FROM
            `categories`
        WHERE
            `posts`.`category_id` = `categories`.`id`
            AND `slug` = 'quis-dolorem-quia-eos'
    )
ORDER BY
    `created_at` DESC;
```

El problema es realmente algo muy sutil y que podría pasar por desapercibido totalmente.

## Solucionar problema de filtrado

Para corregir el problema, nos debemos ubicar en `/app/Models/Post.php` y modificar la sección de filtrado de `search` en el método `filter()`:

```php
public function scopeFilter($query, array $filters)
{
    // Solución:
    $query->when(
        $filters['search'] ?? false,
        fn ($query, $search) => $query
            ->where(fn ($query) => $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('body', 'like', '%' . $search . '%'))
    );

    // Otros filtrados...
    ...
}
```

Específicamente, lo que hicimos fue encapsular la lógica de búsqueda dentro de una nueva cláusula `where`, manejándola mediante una función flecha y en esta añadimos lo que ya teníamos anteriormente. Estos cambios asegurarán la separación de las dos validaciones y obtener el comportamiento esperado.

## Resultado final

Anteriormente, al combinar los filtros, podríamos ver que nos cargaba publicaciones de diferentes categorías. Ahora, podemos apreciar que el filtrado de los posts cumple con los criterios seleccionados.

![Resultado de la solución a la consulta del filtrado por búsqueda](images/resultado-solución-ep43-v40.png)

Con los cambios elaborados en este capítulo, se ha solucionado el problema de filtrado de búsqueda y categoría.
