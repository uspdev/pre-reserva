<table class="table datatable-simples responsive table-stripped table-sm table-bordered table-hover mb-3 dt-fixed-header">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nome do professor</th>
      <th>Disciplina</th>
      <th>Horário</th>
      <th>Dias da semana</th>
      <th>Software utilizado</th>
      <th>Qt alunos</th>
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
            <a href="{{ $submission->data['software_link'] }}"
              target="_blank">{{ $submission->data['software_utilizado'] }}</a>
          @else
            {{ $submission->data['software_utilizado'] }}
          @endif
        </td>
        <td>{{ $submission->data['quantidade_aluno'] }}</td>
        <td>{{ $submission->data['semestre'] }}</td>
        <td>{{ \Carbon\Carbon::parse($submission->created_at)->format('d/m/Y H:i') }}</td>
        <td>
          @php
            $aceito = $submission->data['aceito'] ?? 'not-avaliated';
            if (\Illuminate\Support\Str::beforeLast($aceito, '-') === 'accepted') {
                $sala = \Illuminate\Support\Str::afterLast($aceito, '-');
                $aceito = \Illuminate\Support\Str::beforeLast($aceito, '-');
            }
          @endphp

          @can('manager')
            <div class="dropdown mt-2">
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
            <div>
              @if ($aceito === 'not-avaliated')
                <span class="badge text-light bg-secondary p-2">Não avaliado</span>
              @elseif($aceito === 'accepted')
                <span class="badge text-light bg-success p-2">Aceito - {{ ucfirst($sala) }}</span>
              @elseif($aceito === 'not-accepted')
                <span class="badge bg-warning p-2">Negado</span>
              @endif
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