
<?php

declare(strict_types=1);

namespace App\Http\Requests\System\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Garante que apenas usuários autenticados processem a requisição.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Higieniza e normaliza os dados antes de rodar a validação.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'email' => is_string($this->email) ? Str::lower(trim($this->email)) : $this->email,
        ]);
    }

    /**
     * Regras de validação aplicáveis à requisição.
     *
     * @return array<string, array<int, ValidationRule|string|\Illuminate\Validation\Rules\Unique>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email:rfc,filter',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()?->getKey()),
            ],
        ];
    }

    /**
     * Nomes amigáveis dos campos para mensagens de erro no frontend.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nome',
            'email' => 'e-mail',
        ];
    }
}
