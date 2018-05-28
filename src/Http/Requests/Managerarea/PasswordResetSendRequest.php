<?php

declare(strict_types=1);

namespace Cortex\Auth\B2B2C2\Http\Requests\Managerarea;

class PasswordResetSendRequest extends PasswordResetRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|min:3|max:150|exists_model:'.config('cortex.auth.models.manager').',email',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRedirectUrl()
    {
        return $this->redirector->getUrlGenerator()->route('managerarea.passwordreset.request');
    }
}
