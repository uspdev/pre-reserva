@extends('layouts.app')

@section('content')
  <h2>Minhas pré-reservas</h2>
  <h5>Feitas por {{ $user->name }}</h5>
 
  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur porttitor eu enim lacinia commodo. Aenean ut
    nisl aliquam, dignissim lorem ut, convallis justo. Praesent sit amet semper orci.</p>
  @include('partials.submissionsTable')
@endsection
