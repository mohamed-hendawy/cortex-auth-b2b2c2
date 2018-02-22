<?php

declare(strict_types=1);

namespace Cortex\Auth\B2B2C2\Http\Controllers\Tenantarea;

use Cortex\Foundation\Http\Controllers\AbstractController;
use Rinvex\Auth\Contracts\EmailVerificationBrokerContract;
use Cortex\Auth\B2B2C2\Http\Requests\Tenantarea\EmailVerificationRequest;
use Cortex\Auth\B2B2C2\Http\Requests\Tenantarea\EmailVerificationSendRequest;
use Cortex\Auth\B2B2C2\Http\Requests\Tenantarea\EmailVerificationProcessRequest;

class EmailVerificationController extends AbstractController
{
    /**
     * Show the email verification request form.
     *
     * @param \Cortex\Auth\B2B2C2\Http\Requests\Tenantarea\EmailVerificationRequest $request
     *
     * @return \Illuminate\View\View
     */
    public function request(EmailVerificationRequest $request)
    {
        return view('cortex/auth::tenantarea.pages.verification-email-request');
    }

    /**
     * Process the email verification request form.
     *
     * @param \Cortex\Auth\B2B2C2\Http\Requests\Tenantarea\EmailVerificationSendRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function send(EmailVerificationSendRequest $request)
    {
        $result = app('rinvex.auth.emailverification')
            ->broker($this->getBroker())
            ->sendVerificationLink($request->only(['email']));

        switch ($result) {
            case EmailVerificationBrokerContract::LINK_SENT:
                return intend([
                    'url' => route('tenantarea.home'),
                    'with' => ['success' => trans('cortex/auth::'.$result)],
                ]);

            case EmailVerificationBrokerContract::INVALID_USER:
            default:
                return intend([
                    'back' => true,
                    'withInput' => $request->only(['email']),
                    'withErrors' => ['email' => trans('cortex/auth::'.$result)],
                ]);
        }
    }

    /**
     * Process the email verification.
     *
     * @param \Cortex\Auth\B2B2C2\Http\Requests\Tenantarea\EmailVerificationProcessRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(EmailVerificationProcessRequest $request)
    {
        $result = app('rinvex.auth.emailverification')
            ->broker($this->getBroker())
            ->verify($request->only(['email', 'expiration', 'token']), function ($user) {
                $user->fill([
                    'email_verified' => true,
                    'email_verified_at' => now(),
                ])->forceSave();
            });

        switch ($result) {
            case EmailVerificationBrokerContract::EMAIL_VERIFIED:
                return intend([
                    'url' => route('tenantarea.account.settings'),
                    'with' => ['success' => trans('cortex/auth::'.$result)],
                ]);

            case EmailVerificationBrokerContract::INVALID_USER:
            case EmailVerificationBrokerContract::INVALID_TOKEN:
            case EmailVerificationBrokerContract::EXPIRED_TOKEN:
            default:
                return intend([
                    'url' => route('tenantarea.verification.email.request'),
                    'withInput' => $request->only(['email']),
                    'withErrors' => ['email' => trans('cortex/auth::'.$result)],
                ]);
        }
    }
}
