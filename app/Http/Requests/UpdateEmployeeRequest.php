<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autoriser la requête pour l'instant
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route("employee")->id;

        return [
            "first_name" => "required|string|max:100",
            "last_name" => "required|string|max:100",
            "birth_date" => "nullable|date|before:today",
            "email" =>
                "required|email|max:255|unique:employees,email," . $employeeId,
            "phone" => "nullable|string|max:20",
            "address" => "nullable|string|max:500",
            "position_id" => "required|exists:positions,id",
            "salary_base" => "required|numeric|min:0|max:999999.99",
            "status" => "required|in:actif,inactif,suspendu",
            "user_id" => "nullable|exists:users,id",
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public function messages(): array
    {
        return [
            "first_name.required" => "Le prénom est obligatoire.",
            "first_name.max" =>
                "Le prénom ne doit pas dépasser 100 caractères.",
            "last_name.required" => "Le nom est obligatoire.",
            "last_name.max" => "Le nom ne doit pas dépasser 100 caractères.",
            "email.required" => 'L\'email est obligatoire.',
            "email.unique" => "Cet email est déjà utilisé.",
            "email.email" => "Veuillez entrer une adresse email valide.",
            "email.max" => 'L\'email ne doit pas dépasser 255 caractères.',
            "position_id.required" => "Le poste est obligatoire.",
            "position_id.exists" => 'Le poste sélectionné n\'existe pas.',
            "salary_base.required" => "Le salaire de base est obligatoire.",
            "salary_base.numeric" => "Le salaire doit être un nombre.",
            "salary_base.min" => "Le salaire ne peut pas être négatif.",
            "salary_base.max" => "Le salaire ne peut pas dépasser 999999.99.",
            "status.required" => "Le statut est obligatoire.",
            "status.in" => "Le statut doit être actif, inactif ou suspendu.",
            "birth_date.before" =>
                'La date de naissance doit être antérieure à aujourd\'hui.',

            "phone.max" => "Le téléphone ne doit pas dépasser 20 caractères.",
            "address.max" => 'L\'adresse ne doit pas dépasser 500 caractères.',
            "user_id.exists" => 'L\'utilisateur sélectionné n\'existe pas.',
        ];
    }
}
