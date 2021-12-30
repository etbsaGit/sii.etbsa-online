<table>
    <thead>
        <tr>
            <th>Nombre Cliente</th>
            <th>Sucursal</th>
            <th>Depto Cargo</th>
            <th>telefono</th>
            <th>Total de Unidades</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($group as $item)
            <tr>
                <td>{{ $item->name ?? '' }}</td>
                <td>{{ $item->agency ?? '' }}</td>
                <td>{{ $item->department ?? '' }}</td>
                <td>{{ $item->phone ?? '' }}</td>
                <td>{{ $item->gps_count ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
