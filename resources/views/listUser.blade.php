@extends('layouts.app')

@section('content')
  <h2>Minhas pré-reservas</h2>
  <h5>Feitas por {{ $user->name }}</h5>
  <p>Aqui estão listadas as pré-reservas feitas por você, para seu uso ou de outros</p>
  @include('partials.submissionsTable')
@endsection
