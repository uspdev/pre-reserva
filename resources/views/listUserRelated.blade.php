@extends('layouts.app')

@section('content')
  <h2>Pré-reservas em meu nome</h2>
  <p>Aqui estão listadas as pré-reservas feitas por você ou por outro servidor ou docente, para o seu uso</p>
  @include('partials.submissionsTable')
@endsection
