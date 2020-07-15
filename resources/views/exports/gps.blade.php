<table>
    <thead>
        <tr>
            <th>Nombre GPS</th>
            <th>SIM</th>
            <th>Cliente</th>
            <th>Costo</th>
            <th>Factura</th>
            <th>Importe</th>
            <th>Moneda</th>
            <th>Tipo Cambio</th>
            <th>Fecha Instalacion</th>
            <th>Fecha Vencimiento</th>
        </tr>
    </thead>
    <tbody>
        @foreach($gps as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->chip->sim ?? 'S/A' }}</td>
            <td>{{ $item->gpsGroup->name ?? 'S/A'}}</td>
            <td>{{ $item->chip->costo ?? 'S/A'}}</td>
            <td>{{ $item->invoice ?? 'S/A'}}</td>
            <td>{{ $item->amount ?? 0}}</td>
            <td>{{ $item->currency ?? 'MXN'}}</td>
            <td>{{ $item->exchange_rate ?? 1 }}</td>
            <td>{{ $item->installation_date }}</td>
            <td>{{ $item->renew_date }}</td>
        </tr>
        @endforeach
    </tbody>
</table>