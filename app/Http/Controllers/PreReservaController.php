<?php

namespace App\Http\Controllers;

use Uspdev\Forms\Form;
use Uspdev\Forms\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
        
        return view('showSubmission', compact('submission'));
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

        return redirect()->back();
    }

}
