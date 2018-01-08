# omanshardt/doctrine-datatables
Helper library to implement [Doctrine](http://www.doctrine-project.org/) powered server-side processing for [jquery-datatables](https://github.com/DataTables/DataTables) with joins, search, filtering and ordering.

[![Latest Version](https://img.shields.io/github/release/omanshardt/doctrine-datatables.svg?style=flat-square)](https://github.com/omanshardt/doctrine-datatables/releases) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/4ac1aa6a-a495-49e0-b5d6-d7b82be2a5f6/mini.png)](https://insight.sensiolabs.com/projects/4ac1aa6a-a495-49e0-b5d6-d7b82be2a5f6) [![Total Downloads](https://img.shields.io/packagist/dt/omanshardt/doctrine-datatables.svg?style=flat-square)](https://packagist.org/packages/omanshardt/doctrine-datatables) [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

Install
-------
```bash
composer require omanshardt/doctrine-datatables
```

Usage with [doctrine/dbal](https://github.com/doctrine/dbal):
Usage with [doctrine/orm](https://github.com/doctrine/doctrine2):

This is a clone from https://github.com/vaibhavpandeyvpz/doctrine-datatables with some intenal modifications. So please refer to the original tool for usage.

See [LICENSE.md](https://github.com/omanshardt/doctrine-datatables/blob/master/LICENSE.md) file.

## This version supports the following sql operations:
- !=
- LIKE (with wildcards on both sides)
- LIKE (with wildcard on end, this is default behavior)
- &lt;
- &gt;
- IN
- BETWEEN
- =
