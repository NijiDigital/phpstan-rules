# nijidigital/phpstan-rules

Custom PhpStan rules made with ❤️ by [Niji](https://www.niji.fr).

## Installation

To use this extension, require it in [Composer](https://getcomposer.org/):

```shell
composer require --dev nijidigital/phpstan-rules
```

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
  <summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include phpstan.neon in your project's PHPStan config:

```
includes:
    - vendor/nijidigital/phpstan-rules/phpstan.neon
```
</details>

## Troubleshooting

### PhpStorm doesn't provide any autocomplete on PhpStan classes

<details>
    Taken from [this blog](https://blog.bitexpert.de/blog/phpstorm_phpstan_wsl2_issue) :

    1. Copy the file `vendor/phpstan/phpstan/phpstan.phar` to a local folder of your choice, ie : `C:\php_include_path`.
    2. In PhpStorm, go to File | Settings | PHP and add your newly created folder to the include path.

</details>
