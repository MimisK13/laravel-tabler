@extends('layouts.tabler')

@pushonce('page-styles')
    {{--- ---}}
@endpushonce

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        {{ __('Empty page') }}
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <x-alert/>

            {{--- ---}}
        </div>
    </div>
@endsection

@pushonce('page-scripts')
    {{--- ---}}
@endpushonce
