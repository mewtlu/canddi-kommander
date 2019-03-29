/**
 * Created by steve on 25/07/2014.
 */
module.exports = function (grunt) {

    grunt.initConfig({
        concurrent: {
            target1: [
                'run_grunt:php'
            ]
        },
        run_grunt: {
            options: {
                minimumFiles: 1
            },
            php : {
                options: {
                    log: true,
                    task :  ['build', 'test'],
                    gruntOptions : {}
                },
                src: [('src/main/php/Gruntfile.js')]
            }
        }
    });

    require('matchdep').filterDev(['grunt-*', '!grunt-template-jasmine-*']).forEach(grunt.loadNpmTasks);

    grunt.registerTask('php', ['run_grunt:php']);

    var arrBuildTasks = ['concurrent'];
    grunt.registerTask('build', arrBuildTasks);
    grunt.registerTask('default', ['build']);
};
