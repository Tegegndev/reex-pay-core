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
        'line_shape_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-6',
            'validation'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '940x129',
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

        'step_icon_class' => [
            'translatable' => false,
            'type'         => 'text', // Icon class
            'class'        => 'col-md-6',
            'validation'   => 'required|string|max:255',
        ],

        'step_title' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-6',
            'validation'   => 'required|string|max:255',
        ],

        'step_description' => [
            'translatable' => true,
            'type'         => 'textarea',
            'class'        => 'col-md-12',
            'validation'   => 'nullable|string|max:1000',
        ],

    ],

    'repeated_content_limit' => 4,

];
