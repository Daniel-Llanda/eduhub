<section>
    <header>
        <h2 class="fs-5 fw-medium">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-body-secondary">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('teacher.profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <x-form.label for="name" :value="__('Name')" />

            <x-form.input
                id="name"
                name="name"
                type="text"
                class="form-control"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />

            <x-form.error :messages="$errors->get('name')" />
        </div>

        <div class="mb-3">
            <x-form.label for="email" :value="__('Email')" />

            <x-form.input
                id="email"
                name="email"
                type="email"
                class="form-control"
                :value="old('email', $user->email)"
                required
                autocomplete="email"
            />

            <x-form.error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="small text-body-secondary">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="btn btn-link p-0 text-decoration-underline">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 fw-medium text-success small">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-button class="btn btn-primary">
                {{ __('Save') }}
            </x-button>

            @if (session('status') === 'profile-updated')
                <p class="small text-body-secondary" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
