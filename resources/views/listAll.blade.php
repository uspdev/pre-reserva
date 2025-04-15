@extends('layouts.app')

@section('content')
  <h2>Lista de pré-reservas</h2>
  <p>Aqui estão listadas todas as pré-reservas feitas no sistema</p>
  @include('partials.submissionsTable')
@endsection
