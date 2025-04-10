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
        $form = new Form(['key' => $codpes]);
        $formHtml = $form->generateHtml('prereserva');
        
        return view('form', compact('formHtml'));
    }

    public static function submission(Request $request)
    {       
        $codpes = Auth::user()->codpes;
        $form = new Form(['key' => $codpes]);
        $form->handleSubmission($request);
        
        return redirect(route('list-user'));
    }

    public static function listUser()
    {
        \UspTheme::activeUrl('list-user');

        $user = Auth::user();     
        $codpes = $user->codpes;
        $form = new Form(['key' => $codpes]);
        $submissions = $form->listSubmission('prereserva');

        return view('listUser', compact('submissions', 'user'));
    }

    public static function listUserRelated()
    {
        \UspTheme::activeUrl('list-user-related');

        $user = Auth::user();     
        $codpes = $user->codpes;
        $form = new Form(['key' => $codpes]);
        $submissions = $form->whereSubmissionContains('professor', strval($codpes));

        return view('listUserRelated', compact('submissions', 'user'));
    }

    public static function listAll()
    {
        \UspTheme::activeUrl('list-all');

        if (!Gate::allows('admin') && !Gate::allows('manager')) {
            return redirect(route('list-user'));
        }

        $submissions = FormSubmission::all();
        
        return view('listAll', compact('submissions'));
    }

    public static function showSubmission($id)
    {
        $codpes = Auth::user()->codpes;
        $form = new Form();
        $submission = $form->getSubmission($id);

        if (!Gate::allows('admin') && !Gate::allows('manager') && 
            $submission->key != $codpes && $submission->data->professor != $codpes) {
            return redirect(route('list-user'));
        }
        
        return view('showSubmission', compact('submission'));
    }


    public static function editSubmission($id){
        $codpes = Auth::user()->codpes;
        $form = new Form();
        $submission = $form->getSubmission($id);

        if (!Gate::allows('admin') && !Gate::allows('manager') && 
            $submission->key != $codpes && $submission->data->professor != $codpes) {
            return redirect(route('list-user'));
        }

        $formHtml = $form->generateHtml('prereserva', $submission);
        return view('form', compact('formHtml', 'submission'));
    }

    public static function updateSubmission(Request $request, $id){
        $config['editable'] = true;
        $form = new Form($config);

        $codpes = Auth::user()->codpes;
        $submission = $form->getSubmission($id);
        if (!Gate::allows('admin') && !Gate::allows('manager') && 
            $submission->key != $codpes && $submission->data->professor != $codpes) {
            return redirect(route('list-user'));
        }

        $form->handleSubmission($request, $id);

        return redirect(route('form.show', ['id' => $id]));
    }

    public static function deleteSubmission($id){
        $form = new Form();
        
        $codpes = Auth::user()->codpes;
        $submission = $form->getSubmission($id);
        if (!Gate::allows('admin') && !Gate::allows('manager') && 
            $submission->key != $codpes && $submission->data->professor != $codpes) {
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
