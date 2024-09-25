<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string|max:255|unique:promotions',
            'start_date' => 'required|date',
            'duration' => 'required|integer|min:1',
            'referential_ids' => 'array|exists:referentials,id'
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Le titre de la promotion est obligatoire.',
            'title.string' => 'Le titre de la promotion doit être une chaîne de caractères.',
            'title.max' => 'Le titre de la promotion ne peut pas dépasser 255 caractères.',
            'title.unique' => 'Ce titre de promotion existe déjà.',
            'start_date.required' => 'La date de début de la promotion est obligatoire.',
            'start_date.date' => 'La date de début de la promotion doit être une date valide.',
            'duration.required' => 'La durée de la promotion est obligatoire.',
            'duration.integer' => 'La durée de la promotion doit être un nombre entier.',
            'duration.min' => 'La durée de la promotion doit être d\'au moins 1 mois.',
            'referential_ids.array' => 'Les identifiants des référentiels doivent être sous forme de tableau.',
            'referential_ids.exists' => 'Un ou plusieurs identifiants de référentiels sont invalides.'
        ];
    }



    public function authorize()
    {
        return true;
    }

    
}