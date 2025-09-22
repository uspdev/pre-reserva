<?php

namespace App\Http\Controllers;

use Uspdev\Forms\Form;
use Uspdev\Forms\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\PrereservaMail;
use Illuminate\Support\Str;

class PreReservaController extends Controller
{
    public function form()
    {
        \UspTheme::activeUrl('form');

        $codpes = Auth::user()->codpes;
        $this->authorize('authorizedUser');

        $form = new Form(['key' => $codpes]);
        $formHtml = $form->generateHtml('prereserva');
        
        return view('form', compact('formHtml'));
    }

    public function submission(Request $request)
    {       
        $this->authorize('authorizedUser');

        $submission = (new Form(['editable' => true]))->handleSubmission($request);
        PrereservaMail::createdMessage($submission); // É necessário configurar o email de gerência no .env

        return redirect(route('list-user'));
    }

    public function listUser()
    {
        \UspTheme::activeUrl('list-user');

        $this->authorize('authorizedUser');

        $user = Auth::user();     
        $codpes = $user->codpes;
        $form = new Form(['key' => $codpes]);
        $submissions = $form->listSubmission('prereserva');

        return view('listUser', compact('submissions', 'user'));
    }

    public function listUserRelated()
    {
        \UspTheme::activeUrl('list-user-related');

        $this->authorize('authorizedUser');

        $user = Auth::user();     
        $codpes = $user->codpes;
        $form = new Form(['key' => $codpes]);
        $submissions = $form->whereSubmissionContains('professor', strval($codpes));

        return view('listUserRelated', compact('submissions', 'user'));
    }

    public function listAll()
    {
        \UspTheme::activeUrl('list-all');

        if (!Gate::allows('admin') && !Gate::allows('manager')) {
            return redirect(route('list-user'));
        }

        $submissions = FormSubmission::all();
        
        return view('listAll', compact('submissions'));
    }

    public function showSubmission($id)
    {
        $this->authorize('authorizedUser');

        $codpes = Auth::user()->codpes;
        $form = new Form();
        $submission = $form->getSubmission($id);

        if (!Gate::allows('admin') && !Gate::allows('manager') && 
            $submission->key != $codpes && $submission->data['professor'] != $codpes) {
            return redirect(route('list-user'));
        }

        $activities = $form->getSubmissionActivities($id);
        foreach ($activities as $activity) {
            $changes = $activity->changes();
            $aceitoOld = $changes['old']['data']['aceito'] ?? null;
            $aceitoNew = $changes['attributes']['data']['aceito'] ?? null;

            if ($aceitoOld !== null || $aceitoNew !== null) {
                $from = ($aceitoOld === null) ? 'Não avaliado' :
                    (Str::beforeLast($aceitoOld, '-') === 'accepted'
                        ? 'Aceito - '. ucfirst(Str::afterLast($aceitoOld, '-'))
                        : ($aceitoOld === 'accepted' ? 'Aceito' : ($aceitoOld === 'not-accepted' ? 'Rejeitado' : ($aceitoOld === 'not-avaliated' ? 'Não avaliado' : $aceitoOld))));

                $to = ($aceitoNew === null) ? 'Não avaliado' :
                    (Str::beforeLast($aceitoNew, '-') === 'accepted'
                        ? 'Aceito - '. ucfirst(Str::afterLast($aceitoNew, '-'))
                        : ($aceitoNew === 'accepted' ? 'Aceito' : ($aceitoNew === 'not-accepted' ? 'Rejeitado' : ($aceitoNew === 'not-avaliated' ? 'Não avaliado' : $aceitoNew))));

                $activity->description = "Status alterado de <strong>{$from}</strong> para <strong>{$to}</strong>";
            }
        }
        
        return view('showSubmission', compact('submission', 'activities'));
    }


    public function editSubmission($id)
    {
        $this->authorize('authorizedUser');

        $codpes = Auth::user()->codpes;
        $form = new Form(['key' => $codpes]);
        $submission = $form->getSubmission($id);

        if (!Gate::allows('admin') && !Gate::allows('manager') && 
            $submission->key != $codpes && $submission->data['professor'] != $codpes) {
            return redirect(route('list-user'));
        }

        $formHtml = $form->generateHtml('prereserva', $submission);
        return view('form', compact('formHtml', 'submission'));
    }

    public function updateSubmission(Request $request, $id)
    {
        $this->authorize('authorizedUser');

        $config['editable'] = true;
        $codpes = Auth::user()->codpes;
        $config['key'] = $codpes;

        $form = new Form($config);

        $submission = $form->getSubmission($id);
        if (!Gate::allows('admin') && !Gate::allows('manager') && 
            $submission->key != $codpes && $submission->data['professor'] != $codpes) {
            return redirect(route('list-user'));
        }

        $form->handleSubmission($request, $id);

        return redirect(route('form.show', ['id' => $id]));
    }

    public function deleteSubmission($id)
    {
        $this->authorize('authorizedUser');
        $form = new Form();
        
        $codpes = Auth::user()->codpes;
        $submission = $form->getSubmission($id);
        if (!Gate::allows('admin') && !Gate::allows('manager') && 
            $submission->key != $codpes && $submission->data['professor'] != $codpes) {
            return redirect(route('list-user'));
        }

        $submission->delete();
        
        return redirect(route('list-all'))->with('alert-success', 'Submissão deletada com sucesso!');
    }

    public function accept(Request $request, $id)
    {
        if (!Gate::allows('admin') && !Gate::allows('manager')) {
            return redirect(route('list-user'));
        }

        $config['editable'] = true;
        $form = new Form($config);
        $submission = $form->getSubmission($id);
        $requestData = $submission->data;

        $requestData['aceito'] = $request->input('accepted');
        $requestData['form_definition'] = 'prereserva';
        $requestData['id'] = $submission->id;
        $requestData['key'] = $submission->key;

        $newRequest = Request::create('/fake-url', 'POST', $requestData);
        $form->handleSubmission($newRequest, $id);

        $submission = $form->getSubmission($id);
        PrereservaMail::avaliatedMessage($submission);

        return redirect()->back();
    }

}
