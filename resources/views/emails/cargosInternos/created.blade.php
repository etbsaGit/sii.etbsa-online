@component('mail::message')
    # {{ $cargo_interno->id }} Cargo Interno
    #OT {{ $cargo_interno->ot }}
    Your Cargo Interno has been shipped!

    {{-- @component('mail::button', ['url' => '/'])
        View Cargo Interno
    @endcomponent --}}

    Thanks,
    {{ config('app.name') }}
@endcomponent
