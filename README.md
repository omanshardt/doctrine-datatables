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

This one supports
```bash
Doctrine\ORM\Tools\Pagination\Paginator
```
To search from datatables there are eight different search modes
<dl>
	<dt>*% (LIKE '…%') -> default</dt>
	<dd>This performs a <strong>LIKE '…%'</strong> search where the start of the search term must match  a value in the given column. This can be archived with only providing the search term (because it's default) or by prefixing the search term with "[*%]" <strong>([*%]searchTerm)</strong>.</dd>
	<dt>%% (LIKE '%…%')</dt>
	<dd>This performs a <strong>LIKE '%…%'</strong> search where any part the search term must match  a value in the given column. This can be archived  by prefixing the search term with "[%%]" <strong>([%%]searchTerm)</strong>.</dd>
	<dt>= (Equality)</dt>
	<dd>This performs a <strong>= …</strong> search. The search term must exactly match a value in the given column. This can be archived by prefixing the search term with "[=]" <strong>([=]searchTerm)</strong>.</dd>
	<dt>!= (No Equality)</dt>
	<dd>This performs a <strong>!= …</strong> search. The search term must not exactly match a value in the given column. This can be archived by prefixing the search term with "[!=]" <strong>([!=]searchTerm)</strong>.</dd>
	<dt>> (Greater Than)</dt>
	<dd>This performs a <strong>> …</strong> search. The search term must be smaller than a value in the given column. This can be archived by prefixing the search term with "[>]" <strong>([>]searchTerm)</strong>.</dd>
	<dt>< (Smaller Than)</dt>
	<dd>This performs a <strong>< …</strong> search. The search term must be greater than a value in the given column. This can be archived by prefixing the search term with "[<]" <strong>([<]searchTerm)</strong>.</dd>
	<dt>< (IN)</dt>
	<dd>This performs an <strong>IN(…)</strong> search. One of the provided comma-separated search terms must exactly match a value in the given column. This can be archived by prefixing the search terms with "[IN]" <strong>([IN]searchTerm,searchTerm,…)</strong>.</dd>
	<dt>< (OR)</dt>
	<dd>This performs multiple OR-connected <strong>LIKE('%…%')</strong> searches. One of the provided comma-separated search terms must match a fragment of a value in the given column. This can be archived by prefixing the search terms with "[OR]" <strong>([OR]searchTerm,searchTerm,…)</strong>.</dd>
	<dt>>< (Between)</dt>
	<dd>This performs a <strong>BETWEEN … AND …</strong> search. Both search terms must be separated with a comma. This operation can be archived by prefixing the comma-separated search terms with "[><]" <strong>([><]searchTerm,searchTerm)</strong>.</dd>
</dl>

Prefixes are case-insenstive (IN, in, OR, or).
Provided search terms were trimmed.
