# Kellerkinder Shopware 6 Development Helper

This plugin provides a safety net for local development environments, marking them clearly as such. It also makes sure
your mailer configuration is set to send to a local mail server (like https://github.com/mailhog/MailHog).

### Notable Features:
- Adds a notice about being in a development environment in Storefront and Administration.
- Checks for safe mailer configuration (localhost and using the environment variable for configuration)

### Installation
Include the plugin in your local Shopware system:
```
composer req k10r/development
```

Install the plugin. Make sure to only do this on your local installation and not on a server installation or any shop
that should be able to send mails.
```
bin/console plugin:refresh ; bin/console plugin:install --activate K10rDevelopment
```

### Usage
As long as the plugin is installed and active, it will check for the correct mailer configuration on any request and
command execution. If the configuration is not valid, it will throw an exception and block any action in your shop. You
may have to deactivate the plugin via the database, change your configuration and reactivate it.

⚠️ Make sure to reactivate the plugin after fixing any configuration issues. Otherwise, its validation is not active,
increasing the risk of unwanted email deliveries in local systems.

## License
MIT
