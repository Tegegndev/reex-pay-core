<?php

return [

    'component_fields' => [

        'main_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-6',
            'validation'       => 'required|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '267x452',
        ],
        'bg_shape_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-6',
            'validation'       => 'required|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '469x333',
        ],
        'content_shape_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-4',
            'validation'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '450x167',
        ],
        'about_tool_shape_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-4',
            'validation'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '174x174',
        ],
        'title_bar_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-4',
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

        'description' => [
            'translatable' => true,
            'type'         => 'textarea',
            'class'        => 'col-md-12',
            'validation'   => 'nullable|string|max:1000',
        ],
        'additional_description' => [
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

    'repeated_content' => [
        'about_icon_class' => [
            'translatable' => false,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'required',
        ],
        'about_title' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:10000',
        ],
        'about_text' => [
            'translatable' => true,
            'type'         => 'textarea',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:10000',
        ],
    ],

    'repeated_content_limit' => 2,

];
