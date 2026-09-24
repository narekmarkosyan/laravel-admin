@php
    $normaliseAlert = function ($value) {
        $value = $value instanceof \Illuminate\Support\MessageBag ? $value->toArray() : $value;

        return [
            'title' => \Illuminate\Support\Arr::wrap(\Illuminate\Support\Arr::get($value, 'title'))[0] ?? '',
            'message' => \Illuminate\Support\Arr::wrap(\Illuminate\Support\Arr::get($value, 'message'))[0] ?? '',
        ];
    };
@endphp

@if($error = session()->get('error'))
    @php($error = $normaliseAlert($error))
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-ban"></i>{{ $error['title'] }}</h4>
        <p>{!! $error['message'] !!}</p>
    </div>
@elseif (($errors = session()->get('errors')) instanceof \Illuminate\Support\ViewErrorBag)
    @if ($errors->hasBag('error'))
      <div class="alert alert-danger alert-dismissable">

        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        @foreach($errors->getBag("error")->toArray() as $message)
            <p>{!!  \Illuminate\Support\Arr::get($message, 0) !!}</p>
        @endforeach
      </div>
    @endif
@endif

@if($success = session()->get('success'))
    @php($success = $normaliseAlert($success))
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-check"></i>{{ $success['title'] }}</h4>
        <p>{!! $success['message'] !!}</p>
    </div>
@endif

@if($info = session()->get('info'))
    @php($info = $normaliseAlert($info))
    <div class="alert alert-info alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-info"></i>{{ $info['title'] }}</h4>
        <p>{!! $info['message'] !!}</p>
    </div>
@endif

@if($warning = session()->get('warning'))
    @php($warning = $normaliseAlert($warning))
    <div class="alert alert-warning alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4><i class="icon fa fa-warning"></i>{{ $warning['title'] }}</h4>
        <p>{!! $warning['message'] !!}</p>
    </div>
@endif
