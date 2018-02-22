<?php

declare(strict_types=1);

namespace Cortex\Auth\B2B2C2\Http\Controllers\Managerarea;

use Illuminate\Support\Str;
use Rinvex\Auth\Contracts\PasswordResetBrokerContract;
use Cortex\Foundation\Http\Controllers\AbstractController;
use Cortex\Auth\B2B2C2\Http\Requests\Managerarea\PasswordResetRequest;
use Cortex\Auth\B2B2C2\Http\Requests\Managerarea\PasswordResetSendRequest;
use Cortex\Auth\B2B2C2\Http\Requests\Managerarea\PasswordResetProcessRequest;
use Cortex\Auth\B2B2C2\Http\Requests\Managerarea\PasswordResetPostProcessRequest;

class PasswordResetController extends AbstractController
{
    /**
     * Show the password reset request form.
     *
     * @param Cortex\Auth\B2B2C2\Http\Requests\Managerarea\PasswordResetRequest
     *
     * @return \Illuminate\View\View
     */
    public function request(PasswordResetRequest $request)
    {
        return view('cortex/auth::managerarea.pages.passwordreset-request');
    }

    /**
     * Process the password reset request form.
     *
     * @param \Cortex\Auth\B2B2C2\Http\Requests\Managerarea\PasswordResetSendRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function send(PasswordResetSendRequest $request)
    {
        $result = app('auth.password')
            ->broker($this->getBroker())
            ->sendResetLink($request->only(['email']));

        switch ($result) {
            case PasswordResetBrokerContract::RESET_LINK_SENT:
                return intend([
                    'url' => route('managerarea.home'),
                    'with' => ['success' => trans($result)],
                ]);

            case PasswordResetBrokerContract::INVALID_USER:
            default:
                return intend([
                    'back' => true,
                    'withInput' => $request->only(['email']),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }

    /**
     * Show the password reset form.
     *
     * @param \Cortex\Auth\B2B2C2\Http\Requests\Managerarea\PasswordResetProcessRequest $request
     *
     * @return \Illuminate\View\View
     */
    public function reset(PasswordResetProcessRequest $request)
    {
        $credentials = $request->only('email', 'expiration', 'token');

        return view('cortex/auth::managerarea.pages.passwordreset')->with($credentials);
    }

    /**
     * Process the password reset form.
     *
     * @param \Cortex\Auth\B2B2C2\Http\Requests\Managerarea\PasswordResetPostProcessRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function process(PasswordResetPostProcessRequest $request)
    {
        $result = app('auth.password')
            ->broker($this->getBroker())
            ->reset($request->only(['email', 'expiration', 'token', 'password', 'password_confirmation']), function ($user, $password) {
                $user->fill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->forceSave();
            });

        switch ($result) {
            case PasswordResetBrokerContract::PASSWORD_RESET:
                return intend([
                    'url' => route('managerarea.login'),
                    'with' => ['success' => trans($result)],
                ]);

            case PasswordResetBrokerContract::INVALID_USER:
            case PasswordResetBrokerContract::INVALID_TOKEN:
            case PasswordResetBrokerContract::EXPIRED_TOKEN:
            case PasswordResetBrokerContract::INVALID_PASSWORD:
            default:
                return intend([
                    'back' => true,
                    'withInput' => $request->only(['email']),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }
}
