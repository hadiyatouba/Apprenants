<?php
// UpdateReferentielRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReferentielRequest extends FormRequest
{
    public function rules()
    {
        return [
            'code' => ['required', Rule::unique('referentiels')->ignore($this->referentiel)],
            'libelle' => ['required', Rule::unique('referentiels')->ignore($this->referentiel)],
            'description' => 'required',
            'photo' => 'image',
        ];
    }
}