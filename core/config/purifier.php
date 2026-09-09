<?php

/**
 * Ok, glad you are here
 * first we get a config instance, and set the settings
 * $config = HTMLPurifier_Config::createDefault();
 * $config->set('Core.Encoding', $this->config->get('purifier.encoding'));
 * $config->set('Cache.SerializerPath', $this->config->get('purifier.cachePath'));
 * if ( ! $this->config->get('purifier.finalize')) {
 *     $config->autoFinalize = false;
 * }
 * $config->loadArray($this->getConfig());
 *
 * You must NOT delete the default settings
 * anything in settings should be compacted with params that needed to instance HTMLPurifier_Config.
 *
 * @link http://htmlpurifier.org/live/configdoc/plain.html
 */

return [
    'encoding'         => 'UTF-8',
    'finalize'         => true,
    'ignoreNonStrings' => false,
    'cachePath'        => storage_path('app/purifier'),
    'cacheFileMode'    => 0755,

    'settings' => [
        'default' => [
            'HTML.Doctype'         => 'HTML 4.01 Transitional',
            'HTML.Allowed'         => 'div,b,strong,i,em,u,a[href|title|target],ul,ol,li,p[style],br,span[style],img[src|alt|width|height|style],h1,h2,h3,h4,h5,h6,blockquote,pre,code',
            'HTML.SafeIframe'      => true,
            'URI.SafeIframeRegexp' => '%^(https?:)?//(www.youtube.com/embed/|player.vimeo.com/video/)%',

            'CSS.AllowedProperties' => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align,width,height,margin,margin-left,margin-right,margin-top,margin-bottom,border,border-width,border-style,border-color',

            'AutoFormat.AutoParagraph'          => false,
            'AutoFormat.RemoveEmpty'            => true,
            'AutoFormat.RemoveEmpty.RemoveNbsp' => true,

            'Attr.AllowedFrameTargets' => ['_blank', '_self', '_parent', '_top'],
        ],

        'custom_definition' => [
            'id'    => 'html5-definitions',
            'rev'   => 1,
            'debug' => false,

            'elements' => [
                ['section', 'Block', 'Flow', 'Common'],
                ['nav', 'Block', 'Flow', 'Common'],
                ['article', 'Block', 'Flow', 'Common'],
                ['aside', 'Block', 'Flow', 'Common'],
                ['header', 'Block', 'Flow', 'Common'],
                ['footer', 'Block', 'Flow', 'Common'],
                ['figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common'],
                ['figcaption', 'Inline', 'Flow', 'Common'],
                ['video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                    'src' => 'URI', 'type' => 'Text', 'width' => 'Length', 'height' => 'Length', 'poster' => 'URI', 'controls' => 'Bool',
                ]],
                ['source', 'Block', 'Flow', 'Common', ['src' => 'URI', 'type' => 'Text']],
                ['mark', 'Inline', 'Inline', 'Common'],
                ['ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
                ['del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
                ['u', 'Inline', 'Inline', 'Common'],
            ],

            'attributes' => [
                ['iframe', 'allowfullscreen', 'Bool'],
                ['table', 'height', 'Text'],
                ['td', 'border', 'Text'],
                ['th', 'border', 'Text'],
                ['img', 'style', 'Text'],
                ['img', 'width', 'Length'],
                ['img', 'height', 'Length'],
            ],
        ],

        'custom_attributes' => [
            ['a', 'target', 'Enum#_blank,_self,_parent,_top'],
        ],

        'custom_elements' => [
            ['u', 'Inline', 'Inline', 'Common'],
        ],
    ],
];
