<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - MySebenarnya System</title>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    @stack('styles')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed w-full bg-white/90 backdrop-blur-md shadow-sm z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                        <img src="{{ asset('image/MCMC.png.webp') }}" class="h-20 w-20 object-contain" />
                            <span class="ml-2 text-xl font-bold gradient-text">MySebenarnya</span>
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800"></a>
                    </div>

                    <!-- Primary Navigation for PublicUser -->
                    @if(Auth::user()->isPublicUser())
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('PublicUser.dashboard', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('PublicUser.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('PublicUser.dashboard') ? 'page' : '' }}">
                                    Dashboard
                                </a>

                                <a href="{{ route('PublicUser.profile', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('PublicUser.profile') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('PublicUser.profile') ? 'page' : '' }}">
                                    Profile
                                </a>

                                <a href="{{ route('PublicUser.InquiryForm', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('PublicUser.InquiryForm') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('PublicUser.InquiryForm') ? 'page' : '' }}">
                                    Inquiry Form
                                </a>
                                <a href="{{ route('PublicUser.InquiryHistory', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('PublicUser.InquiryHistory') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('PublicUser.InquiryHistory') ? 'page' : '' }}">
                                    Inquiry History
                                </a>
                                <!-- <a href="{{ route('PublicUser.InquiryStatus', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('PublicUser.InquiryStatus') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}" 
                                aria-current="{{ request()->routeIs('PublicUser.InquiryStatus') ? 'page' : '' }}">
                                    Inquiry Status
                                </a> -->
                                <a href="{{ route('PublicUser.PublicInquiry', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('PublicUser.PublicInquiry') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('PublicUser.PublicInquiry') ? 'page' : '' }}">
                                    Public Inquiry
                                </a>

                            <!-- Add more public user links here if needed -->
                        </div>
                        @elseif(Auth::user()->isMCMC())
                        <!-- Primary Navigation for MCMC -->
                         <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('MCMC.dashboard', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('MCMC.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('MCMC.dashboard') ? 'page' : '' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('MCMC.UserData', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('MCMC.UserData') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('MCMC.UserData') ? 'page' : '' }}">
                                    User Data
                                </a>
                                <a href="{{ route('MCMC.InquiryList', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('MCMC.InquiryList') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('MCMC.InquiryList') ? 'page' : '' }}">
                                    Inquiry List
                                </a>
                                <a href="{{ route('MCMC.AssignedInquiry', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('MCMC.AssignedInquiry') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('MCMC.AssignedInquiry') ? 'page' : '' }}">
                                    Assigned Inquiry
                                </a>
                                <a href="{{ route('MCMC.InquiryProgress', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('MCMC.InquiryProgress') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('MCMC.InquiryProgress') ? 'page' : '' }}">
                                    Inquiry Progress
                                </a>

                                </div>
                        @elseif(Auth::user()->isAgency())
                        <!-- Primary Navigation for Agency -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('Agency.dashboard', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('Agency.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('Agency.dashboard') ? 'page' : '' }}">
                                    Dashboard
                                </a>

                                <a href="{{ route('Agency.profile', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('Agency.profile') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('Agency.profile') ? 'page' : '' }}">
                                    Profile
                                </a>
                                <a href="{{ route('Agency.InquiryHistory', ['user_id' => Auth::id()]) }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('Agency.InquiryHistory') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('Agency.InquiryHistory') ? 'page' : '' }}">
                                    Inquiry History
                                </a>
                                <a href="{{ route('Agency.InquiryList', ['user_id' => Auth::id()]) }}" 
                                class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5
                                {{ request()->routeIs('Agency.InquiryList') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                aria-current="{{ request()->routeIs('Agency.InquiryList') ? 'page' : '' }}">
                                    Inquiry List
                                </a>
                        </div>
                    @endif
                </div>

                <!-- Right side -->
                <div class="flex items-center">
                    {{-- Notifications can go here if needed --}}
                    <form method="POST" action="{{ route('logout') }}" class="ml-3">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto py-6 px-4">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>