<?php

namespace App\Http\Controllers;

use Uspdev\Forms\Form;
use Uspdev\Forms\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PreReservaController extends Controller
{
    public function form()
    {
        \UspTheme::activeUrl('form');

        $codpes = auth()->user()->codpes;
        $form = new Form(['key' => $codpes]);
        $formHtml = $form->generateHtml('prereserva');
        
        return view('form', compact('formHtml'));
    }

    public static function submission(Request $request)
    {        
        $form = new Form();
        $form->handleSubmission($request);
        
        return redirect(route('list-user'));
    }

    public static function listUser()
    {
        \UspTheme::activeUrl('list-user');

        $user = auth()->user();     
        $codpes = $user->codpes;
        $form = new Form(['key' => $codpes]);
        $submissions = $form->listSubmission('prereserva');

        return view('listUser', compact('submissions', 'user'));
    }

    public static function listUserRelated()
    {
        \UspTheme::activeUrl('list-user-related');

        $user = auth()->user();     
        $codpes = $user->codpes;
        $form = new Form(['key' => $codpes]);
        $submissions = $form->whereSubmissionContains('professor', strval($codpes));

        return view('listUserRelated', compact('submissions', 'user'));
    }

    public static function listAll()
    {
        \UspTheme::activeUrl('list-all');

        if (!Gate::allows('admin') && !Gate::allows('manager')) {
            return response()->json(['alert-danger' => 'Você não tem permissão para acessar esta página.'], 403);
        }

        $submissions = FormSubmission::all();
        
        return view('listAll', compact('submissions'));
    }

    public static function showSubmission($id)
    {
        $form = new Form();
        $submission = $form->getSubmission($id);
        
        return view('showSubmission', compact('submission'));
    }


    public static function editSubmission($id){
        $form = new Form();
        $submission = $form->getSubmission($id);
        $formHtml = $form->generateHtml('prereserva', $submission);
        return view('form', compact('formHtml', 'submission'));
    }

    public static function updateSubmission(Request $request, $id){
        $config['editable'] = true;
        $form = new Form($config);
        $form->handleSubmission($request, $id);

        return redirect(route('form.show', ['id' => $id]));
    }

    public static function deleteSubmission($id){
        $form = new Form();
        $submission = $form->getSubmission($id);
        $submission->delete();
        
        return redirect(route('list-user'));
    }

    public function accept(Request $request, $id)
    {
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
