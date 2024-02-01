<table>
    <thead>
        <tr>
            <th>SIM</th>
            <th># IMEI</th>
            <th>COSTO</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chip as $item)
        <tr>
            <td>{{ $item->sim ?? '' }}</td>
            <td>{{ '# '.$item->imei ?? ''}}</td>
            <td>{{ $item->costo ?? ''}}</td>
        </tr>
        @endforeach
    </tbody>
</table>