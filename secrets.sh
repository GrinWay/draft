# Run: symfony console debug:container --env-vars
# to show all the missing ENV vars, not SECRETS, add them to the secrets if they missed in SECRETS as well

echo 'APP_DB_PASS'
php "./bin/console" secrets:set APP_DB_PASS

echo 'APP_ADMIN_EMAIL'
php "./bin/console" secrets:set APP_ADMIN_EMAIL

echo 'APP_ADMIN_MAILER_LOGIN'
php "./bin/console" secrets:set APP_ADMIN_MAILER_LOGIN

echo 'APP_ADMIN_MAILER_PASS'
php "./bin/console" secrets:set APP_ADMIN_MAILER_PASS

echo 'APP_MAILER_MAILGUN_TRANSPORT_API_KEY'
php "./bin/console" secrets:set APP_MAILER_MAILGUN_TRANSPORT_API_KEY

echo 'APP_MAILER_MAILGUN_TRANSPORT_DOMAIN'
php "./bin/console" secrets:set APP_MAILER_MAILGUN_TRANSPORT_DOMAIN

echo 'APP_TEST_EMAIL'
php "./bin/console" secrets:set APP_TEST_EMAIL
