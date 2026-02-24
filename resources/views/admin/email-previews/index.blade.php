@extends('layouts.admin')

@section('title', 'Email Template Previews')

@section('content')
<div class="p-4 md:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Email Template Previews</h1>
        <p class="text-sm text-gray-600 mt-1">Preview all transactional email templates with desktop and mobile widths</p>
    </div>

    @foreach($grouped as $category => $categoryTemplates)
        <div class="mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-4">{{ $category }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($categoryTemplates as $template)
                    <a href="{{ route('admin.email-previews.preview', $template['slug']) }}" target="_blank"
                       class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-admin-teal/30 transition-all duration-200 group">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-lg bg-admin-teal/10 flex items-center justify-center group-hover:bg-admin-teal/20 transition-colors">
                                <i class="fas {{ $template['icon'] }} text-admin-teal"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $template['name'] }}</h3>
                                <p class="text-xs text-gray-500">{{ $template['category'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center text-xs text-admin-teal font-medium group-hover:underline">
                            <i class="fas fa-eye mr-1"></i>Preview
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
@endsection
