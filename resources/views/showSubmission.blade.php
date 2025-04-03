@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="card">
      <div class="card-header card-header-sticky d-flex justify-content-between align-items-center">
        <h4>Detalhes da Pré-Reserva</h4>
        @if (isset($submission))
          @php
            $aceito = $submission->data['aceito'] ?? 'not-avaliated';
            if (\Illuminate\Support\Str::beforeLast($aceito, '-') === 'accepted') {
                $sala = \Illuminate\Support\Str::afterLast($aceito, '-');
                $aceito = \Illuminate\Support\Str::beforeLast($aceito, '-');
            }
          @endphp

          <div class="d-flex">
            <a href="{{ route('list-user') }}" class="btn btn-secondary btn-sm mr-2">
              <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('form.edit', ['id' => $submission->id]) }}" class="btn btn-primary btn-sm mr-2">
              <i class="bi bi-pencil"></i> Editar
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
        <label><strong>Status:</strong></label>

        @if ($aceito === 'not-avaliated')
          <span class="badge text-light bg-secondary">Não avaliado</span>
        @elseif($aceito === 'accepted')
          <span class="badge text-light bg-success">Aceito - {{ ucfirst($sala) }}</span>
        @elseif($aceito === 'not-accepted')
          <span class="badge bg-warning">Negado</span>
        @endif

        <div class="row">
          <div class="col-md-6 mb-3">
            <label><strong>Professor:</strong></label>
            <p class="font-weight-normal">
              {{ \Uspdev\Replicado\Pessoa::nomeCompleto($submission->data['professor'] ?? 'Não informado') }}</p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Disciplina:</strong></label>
            <p class="font-weight-normal">{{ $submission->data['disciplina'] ?? 'Não informado' }} -
              {{ \Uspdev\Replicado\Graduacao::nomeDisciplina($submission->data['disciplina'] ?? '') }}</p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Horário de Início:</strong></label>
            <p class="font-weight-normal">
              {{ \Carbon\Carbon::parse($submission->data['hora_inicio'] ?? '00:00')->format('H:i') }}</p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Horário de Término:</strong></label>
            <p class="font-weight-normal">
              {{ \Carbon\Carbon::parse($submission->data['hora_fim'] ?? '00:00')->format('H:i') }}</p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Dia da Semana:</strong></label>
            <p class="font-weight-normal">
              @php
                $diasDaSemana = [
                    'segunda' => 'Segunda-feira',
                    'terca' => 'Terça-feira',
                    'quarta' => 'Quarta-feira',
                    'quinta' => 'Quinta-feira',
                    'sexta' => 'Sexta-feira',
                    'sabado' => 'Sábado',
                    'domingo' => 'Domingo',
                ];
              @endphp
              @foreach ($submission->data['dia_semana'] as $dia)
                {{ $diasDaSemana[strtolower($dia)] ?? ucfirst($dia) }}
                {{ !$loop->last ? ', ' : '' }}
              @endforeach
            </p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Semestre:</strong></label>
            <p class="font-weight-normal">{{ $submission->data['semestre'] ?? 'Não informado' }}</p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Software Utilizado:</strong></label>
            <p class="font-weight-normal">{{ $submission->data['software_utilizado'] ?? 'Não informado' }}</p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Criado em:</strong></label>
            <p class="font-weight-normal">{{ \Carbon\Carbon::parse($submission->created_at)->format('d/m/Y H:i') }}</p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Link para o Software:</strong></label>
            <p class="font-weight-normal">
              @if (isset($submission->data['software_link']))
                {{ $submission->data['software_link'] }}
              @else
                {{ 'Não informado' }}
              @endif
            </p>
          </div>

          <div class="col-md-6 mb-3">
            <label><strong>Quantidade de Alunos:</strong></label>
            <p class="font-weight-normal">{{ $submission->data['quantidade_aluno'] ?? 'Não informado' }}</p>
          </div>

          <div class="col-md-12 mb-3">
            <label><strong>Observações:</strong></label>
            <p class="font-weight-normal">{{ $submission->data['obs'] ?? 'Nenhuma observação' }}</p>
          </div>

        </div>
      </div>
    </div>
  </div>
@endsection
