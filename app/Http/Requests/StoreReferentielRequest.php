<?php
// StoreReferentielRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReferentielRequest extends FormRequest
{
    public function rules()
    {
        return [
            'code' => 'required|unique:referentiels,code',
            'libelle' => 'required|unique:referentiels,libelle',
            'description' => 'required',
            'photo' => 'required|image',
        ];
    }
}