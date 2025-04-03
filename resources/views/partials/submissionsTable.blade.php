<table class="table datatable-simples responsive table-stripped table-sm table-bordered table-hover mb-3 dt-fixed-header">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nome do professor</th>
      <th>Disciplina</th>
      <th>Horário</th>
      <th>Dias da semana</th>
      <th>Software utilizado</th>
      <th>Quantidade de alunos</th>
      <th>Semestre</th>
      <th>Criado em</th>
      <th>Status</th>
      <th>Opções</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($submissions as $submission)
      <tr>
        <td><a href="{{ route('form.show', ['id' => $submission->id]) }}">{{ $submission->id }}</a></td>
        <td>{{ \Uspdev\Replicado\Pessoa::nomeCompleto($submission->data['professor']) }}</td>
        <td>{{ $submission->data['disciplina'] }} -
          {{ \Uspdev\Replicado\Graduacao::nomeDisciplina($submission->data['disciplina']) }}</td>
        <td>{{ $submission->data['hora_inicio'] }} - {{ $submission->data['hora_fim'] }}</td>
        <td>
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
          @foreach ($submission->data['dia_semana'] as $diaDaSemana)
            {{ $diasDaSemana[strtolower($diaDaSemana)] ?? ucfirst($diaDaSemana) }}
            {{ !$loop->last ? ', ' : '' }}
          @endforeach
        </td>
        <td>
          @if (isset($submission->data['software_link']))
            <a href="{{ $submission->data['software_link'] }}" target="_blank">{{ $submission->data['software_utilizado'] }}</a>
          @else
            {{ $submission->data['software_utilizado'] }}
          @endif
        </td>
        <td>{{ $submission->data['quantidade_aluno'] }}</td>
        <td>{{ $submission->data['semestre'] }}</td>
        <td>{{ \Carbon\Carbon::parse($submission->created_at)->format('d/m/Y H:i') }}</td>
        <td>
          <div>
            @php
              $aceito = $submission->data['aceito'] ?? 'not-avaliated';
              if(\Illuminate\Support\Str::beforeLast($aceito, '-') === 'accepted'){
                $sala = \Illuminate\Support\Str::afterLast($aceito, '-');
                $aceito = \Illuminate\Support\Str::beforeLast($aceito, '-');
              }
            @endphp

            @if ($aceito === 'not-avaliated')
              <span class="badge text-light bg-secondary">Não avaliado</span>
            @elseif($aceito === 'accepted')
              <span class="badge text-light bg-success">Aceito - {{ ucfirst($sala) }}</span>
            @elseif($aceito === 'not-accepted')
              <span class="badge bg-warning">Negado</span>
            @endif
          </div>

          @can('manager')
            <div class="d-flex mt-2">
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
                  <button type="submit" name="accepted" value="not-avaliated" class="btn btn-secondary btn-sm">
                    <i class="fas fa-calendar-minus"></i>
                  </button>
                @endif
              </form>
            </div>
          @endcan
        </td>
        <td>
          <div class="d-flex justify-content-start align-items-center">
            <form action="{{ route('form.delete', ['id' => $submission->id]) }}" method="POST" class="mr-2 d-flex">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm"
                onclick="return confirm('Tem certeza de que deseja excluir esta pré-reserva?')">
                <i class="fas fa-trash-alt"></i>
              </button>
            </form>

            <a href="{{ route('form.edit', ['id' => $submission->id]) }}" class="btn btn-primary btn-sm d-flex">
              <i class="fas fa-pencil-alt"></i>
            </a>
          </div>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>