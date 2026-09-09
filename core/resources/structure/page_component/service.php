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

        'service_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-12',
            'validation'       => 'required|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '321x216',
        ],

        'service_title' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:255',
        ],

        'service_text' => [
            'translatable' => true,
            'type'         => 'textarea',
            'class'        => 'col-md-12',
            'validation'   => 'nullable|string|max:1000',
        ],

    ],

    'repeated_content_limit' => 20,

];
