<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Producto</th>
            <th>Modelo</th>
            <th>Categoria</th>
            <th>Description</th>
            <th>Precio Lista</th>
            <th>Modeneda</th>
            <th>Sucursal</th>
            <th>Usado</th>
            <th>Activo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $item)
            <tr>
                <td>{{ $item->sku }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->model->name }}</td>
                <td>{{ $item->category->name }}</td>
                <td>{{ $item->description }}</td>
                <td>$ {{ $item->price_1 }}</td>
                <td>{{ $item->is_dollar ? 'USD' : 'MXN' }}</td>
                <td>{{ $item->agency->title }}</td>
                <td>{{ $item->is_usado ? 'SI' : 'NO' }}</td>
                <td>{{ $item->active ? 'SI' : 'NO' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
