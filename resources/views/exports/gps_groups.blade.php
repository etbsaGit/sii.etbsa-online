<table>
    <thead>
        <tr>
            <th>Nombre Cliente</th>
            <th>Sucursal</th>
            <th>Depto Cargo</th>
            <th>telefono</th>
        </tr>
    </thead>
    <tbody>
        @foreach($group as $item)
        <tr>
            <td>{{ $item->name ?? '' }}</td>
            <td>{{ $item->agency ?? ''}}</td>
            <td>{{ $item->department ?? ''}}</td>
            <td>{{ $item->phone ?? ''}}</td>
        </tr>
        @endforeach
    </tbody>
</table>