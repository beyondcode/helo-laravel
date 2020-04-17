@component('helo::layout.layout')
{{-- Header --}}
@slot('header')
@component('helo::layout.header', ['url' => 'https://usehelo.com'])
HELO
@endcomponent
@endslot

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('helo::layout.subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('helo::layout.footer')
© {{ date('Y') }} HELO. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent
