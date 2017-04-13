# Change Log

## Unreleased

### Fixed

- `digits` validator

### Added

- `alpha` and `alpha_num` validators
- `wrapRegex` to simplify wrapping of regex validators
- Initial tests with peridot

## 0.3.1 - 2017-04-13

### Added

- New `required` and `date` validator
- `arrayArgs` to easily parse args that should be an array or array of arguments.

### Fixed

- A few bugs in ViolationCollection and FluentValidationBuilder
- messaging for boolean validator

## 0.3.0 - 2017-04-08

### Added

- Added new Validation Kernel
- ValidationPackages
- Formatted Messages
- Violation Collections
- Fluent Violation Builder
- Validation Context

### Changed

- Moved validators into Validators module.
- Violation structure and format

## 0.2.2 - 2016-06-11

### Added

- `array_collection` to validate an array and collection

## 0.2.1 - 2016-05-21

### Added

- Ability to find violations from a set of violations.

## 0.2.0 - 2015-07-30

### Changed

- Refactored doctrine validators to use ObjectRepository

### Added

- More violation codes
- inc file to do all of the including
- length validators
- regex validators
- greedy pipe

## 0.1.0 - 2015-07-06

- Initial Release

### Added

- Initial Validation system
- Util, Doctrine, Symfony Validators
