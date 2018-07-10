<?php

declare(strict_types=1);

namespace Cortex\Auth\B2B2C2\Http\Requests\Frontarea;

use Rinvex\Support\Traits\Escaper;
use Illuminate\Foundation\Http\FormRequest;

class AccountSettingsRequest extends FormRequest
{
    use Escaper;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return void
     */
    public function withValidator($validator): void
    {
        // Sanitize input data before submission
        $this->replace($this->escape($this->all()));
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $data = $this->all();

        if ($user = $this->user($this->route('guard'))) {
            $country = $data['country_code'] ?? null;
            $email = $data['email'] ?? null;
            $phone = $data['phone'] ?? null;
            $twoFactor = $user->getTwoFactor();

            if ($email !== $user->email) {
                $data['email_verified'] = false;
                $data['email_verified_at'] = null;
            }

            if ($phone !== $user->phone) {
                $data['phone_verified'] = false;
                $data['phone_verified_at'] = null;
            }

            if ($twoFactor && (isset($data['phone_verified']) || $country !== $user->country_code)) {
                array_set($twoFactor, 'phone.enabled', false);
                $data['two_factor'] = $twoFactor;
            }
        }

        $this->replace($data);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $user = $this->user($this->route('guard'));
        $user->updateRulesUniques();

        return $user->getRules();
    }
}
