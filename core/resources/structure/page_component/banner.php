<?php

return [

    'component_fields' => [

        'background_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-6',
            'validation'       => 'required|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '1920x1239',
        ],

        'hero_main_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-6',
            'validation'       => 'required|image|mimes:jpg,jpeg,png',
            'recommended_size' => '933x586',
        ],

        'shape_image_1' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-4',
            'validation'       => 'nullable|image',
            'recommended_size' => '50x49',
        ],

        'shape_image_2' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-4',
            'validation'       => 'nullable|image',
            'recommended_size' => '68x75',
        ],

        'shape_image_3' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-4',
            'validation'       => 'nullable|image',
            'recommended_size' => '66x66',
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

        'description' => [
            'translatable' => true,
            'type'         => 'textarea',
            'class'        => 'col-md-12',
            'validation'   => 'nullable|string|max:1000',
        ],

        'button_text' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-6',
            'validation'   => 'required|string|max:100',
        ],

        'button_url' => [
            'translatable' => false,
            'type'         => 'text',
            'class'        => 'col-md-6',
            'validation'   => 'required',
        ],
    ],

];
