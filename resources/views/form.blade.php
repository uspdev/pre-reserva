@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="card">
      <div class="card-header card-header-sticky d-flex justify-content-between align-items-center">
        <h4>Preencher formulário de pré-reserva</h4>
        @if (isset($submission))
          @php
            $aceito = $submission->data['aceito'] ?? 'not-avaliated';
            if(\Illuminate\Support\Str::beforeLast($aceito, '-') === 'accepted'){
              $sala = \Illuminate\Support\Str::afterLast($aceito, '-');
              $aceito = \Illuminate\Support\Str::beforeLast($aceito, '-');
            }
          @endphp
          <div class="d-flex">

            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm mr-2">
              <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @can('manager')
              <form action="{{ route('form.accept', ['id' => $submission->id]) }}" method="POST" class="d-flex" id="form-{{ $submission->id }}">
                @csrf
                @method('POST')

                @if ($aceito !== 'accepted')
                  @include('partials.sala-select')
                @endif

                @if ($aceito === 'not-avaliated' || $aceito !== 'not-accepted')
                  <button type="submit" name="accepted" value="not-accepted" class="btn btn-warning btn-sm mr-2">
                    <i class="fas fa-thumbs-down"></i>
                  </button>
                @endif

                @if ($aceito !== 'not-avaliated')
                  <button type="submit" name="accepted" value="not-avaliated" class="btn btn-secondary btn-sm mr-2">
                    <i class="fas fa-calendar-minus"></i>
                  </button>
                @endif
              </form>
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
      <div class="card-body">
        @if (isset($aceito))
          <label><strong>Status:</strong></label>

          @if ($aceito === 'not-avaliated')
            <span class="badge text-light bg-secondary ml-1 mr-3">Não Avaliado</span>
          @elseif($aceito === 'accepted')
            <span class="badge text-light bg-success">Aceito - {{ ucfirst($sala) }}</span>
          @elseif($aceito === 'not-accepted')
            <span class="badge bg-warning ml-1 mr-3">Negado</span>
          @endif
        @endif

        {!! $formHtml !!}

      </div>
    </div>
  </div>
@endsection
