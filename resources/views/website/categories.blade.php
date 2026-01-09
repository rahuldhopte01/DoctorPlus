@extends('layout.mainlayout', ['activePage' => 'categories'])

@section('title', __('Treatment Categories'))

@section('content')
<div class="xl:w-3/4 mx-auto py-10">
    <div class="mb-10 text-center">
        <h1 class="font-fira-sans font-medium text-4xl text-black mb-4">{{ __('Treatment Categories') }}</h1>
        <p class="font-fira-sans text-gray text-lg">{{ __('Browse our treatment categories to find the right care for you') }}</p>
    </div>

    @if(count($categories) > 0)
    <div class="grid xlg:grid-cols-4 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-2 msm:grid-cols-1 xsm:grid-cols-1 xxsm:grid-cols-1 gap-6">
        @foreach($categories as $category)
        <div class="bg-white shadow-xl rounded-lg overflow-hidden transform hover:scale-105 transition duration-300 cursor-pointer"
             onclick="window.location.href='{{ route('category.detail', ['id' => $category->id]) }}'">
            <div class="p-6">
                @if($category->image)
                <div class="mb-4 flex justify-center">
                    <img src="{{ $category->fullImage }}" alt="{{ $category->name }}" 
                         class="h-24 w-24 object-cover rounded-full">
                </div>
                @else
                <div class="mb-4 flex justify-center">
                    <div class="h-24 w-24 bg-primary bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-stethoscope text-primary text-4xl"></i>
                    </div>
                </div>
                @endif
                
                <h3 class="font-fira-sans font-medium text-xl text-black text-center mb-2">
                    {{ $category->name }}
                </h3>
                
                @if($category->treatment)
                <p class="font-fira-sans text-sm text-gray text-center mb-4">
                    {{ $category->treatment->name }}
                </p>
                @endif

                <div class="text-center">
                    <span class="inline-flex items-center text-primary font-fira-sans text-sm font-medium">
                        {{ __('View Details') }}
                        <svg width="11" height="11" viewBox="0 0 11 11" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="ml-2">
                            <path d="M8.73544 0.852912C8.6542 0.446742 8.25908 0.183329 7.85291 0.264563L1.23399 1.58835C0.827824 1.66958 0.564411 2.0647 0.645646 2.47087C0.72688 2.87704 1.122 3.14045 1.52817 3.05922L7.41165 1.88252L8.58835 7.76601C8.66958 8.17218 9.0647 8.43559 9.47087 8.35435C9.87704 8.27312 10.1405 7.878 10.0592 7.47183L8.73544 0.852912ZM2.62404 10.416L8.62404 1.41602L7.37596 0.583973L1.37596 9.58397L2.62404 10.416Z" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-20">
        <p class="font-fira-sans font-normal text-base text-gray">{{ __('No categories available at this time') }}</p>
    </div>
    @endif
</div>
@endsection
