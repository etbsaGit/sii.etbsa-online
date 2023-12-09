@component('mail::message')
@slot('header')
@component('mail::header', ['url' => $url])
{{-- Logo --}}
<img src="{{ asset('img/logo.png') }}" alt="Nuevo Logo">
{{-- Nombre de la aplicación --}}
{{ config('app.name') }}
@endcomponent
@endslot
# {{$subjectMessage . ' #' . $order->purchase_number}}

@component('mail::panel')
Estatus Actual: {{ $order->estatus->key }}.
Revisar la Orden de Compra. 
@endcomponent

{{-- @component('mail::table')
| Cant.       | UM         | Clv Prod. / Descripcion  | Precio | Subtotal |
| ----------- |:----------:| :----------------------- | ------:| --------:|
@foreach ($order->products as $product)
| {{ $product['qty'] }} | {{ $product['unit']['clave'] . '-' . $product['unit']['name'] }} | {{ $product['claveProduct']['c_ClaveProdServ'] }}({{ $product['claveProduct']['Descripción'] }}) <br> {{ $product['description'] }} |  {{ '$ ' . number_format($product['price'], 2, '.', ',') }} | {{ '$ ' . number_format($product['subtotal'], 2, '.', ',') }}|
@endforeach
|||  | <b>Subtotal</b>  |  {{ '$ ' . number_format($order['subtotal'], 2, '.', ',') }} |
|||  | <b>IVA</b>  |  {{ '$ ' . number_format($order['tax'], 2, '.', ',') }} |
|||  | <b>Total</b>  |  {{ '$ ' . number_format($order['total'], 2, '.', ',') }} |
@endcomponent --}}

@component('mail::button', ['url' => $url ])
Ver Orden de Compra
@endcomponent
Gracias,<br>
{{ config('app.name') }}
@endcomponent
