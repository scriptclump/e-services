/*
 * Sleek Grunt task runner
 * 
 * @package Sleek.js
 * @version 2.0
 * 
 * The MIT License (MIT)

 * Copyright Cubet Techno Labs, Cochin (c) 2013 <info@cubettech.com>

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @author Robin <robin@cubettech.com>
 * @Date 13-06-2014
 */

module.exports = function(grunt) {
//get sleek configs
    global.sleekConfig = {};
    require("./application/config/config.js");

    require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        serverFile: 'app.js',
        shell: {
            nodemon: {
                command: 'nodemon app.js',
                options: {
                    stdout: true,
                    stderr: true
                }
            }
        },
        concurrent: {
            dev: {
                tasks: ['shell:nodemon', 'open:dev', 'watch'],
                options: {
                    logConcurrentOutput: true
                }
            }
        },
        htmlhint: {
            options: {
                'tag-pair': true,
                'tagname-lowercase': true,
                'attr-lowercase': true,
                'attr-value-double-quotes': true,
                'id-unique': true,
                'head-script-disabled': true,
                'style-disabled': true
            },
            all: ['application/**/*.html']
        },
        jshint: {
            options: {
                curly: true,
                eqeqeq: true,
                eqnull: true,
                browser: true,
                globals: {
                    jQuery: true
                }
            },
            all: [    'Gruntfile.js',
                    'lib/**/*.js',
                    'application/**/*.js',
                    'system/**/*.js',
                    'modules/**/*.js',
                    '!system/lib/handhelpers.js',
                    '!system/lib/functions.js',
                    '!system/core/db.js',
                    '!system/core/sleek.js'
            ]
        },
        open: {
            dev: {
                path: 'http://' + sleekConfig.appHost + ':' + sleekConfig.appPort,
               // app: 'firefox',
		options: {
			delay:100
		}

            }
        },
        watch: {
            html: {
                files:    ['./**/*.html'],
                tasks:    ['htmlhint']
            },
            js: {
                files:    ['./**/*.js'],
                tasks:    ['jshint']
            }
        }
    });

    // Define tasks
    grunt.registerTask('default', ['concurrent:dev']);

};
