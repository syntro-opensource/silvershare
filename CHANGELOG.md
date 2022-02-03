# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

<a name="unreleased"></a>
## [Unreleased]


<a name="1.0.4"></a>
## [1.0.4] - 2022-02-03
### 🐞 Fixed
- settting a default image does not overwrite fallback image ([#7](https://github.com/syntro-opensource/silvershare/issues/7))

### 🗑 Removed
- Tests (& support) for PHP 7.2 & 7.3


<a name="1.0.3"></a>
## [1.0.3] - 2021-09-24
### 🐞 Fixed
- info fields have background
- no longer uses alerts to communicate image state


<a name="1.0.2"></a>
## [1.0.2] - 2021-09-09
### 🐞 Fixed
- fallback image only returns if image is in DB ([#5](https://github.com/syntro-opensource/silvershare/issues/5))

### 🔧 Changed
- alert color changed from danger to warning when no OGImage found ([#6](https://github.com/syntro-opensource/silvershare/issues/6))


<a name="1.0.1"></a>
## [1.0.1] - 2021-08-21
### 🐞 Fixed
- requirements are cleared and restored when `forTemplate` is used

### Pull Requests
- Merge pull request [#3](https://github.com/syntro-opensource/silvershare/issues/3) from syntro-opensource/fix/requirements


<a name="1.0.0"></a>
## 1.0.0 - 2021-08-20
### 🍰 Added
- packagist release


[Unreleased]: https://github.com/syntro-opensource/silvershare/compare/1.0.4...HEAD
[1.0.4]: https://github.com/syntro-opensource/silvershare/compare/1.0.3...1.0.4
[1.0.3]: https://github.com/syntro-opensource/silvershare/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/syntro-opensource/silvershare/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/syntro-opensource/silvershare/compare/1.0.0...1.0.1
