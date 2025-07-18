<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'profile' => \App\Http\Middleware\CheckRole::class,
        'profileSubmodule' => \App\Http\Middleware\CheckSubmoduleRole::class,
        'verify.ownership.polymorph.page' => \App\Http\Middleware\VerifyOwnershipPolymorphPage::class,
        'verify.ownership.polymorph.api' => \App\Http\Middleware\VerifyOwnershipPolymorphApi::class,
        'verify.ownership.audit.page' => \App\Http\Middleware\VerifyOwnershipAuditPage::class,
        'verify.ownership.audit.api' => \App\Http\Middleware\VerifyOwnershipAuditApi::class,
        'verify.ownership.obligation.page' => \App\Http\Middleware\VerifyOwnershipObligationPage::class,
        'verify.ownership.obligation.api' => \App\Http\Middleware\VerifyOwnershipObligationApi::class,
        'verify.ownership.aplicability.page' => \App\Http\Middleware\VerifyOwnershipAplicabilityPage::class,
        'verify.ownership.aplicability.api' => \App\Http\Middleware\VerifyOwnershipAplicabilityApi::class,
        'verify.ownership.corporate.api' => \App\Http\Middleware\VerifyOwnershipCorporateApi::class,
        'verify.ownership.corporate.address.api' => \App\Http\Middleware\VerifyOwnershipCorporateAddressApi::class,
        'verify.ownership.corporate.contact.api' => \App\Http\Middleware\VerifyOwnershipCorporateContactApi::class,
        'verify.ownership.aspect.matter.api' => \App\Http\Middleware\VerifyOwnershipAspectMatterApi::class,
        'verify.ownership.article.guideline.api' => \App\Http\Middleware\VerifyOwnershipArticleGuidelineApi::class,
        'verify.ownership.form.question.api' => \App\Http\Middleware\VerifyOwnershipFormQuestionApi::class,
        'verify.ownership.form.requirement.api' => \App\Http\Middleware\VerifyOwnershipFormRequirementApi::class,
        'verify.ownership.form.requirement.recomendation.api' => \App\Http\Middleware\VerifyOwnershipFormRequirementRecomendationApi::class,
        'verify.ownership.form.requirement.subrecomendation.api' => \App\Http\Middleware\VerifyOwnershipFormRequirementSubrecomendationApi::class,
        'verify.ownership.polymorph.comment.api' => \App\Http\Middleware\VerifyOwnershipPolymorphCommentApi::class,
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces non-global middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Routing\Middleware\ThrottleRequests::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
