@if(Session::has('toastr'))
    @php
        $toastr     = Session::pull('toastr');
        $toastr     = $toastr instanceof \Illuminate\Support\MessageBag ? $toastr->toArray() : $toastr;
        $type       = \Illuminate\Support\Arr::wrap(\Illuminate\Support\Arr::get($toastr, 'type'))[0] ?? 'success';
        $message    = \Illuminate\Support\Arr::wrap(\Illuminate\Support\Arr::get($toastr, 'message'))[0] ?? '';
        $options    = json_encode(\Illuminate\Support\Arr::get($toastr, 'options', []));
    @endphp
    <script>
        $(function () {
            toastr.{{$type}}('{!!  $message  !!}', null, {!! $options !!});
        });
    </script>
@endif
