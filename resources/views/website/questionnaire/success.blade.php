@extends('layout.mainlayout', ['activePage' => 'questionnaire'])

@section('title', __('Questionnaire Submitted'))

@section('content')
<div class="xl:w-3/4 mx-auto py-20">
    <div class="bg-white shadow-xl rounded-lg p-12 text-center">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <i class="fas fa-check text-green-500 text-4xl"></i>
            </div>
            <h1 class="font-fira-sans font-medium text-3xl text-black mb-4">
                {{ __('Questionnaire Submitted Successfully!') }}
            </h1>
            <p class="font-fira-sans text-gray text-lg mb-8">
                {{ __('Thank you for completing the questionnaire. Your responses have been saved.') }}
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('categories') }}" class="bg-primary text-white font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-opacity-90 transition duration-300">
                {{ __('Browse More Categories') }}
            </a>
            <a href="{{ url('/') }}" class="bg-gray-200 text-gray-700 font-fira-sans font-medium px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-300">
                {{ __('Go to Home') }}
            </a>
        </div>
    </div>
</div>
@endsection
