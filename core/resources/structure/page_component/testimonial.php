<?php

return [

    'component_fields' => [

        'title_bar_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-6',
            'validation'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '13x11',
        ],

        'subheading' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:255',
        ],

        'heading' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:255',
        ],

    ],

    'repeated_content' => [

        'client_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-12',
            'validation'       => 'required|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '60x60',
        ],

        'client_name' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-6',
            'validation'   => 'required|string|max:100',
        ],

        'client_position' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-6',
            'validation'   => 'required|string|max:100',
        ],

        'comment_text' => [
            'translatable' => true,
            'type'         => 'textarea',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:1000',
        ],

        'rating' => [
            'translatable' => false,
            'type'         => 'number', // 1-5
            'class'        => 'col-md-12',
            'validation'   => 'required|integer|min:1|max:5',
            'info'         => 'Please enter a rating between 1 - 5.',
        ],

    ],

    'repeated_content_limit' => 10,

];
