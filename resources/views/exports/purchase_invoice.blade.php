<table>
    <thead>
        <tr>
            <th>Factura UUID</th>
            <th>Folio / Serie</th>
            <th>Importe</th>
            <th>Fecha Factura</th>
            <th>Fecha para Pagar</th>
            <th>Proveedor</th>
            <th>Proveedor RFC</th>
            <th>Motivo</th>
            <th>Concepto</th>
            <th>Cargos</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoice as $item)
            @php
                $cargo = json_encode($item->invoiceable->charges);
                $cargod = json_decode($cargo);
            @endphp
            <tr>
                <td>{{ $item->folio_fiscal }}</td>
                <td>{{ $item->folio . $item->serie }}</td>
                <td>{{ '$' . $item->amount . ' ' . $item->currency }}</td>
                <td>{{ $item->invoice_date }}</td>
                <td>{{ $item->date_to_pay }}</td>
                <td>{{ $item->invoiceable->supplier->business_name }}</td>
                <td>{{ $item->invoiceable->supplier->rfc }}</td>
                <td>{{ $item->invoiceable->observation }}</td>
                <td>{{ $item->invoiceable->purchase_concept->name }}</td>
                <td>
                    @foreach ($cargod as $cargo)
                        {{ $cargo->agency->title . ' - ' . $cargo->department->title . ' - ' . $cargo->percent . '%' }}
                        <br />
                    @endforeach
                </td>
                {{-- <td>{{ $cargo->department->title }}</td>
                    <td>{{ $cargo->percent }}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
