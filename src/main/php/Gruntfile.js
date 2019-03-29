/**
 * Created by steve on 28/07/2014.
 */
module.exports = function (grunt) {


    var objPhpunit      = {};

    grunt.config.init({
        composer: {
            options: {
                usePhp: true,
                cwd: ".",
                composerLocation: "/usr/local/bin/composer"
            },
            autoload: {
                options: {
                    usePhp: true,
                    cwd: ".",
                    composerLocation: "/usr/local/bin/composer"
                }
            }
        },
        phplint : {
            options : {
                swapPath : '/tmp'
            },
            all : ['**/*.php', '!libs/**', '!node_modules/**', '!vendor/**']
        },
        phpunit : {
            classes : {
                dir : '../../test/php/'
            },
            options: {
                bin: 'vendor/phpunit/phpunit/phpunit',
                bootstrap: '../../test/gruntbootstrap.php',
                colors: true,
                verbose: true,
                stopOnError : true,
                stopOnFailure : true,
                stderr : true
            }
        },
        'string-replace': {
            kit: {
                files: {
                    'vendor/zendframework/zendframework1/library/Zend/' : [
                        'vendor/zendframework/zendframework1/library/Zend/**/*.php',
                        '!vendor/zendframework/zendframework1/library/Zend/Application.php',
                    ]
                },
                options: {
                    replacements: [{
                        pattern: /require_once/ig,
                        replacement: '//require_once'
                    }]
                }
            }
        }
    });
    //this setups a different target for each of the php dirs
    grunt.config.merge({
        phpunit : objPhpunit
    });

    grunt.registerTask(
        'makeClassmaps',
        [
            "composer:autoload:dump-autoload"
        ]
    );

    var arrBuildTasks = ['string-replace', 'phplint', 'makeClassmaps'];

    grunt.registerTask('test', ['string-replace', 'phpunit']);
    grunt.registerTask('build', arrBuildTasks);
    grunt.registerTask('default', ['build', 'test']);

    require('matchdep').filterDev(['grunt-*', '!grunt-template-jasmine-*', '!grunt-aws']).forEach(grunt.loadNpmTasks);
};
