# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

- **Bumped required min. PHP version to 7.4**
- Upgraded development dependencies: phpcs from 2.3 to 3.5.  php-parallel-lint from the `0.*` version, to the newer composer pkg (actually had to update the entire pkg name in composer.json as the prev. had been abandoned).
- Added an `.editorconfig`, updated `phpunit.xml`, and renamed it to `.dist` in the repository.  Also added `phpstan.neon`, 
- Automatic code formatting corrections ala phpcs and php-cs-fixer.
- Parameter types and return types added.  Haven't added class property hints anywhere yet, I'm pretty sure.  Many of these were automated via psalm/psalter.  This also added all automated `@psalm-*` annotations.
- Cleaned up a lot of code comments.  Tried to convert all comments to proper docblocks where it made sense, although this isn't 100%.
- Split all classes out to each have their own file and be PSR-4 compatible with the exception of the serialization function.  Incredibly, the v2 branch already did this.  I didn't see it, so this was independent.  
- Merged `master` and `v2` branches together.  This was not as big as an undertaking as it would appear but was not small, either.  
  - This brought in proper unit tests via phpunit. 
  - `v2` had more standards-compliant code, but a very non-PSR4 structure.  Looked like perhaps a Java-like file structure.  As a part of this, I brought it in line with PSR-4 standards (or very close).  
- Added new `ByteParser`.  Again, incredibly there was a nearly identical Parser I had never seen in the `v2` branch called `LookaheadParser`.  It's not a stretch, as the Utf8Parser does the same thing but just won't work on binary.  So, there are two implementations of this, one is called ByteParser (my implementation) and another ByteParserAlt (the original implementation written by "Kipras").  Need to do perf. tests.