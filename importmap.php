<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'bootstrap' => [
        'version' => '5.3.8',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.8',
        'type' => 'css',
    ],
    '@stimulus-components/sortable' => [
        'version' => '5.0.2',
    ],
    'sortablejs' => [
        'version' => '1.15.6',
    ],
    '@rails/request.js' => [
        'version' => '0.0.11',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'filepond' => [
        'version' => '4.32.9',
    ],
    'filepond/dist/filepond.min.css' => [
        'version' => '4.32.9',
        'type' => 'css',
    ],
    'filepond-plugin-file-encode' => [
        'version' => '2.1.14',
    ],
    'filepond-plugin-file-metadata' => [
        'version' => '1.0.8',
    ],
    'filepond-plugin-file-poster' => [
        'version' => '2.5.2',
    ],
    'filepond-plugin-file-poster/dist/filepond-plugin-file-poster.css' => [
        'version' => '2.5.2',
        'type' => 'css',
    ],
    'filepond-plugin-file-validate-size' => [
        'version' => '2.2.8',
    ],
    'filepond-plugin-file-validate-type' => [
        'version' => '1.2.9',
    ],
    'filepond-plugin-image-crop' => [
        'version' => '2.0.6',
    ],
    'filepond-plugin-image-edit' => [
        'version' => '1.6.3',
    ],
    'filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css' => [
        'version' => '1.6.3',
        'type' => 'css',
    ],
    'filepond-plugin-image-exif-orientation' => [
        'version' => '1.0.11',
    ],
    'filepond-plugin-image-preview' => [
        'version' => '4.6.12',
    ],
    'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css' => [
        'version' => '4.6.12',
        'type' => 'css',
    ],
    'filepond-plugin-image-resize' => [
        'version' => '2.0.10',
    ],
    'filepond-plugin-image-transform' => [
        'version' => '3.8.7',
    ],
    'filepond-plugin-image-validate-size' => [
        'version' => '1.2.7',
    ],
    'filepond/locale/ar-ar.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/cs-cz.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/da-dk.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/de-de.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/el-el.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/en-en.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/es-es.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/fa_ir.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/fi-fi.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/fr-fr.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/he-he.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/hr-hr.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/hu-hu.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/id-id.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/it-it.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/ja-ja.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/lt-lt.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/nl-nl.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/no_nb.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/pl-pl.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/pt-br.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/ro-ro.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/ru-ru.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/sk-sk.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/sv_se.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/tr-tr.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/uk-ua.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/vi-vi.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/zh-cn.js' => [
        'version' => '4.32.9',
    ],
    'filepond/locale/zh-tw.js' => [
        'version' => '4.32.9',
    ],
    'tom-select' => [
        'version' => '2.4.3',
    ],
    '@orchidjs/sifter' => [
        'version' => '1.1.0',
    ],
    '@orchidjs/unicode-variants' => [
        'version' => '1.1.2',
    ],
    'tom-select/dist/css/tom-select.default.min.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    'tom-select/dist/css/tom-select.default.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    'tom-select/dist/css/tom-select.bootstrap4.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
    'tom-select/dist/css/tom-select.bootstrap5.css' => [
        'version' => '2.4.3',
        'type' => 'css',
    ],
];
