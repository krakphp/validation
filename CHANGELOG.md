# Change Log

## Unreleased

### Fixed

- Fixed bug with `pipeAll` when any errors occurred

## 0.3.10 - 2018-03-17

### Added

- New `assert` method on the WrappedValidator.

## 0.3.9 - 2018-03-06

- Fixing bug with invalid keys message
- Fixing typo in `doctrine_entities`

## 0.3.8 - 2018-02-01

### Added

- Validation for extra keys in the `collection` validator
- API documentation
- Validator Documentation
- Aliases with the Kernel `aliases` method.

## 0.3.7 - 2017-07-09

### Added

- Violation Collection Array Access #7
- `number` and `any` validator #9

## 0.3.6 - 2017-05-06

### Added

- ViolationException #3
- Violation Documentation
- Length Validators #2

## 0.3.5 - 2017-05-03

### Fixed

- HttpRequestValidationContext bug

## 0.3.4 - 2017-05-02

### Fixed

- Doctrine Unique Entity class bug

## 0.3.3 - 2017-04-13

### Fixed

- Kernel bug where context was not passed in

## 0.3.2 - 2017-04-13

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
