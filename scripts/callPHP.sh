#!/bin/bash

export APPLICATION_ENV="development"
export APPLICATION_PATH="/Volumes/SourceCode/canddi-kommander/src/main/php"
export VENDOR_PATH="/Volumes/SourceCode/canddi-kommander/src/main/php/vendor/"
export APPLICATION_CONFIG_PATH="/Volumes/SourceCode/canddi-kommander/src/main/php/config"

PHPCOMMAND=`which php`
${PHPCOMMAND} ${APPLICATION_PATH}/cli/cli.php "$@"
