@extends('layouts.app')

@section('content')
  <div class="card">
    <div class="card-header card-header-sticky d-flex justify-content-between align-items-center">
      <h4>Nova pré-reserva</h4>
      @if (isset($submission))
        <div class="d-flex align-items-center justify-content-start">
          <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm mr-2">
            <i class="fas fa-arrow-left"></i> Voltar
          </a>
          @php
            $aceito = $submission->data['aceito'] ?? 'not-avaliated';
            if (\Illuminate\Support\Str::beforeLast($aceito, '-') === 'accepted') {
                $sala = \Illuminate\Support\Str::afterLast($aceito, '-');
                $aceito = \Illuminate\Support\Str::beforeLast($aceito, '-');
            }
          @endphp

          @can('manager')
            <div class="dropdown mr-2">
              @if ($aceito === 'not-avaliated')
                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton"
                  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  Não Avaliado
                @elseif($aceito === 'accepted')
                  <button class="btn btn-success btn-sm dropdown-toggle" type="button" id="dropdownMenuButton"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Aceito - {{ ucfirst($sala) }}
                  @elseif($aceito === 'not-accepted')
                    <button class="btn btn-warning btn-sm dropdown-toggle" type="button" id="dropdownMenuButton"
                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Negado
              @endif
              </button>
              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <form action="{{ route('form.accept', ['id' => $submission->id]) }}" method="POST"
                  class="d-flex flex-column">
                  @csrf
                  @method('POST')
                  @if ($aceito !== 'accepted')
                    <button type="submit" name="accepted" value="accepted-g1" class="btn btn-success btn-sm m-1">
                      <i class="fas fa-thumbs-up"></i> G1
                    </button>
                    <button type="submit" name="accepted" value="accepted-g2" class="btn btn-success btn-sm m-1">
                      <i class="fas fa-thumbs-up"></i> G2
                    </button>
                    <button type="submit" name="accepted" value="accepted-g3" class="btn btn-success btn-sm m-1">
                      <i class="fas fa-thumbs-up"></i> G3
                    </button>
                  @endif
                  @if ($aceito === 'not-avaliated' || $aceito !== 'not-accepted')
                    <button type="submit" name="accepted" value="not-accepted" class="btn btn-warning btn-sm m-1">
                      <i class="fas fa-thumbs-down"></i>
                    </button>
                  @endif

                  @if ($aceito !== 'not-avaliated')
                    <button type="submit" name="accepted" value="not-avaliated" class="btn btn-secondary btn-sm m-1">
                      <i class="fas fa-calendar-minus"></i>
                    </button>
                  @endif
                </form>
              </div>
            </div>
          @else
            <div class="mr-2">
              @if ($aceito === 'not-avaliated')
                <span class="btn text-light btn-secondary btn-sm">Não avaliado</span>
              @elseif($aceito === 'accepted')
                <span class="btn text-light btn-success btn-sm">Aceito - {{ ucfirst($sala) }}</span>
              @elseif($aceito === 'not-accepted')
                <span class="btn btn-warning btn-sm">Negado</span>
              @endif
            </div>
          @endcan

          <form action="{{ route('form.delete', ['id' => $submission->id]) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm"
              onclick="return confirm('Tem certeza de que deseja excluir esta pré-reserva?')">
              <i class="fas fa-trash-alt"></i>
            </button>
          </form>
        </div>
      @endif
    </div>
  </div>
  <div class="card-body">

    {!! $formHtml !!}

  </div>
  </div>
@endsection
