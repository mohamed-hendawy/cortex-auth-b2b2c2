<?php

declare(strict_types=1);

namespace Cortex\Auth\B2B2C2\Http\Controllers\Frontarea;

use Cortex\Foundation\Http\Controllers\AuthenticatedController;
use Cortex\Auth\B2B2C2\Http\Requests\Frontarea\AccountPasswordRequest;

class AccountPasswordController extends AuthenticatedController
{
    /**
     * Edit account possword.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('cortex/auth::frontarea.pages.account-password');
    }

    /**
     * Update account password.
     *
     * @param \Cortex\Auth\B2B2C2\Http\Requests\Frontarea\AccountPasswordRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(AccountPasswordRequest $request)
    {
        $currentUser = $request->user($this->getGuard());

        // Update profile
        $currentUser->fill(['password' => $request->get('new_password')])->forceSave();

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/auth::messages.account.updated_password')],
        ]);
    }
}
