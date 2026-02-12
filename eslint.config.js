'use strict';

// import globals from 'globals';
// import pluginJs from '@eslint/js';
/** @type {import('eslint').Linter.Config[]} */
//export default [{ languageOptions: { globals: globals.browser } }, pluginJs.configs.recommended];

import globals from 'globals';
// import pluginJs from '@eslint/js';
import stylistic from '@stylistic/eslint-plugin';
// import { FlatCompat } from '@eslint/eslintrc';
// import babelParser from '@babel/eslint-parser';
// import jquery from 'eslint-plugin-jquery';

export default [
    // pluginJs.configs.recommended,
    {
        plugins: {
            '@stylistic': stylistic,
        },
        files: ['**/*.{js,mjs,cjs,ts}'],
        ignores: [
            'doc/',
            'pm2-installer/',
            'pm2-installer-main/',
            'pm2-logrotate/',
            '!node_modules/', // unignore `node_modules/` directory
            'node_modules/*', // ignore its content
        ],
        rules: {
            // 'prefer-const': 'warn',
            // 'no-unused-vars': 'warn',
            'no-undef': 'warn',
            // 'no-empty': 'warn',
            // '@stylistic/indent': ['warn', 4],
            // '@stylistic/semi': 'warn',
        },
    },
    {
        languageOptions: {
            ecmaVersion: 2022,
            sourceType: 'module',
            globals: {
                ...globals.browser,
                ...globals.node,
                ...globals.jquery,
                ...globals.jest,
            },
        },
    },
];
