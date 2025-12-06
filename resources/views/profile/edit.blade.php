<x-app-layout>
    @section('title', 'Profile Settings')

    <x-dashboard-header
        title="Profile Settings"
        subtitle="Manage your account information and preferences"
    />

    <div class="max-w-4xl mx-auto p-6 space-y-6">

        <!-- Success Messages -->
        @if (session('status') === 'profile-updated')
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 rounded">
                Profile information updated successfully!
            </div>
        @endif

        @if (session('status') === 'avatar-updated')
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 rounded">
                Profile picture updated successfully!
            </div>
        @endif

        @if (session('status') === 'avatar-removed')
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 rounded">
                Profile picture removed successfully!
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="bg-green-50 border-l-4 border-green-400 text-green-900 px-4 py-3 rounded">
                Password updated successfully!
            </div>
        @endif

        <!-- Profile Overview Card -->
        <div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
            <div class="flex items-center space-x-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img class="h-24 w-24 rounded-full object-cover ring-4 ring-indigo-100" 
                             src="{{ Storage::url($user->avatar) }}" 
                             alt="{{ $user->name }}">
                    @else
                        <div class="h-24 w-24 rounded-full bg-indigo-600 flex items-center justify-center ring-4 ring-indigo-100">
                            <span class="text-2xl font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- User Info -->
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    @if($user->role)
                        <div class="flex items-center space-x-2">
                            <p class="text-sm {{ $user->isSuperAdmin() ? 'text-purple-600' : 'text-indigo-600' }} font-medium">
                                {{ $user->role->name }}
                            </p>
                            @if($user->isSuperAdmin())
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    Full Access
                                </span>
                            @endif
                        </div>
                    @endif
                    @if($user->department)
                        <p class="text-sm text-gray-500">{{ $user->department->name }}</p>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="text-right">
                    <div class="text-sm text-gray-500">Member since</div>
                    <div class="font-semibold text-gray-900">{{ $user->created_at->format('M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Profile Information</h2>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" value="Full Name" />
                        <x-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                 :value="old('name', $user->name)" required autofocus autocomplete="name" />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" value="Email Address" />
                        <x-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                 :value="old('email', $user->email)" required autocomplete="username" />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-sm text-gray-800">
                                    Your email address is unverified.
                                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Click here to re-send the verification email.
                                    </button>
                                </p>

                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 font-medium text-sm text-green-600">
                                        A new verification link has been sent to your email address.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Phone -->
                    <div>
                        <x-input-label for="phone" value="Phone Number" />
                        <x-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
                                 :value="old('phone', $user->phone)" autocomplete="tel" />
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>

                    <!-- Role (Read-only) -->
                    <div>
                        <x-input-label value="Role" />
                        <div class="mt-1 px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700">
                            {{ $user->role->name ?? 'No Role Assigned' }}
                        </div>
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <x-input-label for="bio" value="Bio" />
                    <textarea id="bio" name="bio" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Tell us a little about yourself...">{{ old('bio', $user->bio) }}</textarea>
                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                    <p class="mt-1 text-sm text-gray-500">Maximum 500 characters</p>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                        Save Changes
                    </button>

                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-gray-600">Saved.</p>
                    @endif
                </div>
            </form>
        </div>

        <!-- Profile Picture -->
        <div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Profile Picture</h2>
            </div>

            <div class="flex items-center space-x-6">
                <!-- Current Avatar -->
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img class="h-20 w-20 rounded-full object-cover ring-4 ring-gray-100" 
                             src="{{ Storage::url($user->avatar) }}" 
                             alt="{{ $user->name }}">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center ring-4 ring-gray-100">
                            <svg class="h-8 w-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Upload Form -->
                <div class="flex-1">
                    <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('patch')

                        <div>
                            <input type="file" id="avatar" name="avatar" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                            <p class="mt-1 text-sm text-gray-500">JPG, PNG, GIF up to 2MB</p>
                        </div>

                        <div class="flex items-center space-x-3">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition text-sm">
                                Upload Picture
                            </button>

                            @if($user->avatar)
                                <form method="POST" action="{{ route('profile.avatar.remove') }}" class="inline">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-md transition text-sm"
                                            onclick="return confirm('Are you sure you want to remove your profile picture?')">
                                        Remove Picture
                                    </button>
                                </form>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Change Password</h2>
            </div>

            <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
                @csrf
                @method('patch')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Current Password -->
                    <div>
                        <x-input-label for="current_password" value="Current Password" />
                        <x-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>

                    <!-- New Password -->
                    <div>
                        <x-input-label for="password" value="New Password" />
                        <x-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" value="Confirm Password" />
                        <x-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                        Update Password
                    </button>

                    @if (session('status') === 'password-updated')
                        <p class="text-sm text-gray-600">Saved.</p>
                    @endif
                </div>
            </form>
        </div>

        <!-- Delete Account -->
        <div class="bg-white shadow-xl rounded-2xl p-6 ring-1 ring-gray-900/5 border-l-4 border-red-400">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-red-900">Delete Account</h2>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
            </p>

            <button type="button" id="deleteAccountBtn" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                Delete Account
            </button>
        </div>

    </div>

    <!-- Delete Account Confirmation Modal -->
    <x-confirmation-modal 
        id="deleteAccountModal"
        title="Delete Account"
        message="Are you sure you want to delete your account? This action cannot be undone."
        confirm-text="Delete Account"
        cancel-text="Cancel"
        confirm-class="bg-red-600 hover:bg-red-700"
        icon="warning"
    />

    <!-- Delete Account Form Modal -->
    <div id="deleteFormModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[70] flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative">
            
            <!-- Close Button -->
            <button id="closeDeleteForm" class="absolute top-3 right-3 text-gray-500 hover:text-gray-800 text-xl">&times;</button>

            <!-- Modal Title -->
            <h2 class="text-2xl font-bold mb-4 text-red-900">Confirm Account Deletion</h2>

            <!-- Form -->
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="mb-6">
                    <x-input-label for="password" value="Please enter your password to confirm:" />
                    <x-input id="delete_password" name="password" type="password" class="mt-1 block w-full" placeholder="Password" />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" id="cancelDelete" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md transition">
                        Delete Account
                    </button>
                </div>
            </form>

        </div>
    </div>

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteAccountBtn = document.getElementById('deleteAccountBtn');
        const deleteFormModal = document.getElementById('deleteFormModal');
        const closeDeleteForm = document.getElementById('closeDeleteForm');
        const cancelDelete = document.getElementById('cancelDelete');

        deleteAccountBtn.addEventListener('click', function() {
            deleteFormModal.classList.remove('hidden');
        });

        function hideDeleteForm() {
            deleteFormModal.classList.add('hidden');
        }

        closeDeleteForm.addEventListener('click', hideDeleteForm);
        cancelDelete.addEventListener('click', hideDeleteForm);

        // Close on backdrop click
        deleteFormModal.addEventListener('click', function(e) {
            if (e.target === deleteFormModal) {
                hideDeleteForm();
            }
        });
    });
    </script>

</x-app-layout>