<?php

declare(strict_types=1);

namespace Cortex\Auth\B2B2C2\Http\Controllers\Managerarea;

use Illuminate\Http\Request;
use Cortex\Foundation\Http\Controllers\AuthenticatedController;
use Cortex\Auth\B2B2C2\Http\Requests\Managerarea\AccountSettingsRequest;

class AccountSettingsController extends AuthenticatedController
{
    /**
     * Redirect to account settings..
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return intend([
            'url' => route('managerarea.account.settings'),
        ]);
    }

    /**
     * Edit account settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $countries = collect(countries())->map(function ($country, $code) {
            return [
                'id' => $code,
                'text' => $country['name'],
                'emoji' => $country['emoji'],
            ];
        })->values();
        $languages = collect(languages())->pluck('name', 'iso_639_1');
        $genders = ['male' => trans('cortex/auth::common.male'), 'female' => trans('cortex/auth::common.female')];

        return view('cortex/auth::managerarea.pages.account-settings', compact('countries', 'languages', 'genders'));
    }

    /**
     * Update account settings.
     *
     * @param \Cortex\Auth\B2B2C2\Http\Requests\Managerarea\AccountSettingsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(AccountSettingsRequest $request)
    {
        $data = $request->validated();
        $currentUser = $request->user($this->getGuard());

        // Update profile
        $currentUser->fill($data)->save();

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/auth::messages.account.updated_account')]
                      + (isset($data['two_factor']) ? ['warning' => trans('cortex/auth::messages.verification.twofactor.phone.auto_disabled')] : []),
        ]);
    }
}
