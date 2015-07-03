# Krak Validation

The Krak Validation library is a functional and simple approach and interface to validation. It was made out of the need a validation library with a simple interface. Other leading validators like Respect, Zend, or Symfony have a lot of complexity in their API and adding an actual validator will require adding several classes in some instances.

Krak Validation changes all of this by taking a functional approach to validation where every validator is just a function or instance of `Krak\Validation\Validator`. This design lends itself easily for extending, custom validators, and decorators.

## Validators

A validator can be a `Krak\Validation\Validator`, or any callable that accepts a value and returns null on success and a Violation instance on error

## Roadmap

### **v0.2.0**

For the next version, I'm looking towards making a violation message formatter to format the violations into a nice array of error messages.
