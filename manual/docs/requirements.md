| Requirement       | Minimum | Recommended           |
| -----------       |:-------:|:-----------:          |
| PHP Version       | 5.6.0   | [Latest stable release](https://www.php.net/supported-versions.php) |
| PHP Extensions    | [iconv](https://www.php.net/manual/en/book.iconv.php) | [iconv](https://www.php.net/manual/en/book.iconv.php),<br> [mbstring](https://www.php.net/manual/en/book.mbstring.php)
| PHP INI Settings  | ---     | [allow_url_fopen = 1](https://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen) **

** This makes it possible to load files from URL using [`file_get_html`](/api/file_get_html/)