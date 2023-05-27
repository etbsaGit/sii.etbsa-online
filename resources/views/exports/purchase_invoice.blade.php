<table>
    <thead>
        <tr>
            <th>Factura UUID</th>
            <th>Folio / Serie</th>
            <th>Importe</th>
            <th>Cargos</th>
            <th>Fecha Factura</th>
            <th>Fecha para Pagar</th>
            <th>Proveedor</th>
            <th>Proveedor RFC</th>
            <th>Concepto</th>
            <th>Articulos</th>
            <th>Motivo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoice as $item)
            @php
                $cargo = json_encode($item->invoiceable->charges);
                $products = json_encode($item->invoiceable->products);
                $cargos = json_decode($cargo);
                $partidas = json_decode($products);
            @endphp
            <tr>
                <td>{{ $item->folio_fiscal }}</td>
                <td>{{ $item->folio . $item->serie }}</td>
                <td>{{ '$' . $item->amount . ' ' . $item->currency }}</td>
                <td>
                    @foreach ($cargos as $cargo)
                        {{ $cargo->agency->title . ' - ' . $cargo->department->title . ' - ' . $cargo->percent . '%' }}
                        <br />
                    @endforeach
                </td>
                <td>{{ $item->invoice_date }}</td>
                <td>{{ $item->date_to_pay }}</td>
                <td>{{ $item->invoiceable->supplier->business_name }}</td>
                <td>{{ $item->invoiceable->supplier->rfc }}</td>
                <td>{{ $item->invoiceable->purchase_concept->name }}</td>
                <td>
                    @foreach ($partidas as $partida)
                        {{ $partida->description . ' - ' . $partida->group->name }}
                        <br />
                    @endforeach
                </td>
                <td>
                    {{ $item->invoiceable->observation }}
                </td>
                {{-- <td>{{ $cargo->department->title }}</td>
                    <td>{{ $cargo->percent }}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
