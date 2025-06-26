@component('mail::message')
# {{ $title }}

{!! $message !!}

**Dados da pré-reserva:**

- **Autor:** {{ \Uspdev\Replicado\Pessoa::nomeCompleto($prereserva->key) ?? 'Não informado' }}
- **Professor:** {{ \Uspdev\Replicado\Pessoa::nomeCompleto($prereserva->data['professor']) ?? 'Não informado' }}
- **Disciplina:** {{ $prereserva->data['disciplina'] ?? '-' }}
- **Horário de início:** {{ $prereserva->data['hora_inicio'] ?? '-' }}
- **Horário de término:** {{ $prereserva->data['hora_fim'] ?? '-' }}
- **Dia(s) da semana:** 
@if(isset($prereserva->data['dia_semana']) && is_array($prereserva->data['dia_semana']))
    {{ implode(', ', $prereserva->data['dia_semana']) }}
@else
    {{ $prereserva->data['dia_semana'] ?? '-' }}
@endif
- **Software utilizado:** {{ $prereserva->data['software_utilizado'] ?? 'Nenhum' }}
- **Link do software:** {{ $prereserva->data['software_link'] ?? 'Não informado' }}
- **Quantidade de alunos:** {{ $prereserva->data['quantidade_aluno'] ?? '-' }}
- **Semestre:** {{ $prereserva->data['semestre'] ?? '-' }}
- **Observações:** {{ $prereserva->data['obs'] ?? 'Nenhuma observação' }}

@component('mail::button', ['url' => config('app.url')])
Acessar o sistema
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent