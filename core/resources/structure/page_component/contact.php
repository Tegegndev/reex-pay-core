<?php

return [

    'component_fields' => [

        'contact_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-6',
            'validation'       => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '458x357',
        ],
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

];
