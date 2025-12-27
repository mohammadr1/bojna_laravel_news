@extends('customer.layouts.master-one-col')

@section('head-tag')
<link rel="stylesheet" href="{{ asset('assets/css/style-show.css') }}">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
{{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
<style type="text/tailwindcss">
    @theme {
        --color-clifford: #da373d;
      }
</style> --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

@endsection

@section('content')

<div class="custom-responsive-row col-12 row">

    <div class="col-md-8 rounded" style="max-width: 800px; height: auto;">

    {{ message }}

    </div>


    <!-- Sidebar -->
    @include('customer.news.sidebar')
</div>





@endsection


@section('scripts')

@endsection
