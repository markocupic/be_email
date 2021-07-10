:: Run easy-coding-standard (ecs) via this batch file inside your IDE e.g. PhpStorm (Windows only)
:: Install inside PhpStorm the  "Batch Script Support" plugin
cd..
cd..
cd..
cd..
cd..
cd..
:: src
vendor\bin\ecs check vendor/markocupic/be_email/src --config vendor/markocupic/be_email/.ecs/config/default.php
:: tests
vendor\bin\ecs check vendor/markocupic/be_email/tests --config vendor/markocupic/be_email/.ecs/config/default.php
:: legacy
vendor\bin\ecs check vendor/markocupic/be_email/src/Resources/contao --config vendor/markocupic/be_email/.ecs/config/legacy.php
:: templates
vendor\bin\ecs check vendor/markocupic/be_email/src/Resources/contao/templates --config vendor/markocupic/be_email/.ecs/config/template.php
::
cd vendor/markocupic/be_email/.ecs./batch/check
