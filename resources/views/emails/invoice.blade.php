@component('mail::message')
# Detalle de factura para {{ $invoice["client_name"]}}

@component('mail::table')
| Nombre       | Descripcion         | Total  |
| ------------- |-------------| --------:|
| {{ $invoice["invoice_name"]}}     | {{ $invoice["invoice_description"]}}      | ${{ $invoice["invoice_total"]}}      |
@endcomponent
 

@component('mail::button', ['url' => $invoice["url_payment"], 'color' => 'success'])
Ir a pagar
@endcomponent

Gracias por su compra,<br>
{{ config('app.name') }}
@endcomponent
