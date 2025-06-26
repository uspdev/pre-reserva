<?php
namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\PrereservaNotification;
use Uspdev\Replicado\Pessoa;

class PrereservaMail
{
    private static function getEmailContent($type, $role, $prereserva)
    {
        $sala = '';
        if (isset($prereserva->data['aceito'])) {
            $sala = '<br><br>Sala designada: <strong>' .
                \Illuminate\Support\Str::upper(
                    \Illuminate\Support\Str::afterLast($prereserva->data['aceito'], '-')
                ) .
                '</strong>';
        }
        $contato = 'Se você tiver dúvidas ou precisar de mais informações, entre em contato com a equipe da SCINFOR pelo e-mail <a href="mailto:informatica@eesc.usp.br">informatica@eesc.usp.br</a>.';

        $cases = [
            'accepted_autor' => [
                'title' => 'Pré-reserva aceita',
                'message' => 'Sua pré-reserva para as salas informatizadas foi aceita!' . $sala,
            ],
            'analysis_autor' => [
                'title' => 'Pré-reserva em Análise',
                'message' => 'Sua pré-reserva para as salas informatizadas foi reavaliada e voltou ao estado em análise.<br><br>' . $contato,
            ],
            'created_autor' => [
                'title' => 'Pré-reserva criada',
                'message' => 'Uma nova pré-reserva para as salas informatizadas foi criada por você.',
            ],
            'rejected_autor' => [
                'title' => 'Pré-reserva não aceita',
                'message' => 'Infelizmente, sua solicitação de pré-reserva para as salas informatizadas não foi aprovada neste momento.<br><br>' . $contato,
            ],

            'accepted_professor' => [
                'title' => 'Pré-reserva aceita',
                'message' => 'A pré-reserva para as salas informatizadas feita para você foi aceita!' . $sala,
            ],
            'analysis_professor' => [
                'title' => 'Pré-reserva em Análise',
                'message' => 'A pré-reserva para as salas informatizadas feita para você foi reavaliada e voltou ao estado em análise.<br><br>' . $contato,
            ],
            'created_professor' => [
                'title' => 'Pré-reserva criada',
                'message' => 'Uma nova pré-reserva para as salas informatizadas foi criada para você.',
            ],
            'rejected_professor' => [
                'title' => 'Pré-reserva não aceita',
                'message' => 'Infelizmente, sua solicitação de pré-reserva para as salas informatizadas não foi aprovada neste momento.<br><br>' . $contato,
            ],

            'created_manager' => [
                'title' => 'Pré-reserva criada',
                'message' => 'Uma nova pré-reserva foi criada para as salas informatizadas.',
            ],
        ];

        $key = "{$type}_{$role}";
        return $cases[$key] ?? [
            'title' => 'Notificação de Pré-reserva',
            'message' => '',
        ];
    }

    public static function createdMessage($submission){
        $professorMail = Pessoa::email($submission->data['professor']);
        $autorMail = Pessoa::email($submission->key);

        if(config('preserva.nofityEmail')){
            $content = SELF::getEmailContent('created', 'manager', $submission);
            Mail::to(config('preserva.nofityEmail'))->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
        }
        
        if($professorMail != $autorMail){
            $content = SELF::getEmailContent('created', 'professor', $submission);
            Mail::to($professorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));

            $content = SELF::getEmailContent('created', 'autor', $submission);
            Mail::to($autorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
        } else {
            $content = SELF::getEmailContent('created', 'autor', $submission);
            Mail::to($autorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
        }
    }

    public static function avaliatedMessage($submission){
        $professorMail = Pessoa::email($submission->data['professor']);
        $autorMail = Pessoa::email($submission->key);

        if($submission->data['aceito'] == 'not-accepted'){
           if($professorMail != $autorMail){
                $content = SELF::getEmailContent('rejected', 'professor', $submission);
                Mail::to($professorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));

                $content = SELF::getEmailContent('rejected', 'autor', $submission);
                Mail::to($autorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
            } else {
                $content = SELF::getEmailContent('rejected', 'autor', $submission);
                Mail::to($autorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
            }
        } elseif($submission->data['aceito'] == 'not-avaliated') {
            if($professorMail != $autorMail){
                $content = SELF::getEmailContent('analysis', 'professor', $submission);
                Mail::to($professorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));

                $content = SELF::getEmailContent('analysis', 'autor', $submission);
                Mail::to($autorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
            } else {
                $content = SELF::getEmailContent('analysis', 'autor', $submission);
                Mail::to($autorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
            }
        } else {
            if($professorMail != $autorMail){
                $content = SELF::getEmailContent('accepted', 'professor', $submission);
                Mail::to($professorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));

                $content = SELF::getEmailContent('accepted', 'autor', $submission);
                Mail::to($autorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
            } else {
                $content = SELF::getEmailContent('accepted', 'autor', $submission);
                Mail::to($autorMail)->queue(new PrereservaNotification($submission, $content['title'], $content['message']));
            }
        }
    }
}