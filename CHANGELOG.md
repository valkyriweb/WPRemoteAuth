# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Added
1. Added PEST as test framework
2. Added CHANGELOG.md
3. Added "test" script to composer.json
4. added WordPress directory for WordPress specific implementations, with support for AJAX endpoint

### Changed
1. Updated README.md
2. Updated .gitignore
3. Updated AuthenticationToken Class
4. Updated Namespace to be ValkyriWeb
5. Updated readme to reflect new namespace and package name, and resolved namespace in AuthContract import
6. Adding version and minimum stability changes to composer.json
7. Removing Register ajax endpoints from WP class and rather instantiating in anonymous function, as WordPress does not recognise the code in the class
8. Adding new insert industry endpoints to RegisterAjaxEndpoints class

### Removed
1. Removing industry endpoints to their own plugin and class
2. Removed hanging variable left out