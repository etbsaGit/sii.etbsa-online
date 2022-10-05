<table>
    <thead>
        <tr>
            <th>Alias</th>
            <th>Razon Social</th>
            <th>RFC</th>
            <th>Telefono</th>
            <th>Email</th>
            <th>Domicilio</th>
            <th>Giro</th>
            <th>Observacion</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($supplier as $item)
            <tr>
                <td>{{ $item->alias ?? ' ' }}</td>
                <td>{{ $item->business_name }}</td>
                <td>{{ $item->rfc ?? ' ' }}</td>
                <td>{{ $item->phone ?? ' ' }}</td>
                <td>{{ $item->email ?? ' ' }}</td>
                <td>{{ $item->address ?? ' ' }}</td>
                <td>{{ implode(', ', $item->giro ?? []) }}</td>
                <td>{{ $item->observation ?? ' ' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
