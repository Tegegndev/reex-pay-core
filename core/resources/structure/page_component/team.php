<?php

return [

    'component_fields' => [

        'title_bar_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-12',
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

        'team_image' => [
            'translatable'     => false,
            'type'             => 'img',
            'class'            => 'col-md-12',
            'validation'       => 'required|image|mimes:jpg,jpeg,png,webp',
            'recommended_size' => '414x420',
        ],

        'name' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:100',
        ],

        'designation' => [
            'translatable' => true,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:100',
        ],

        'facebook_url' => [
            'translatable' => false,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'nullable|url',
        ],

        'twitter_url' => [
            'translatable' => false,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'nullable|url',
        ],

        'linkedin_url' => [
            'translatable' => false,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'nullable|url',
        ],

        'pinterest_url' => [
            'translatable' => false,
            'type'         => 'text',
            'class'        => 'col-md-12',
            'validation'   => 'nullable|url',
        ],

    ],

    'repeated_content_limit' => 30,

];
