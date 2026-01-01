<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DriverRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return self::baseRules($this->route('driver')?->id);
    }

    public static function baseRules(?int $driverId = null): array
    {
        return [
            'nom' => ['required', 'string', 'max:120'],
            'prenom' => ['required', 'string', 'max:120'],
            'matricule' => ['required', 'string', 'max:100', Rule::unique('drivers', 'matricule')->ignore($driverId)],
            'cin' => ['required', 'string', 'max:100', Rule::unique('drivers', 'cin')->ignore($driverId)],
            'date_naissance' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'max:2048'],

            'service_affecte' => ['nullable', 'string', 'max:150'],
            'responsable_hierarchique' => ['nullable', 'string', 'max:150'],
            'poste_occupe' => ['nullable', 'string', 'max:150'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'email_pro' => ['nullable', 'email', 'max:150'],

            'num_permis' => ['nullable', 'string', 'max:120'],
            'date_delivrance' => ['nullable', 'date'],
            'date_expiration' => ['nullable', 'date', 'after_or_equal:date_delivrance'],
            'categories' => ['nullable', 'string', 'max:50'],
            'scan_permis' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],

            'statut_actuel' => ['required', 'string', Rule::in(['Disponible', 'En congé', 'Maladie', 'Non disponible'])],
        ];
    }
}
