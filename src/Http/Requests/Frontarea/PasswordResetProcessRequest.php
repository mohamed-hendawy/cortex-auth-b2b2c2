<?php

declare(strict_types=1);

namespace Cortex\Auth\B2B2C2\Http\Requests\Frontarea;

use Rinvex\Auth\Contracts\PasswordResetBrokerContract;

class PasswordResetProcessRequest extends PasswordResetRequest
{
    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     *
     * @return void
     */
    public function withValidator($validator): void
    {
        $credentials = $this->only('email', 'expiration', 'token');
        $broker = app('auth.password')->broker($this->route('broker'));

        $validator->after(function ($validator) use ($broker, $credentials) {
            if (! ($user = $broker->getUser($credentials))) {
                $validator->errors()->add('email', trans('cortex/auth::'.PasswordResetBrokerContract::INVALID_USER));
            }

            if ($user && ! $broker->validateToken($user, $credentials)) {
                $validator->errors()->add('email', trans('cortex/auth::'.PasswordResetBrokerContract::INVALID_TOKEN));
            }

            if (! $broker->validateTimestamp($credentials['expiration'])) {
                $validator->errors()->add('email', trans('cortex/auth::'.PasswordResetBrokerContract::EXPIRED_TOKEN));
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // Do not validate `token` here since at this stage we can NOT generate viewable error,
            // and it is been processed in the controller through EmailVerificationBroker anyway
            //'token' => 'required|regex:/^([0-9a-f]*)$/',
            'email' => 'required|email|min:3|max:150|exists:'.config('cortex.auth.tables.members').',email',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRedirectUrl()
    {
        return $this->redirector->getUrlGenerator()->route('frontarea.passwordreset.request');
    }
}
