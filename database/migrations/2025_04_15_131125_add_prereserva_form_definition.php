<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('form_definitions')->insert([
            'id' => 1,
            'name' => 'prereserva',
            'group' => 'prereserva',
            'description' => 'formulário de pre reservas',
            'fields' => json_encode([
                ["name" => "professor", "type" => "pessoa-usp", "label" => "Professor", "required" => true],
                ["name" => "disciplina", "type" => "disciplina-usp", "label" => "Disciplina", "required" => true],
                ["name" => "hora_inicio", "type" => "time", "label" => "Horário de Início", "required" => true],
                ["name" => "hora_fim", "type" => "time", "label" => "Horário de Término", "required" => true],
                ["name" => "dia_semana", "type" => "checkbox", "label" => "Dia da Semana", "required" => true, "options" => [
                    ["label" => "Segunda-feira", "value" => "segunda"],
                    ["label" => "Terça-feira", "value" => "terca"],
                    ["label" => "Quarta-feira", "value" => "quarta"],
                    ["label" => "Quinta-feira", "value" => "quinta"],
                    ["label" => "Sexta-feira", "value" => "sexta"],
                    ["label" => "Sábado", "value" => "sabado"],
                    ["label" => "Domingo", "value" => "domingo"]
                ]],
                ["name" => "software_utilizado", "type" => "text", "label" => "Software que vai utilizar para aula", "required" => true],
                ["name" => "software_link", "type" => "text", "label" => "Link para download do software (se necessário)"],
                ["name" => "quantidade_aluno", "type" => "number", "label" => "Quantidade de Alunos", "required" => true],
                ["name" => "obs", "type" => "textarea", "label" => "Observações"],
                ["name" => "semestre", "type" => "hidden", "value" => "20252"],
                ["name" => "aceito", "type" => "hidden"]
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('form_definitions')->where('id', 1)->delete();
    }
};
