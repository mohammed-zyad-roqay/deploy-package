# Laravel Deploy Package

A package for deploying Laravel projects with SSH commands.

## Installation

You can install the package via Composer:

```bash
composer require deploy/deploy-package
```

## Configuration

After installing the package, you can publish the configuration file using the following command:

```bash
php artisan vendor:publish --tag=deploy-config
```

This will create a `deploy.php` configuration file in your `config` directory.

## Usage

You can use the `deploy` command to deploy your application to a specified environment. The command signature is as follows:

```bash
php artisan deploy {environment} {--ssh}
```

- `environment`: The target environment for deployment.
- `--ssh`: Optional flag to use SSH for deployment.

## Logging

Deployment logs will be written to the path specified in the `config/deploy.php` file. Make sure the directory is writable.

## Testing

To run the tests for this package, use PHPUnit:

```bash
vendor/bin/phpunit
```

## License

This package is open-source and available under the [MIT License](LICENSE).