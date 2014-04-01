module.exports = function (grunt) {
  'use strict';

  var optionIncrement = grunt.option('increment');

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    paths: {
      bin: 'vendor/bin',
      lib: 'lib/**/*.php'
    },
    bump: {
      options: {
        commitFiles: ['-a'],
        files: ['package.json'],
        updateConfigs: ['pkg']
      }
    },
    'gh-pages': {
      options: {
        base: 'doc',
        message: 'Latest auto-generated docs.'
      },
      src: '**/*'
    },
    jshint: {
      gruntfile: {
        files: {
          src: 'Gruntfile.js'
        },
        options: {
          jshintrc: true
        }
      }
    },
    phpcpd: {
      lib: {
        dir: '<%= paths.lib %>'
      },
      options: {
        bin: '<%= paths.bin %>/phpcpd'
      }
    },
    phpcs: {
      lib: {
        dir: ['<%= paths.lib %>']
      },
      options: {
        bin: '<%= paths.bin %>/phpcs',
        standard: 'PSR1'
      }
    },
    phplint: {
      src: '<%= paths.lib %>'
    },
    phpmd: {
      lib: {
        dir: 'lib'
      },
      options: {
        bin: '<%= paths.bin %>/phpmd',
        reportFormat: 'text',
        rulesets: ['codesize', 'controversial', 'design', 'naming', 'unusedcode'].join(',')
      }
    },
    prompt: {
      deploy: {
        options: {
          questions: [
            {
              config: 'deploy.increment',
              type: 'list',
              message: 'Which part of the version number do you want to increment? (Current: v<%= pkg.version %>)',
              choices: [
                {
                  value: 'build',
                  name: 'build (x.y.z-N) — append build number for pre-release'
                },
                {
                  value: 'git',
                  name: 'git (x.y.z-NNNNN) — append git revision for pre-release'
                },
                {
                  value: 'patch',
                  name: 'patch (x.y.Z) — backwards-compatible bug fixes'
                },
                {
                  value: 'minor',
                  name: 'minor (x.Y.z) — added functionality in a backwards-compatible manner'
                },
                {
                  value: 'major',
                  name: 'major (X.y.z) — incompatible API changes'
                }
              ],
              when: function () {
                return !optionIncrement;
              }
            }
          ]
        }
      }
    },
    shell: {
      options: {
        stdout: true
      },
      phpdcd: {
        command: '<%= paths.bin %>/phpdcd <%= paths.lib %>'
      },
      phpdoc: {
        command: '<%= paths.bin %>/phpdoc.php'
      },
      phploc: {
        command: '<%= paths.bin %>/phploc <%= paths.lib %>'
      },
      'security-checker': {
        command: '<%= paths.bin %>/security-checker security:check'
      }
    },
    watch: {
      jshint: {
        files: '<%= jshint.gruntfile.files.src %>',
        tasks: 'jshint:gruntfile'
      },
      phpcpd: {
        files: '<%= phpcpd.lib.dir %>',
        tasks: 'phpcpd'
      },
      phpcs: {
        files: '<%= phpcs.lib.dir %>',
        tasks: 'phpcs'
      },
      phplint: {
        files: '<%= phplint.src %>',
        tasks: 'phplint'
      },
      'security-checker': {
        files: 'composer.json',
        tasks: 'security-checker'
      }
    }
  });

  grunt.registerTask('bump-increment', 'Increment the version number.', function (inc) {
    var increment = inc || optionIncrement || grunt.config('deploy.increment');

    grunt.task.run('bump:' + increment + ':bump-only');
  });

  grunt.registerTask('phpdcd', ['shell:phpdcd']);
  grunt.registerTask('phpdoc', ['shell:phpdoc']);
  grunt.registerTask('phploc', ['shell:phploc']);
  grunt.registerTask('security-checker', ['shell:security-checker']);

  grunt.registerTask('default', ['build', 'watch']);
  grunt.registerTask('quality', ['phplint', 'phpcs', 'phpcpd', 'phploc', 'phpdcd', 'phpmd', 'security-checker']);
  grunt.registerTask('build', ['jshint', 'quality', 'phpdoc']);
  grunt.registerTask('deploy', ['prompt:deploy', 'bump-increment', 'build', 'bump::commit-only', 'gh-pages']);

  grunt.loadNpmTasks('grunt-bump');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-gh-pages');
  grunt.loadNpmTasks('grunt-phpcpd');
  grunt.loadNpmTasks('grunt-phpcs');
  grunt.loadNpmTasks('grunt-phplint');
  grunt.loadNpmTasks('grunt-phpmd');
  grunt.loadNpmTasks('grunt-prompt');
  grunt.loadNpmTasks('grunt-shell');
};
