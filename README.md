# pharizer

Create *phar* files / executables from a PHP project file structure.

## Features
- Easy to integrate into build chains
- Supports multiple phar files per project
- Extended file filter capabilities

## Install
The easiest way to install *pharizer* is by using [composer](https://getcomposer.org/): 

```
$> composer require fostam/pharizer
```

After installation, it can be called from the following location:
```
$> vendor/bin/pharizer
```

## Configuration file
The configuration file specifies the PHARs that should be built and uses the
[YAML](https://en.wikipedia.org/wiki/YAML) format.
Usually, it resides at the top level of your project file structure.
The default name is `pharizer.yaml`.

Example `pharizer.yaml`:
```
target-directory: dist
targets:
  myphar:
    source-directory: .
    stub-file: src/myphar.php
    filters:
      - include: ^(src|vendor)/
      - exclude: .*
 
  another.phar:
    source-directory: .
    stub-file: src/another.php
    filters:
      - include: ^(src|vendor)/
      - exclude: .*
```

Main Keys:
- `target-directory` _(optional)_: If all targets should be placed under
a common directory, e.g. `dist`, it can be specified here. Defaults to the current
directory (`.`).
- `targets`: A list of targets. A target name can have (but does not need) the
`.phar` extension.
It can contain a path, too. Examples: `dist/myphar.phar`, `myphar`).

Target Keys:
- `source-directory` _(optional)_: The base directory used for collecting files.
Defaults to the current directory (`.`).
- `stub-file`: The "entry point" file that is invoked when the `phar` is executed
or included. The location must be relative to the `source-directory`.
- `filters`: Filter patterns can be used to control which files are included
in the PHAR and which not. See "Filters" for more details. 
- `shebang` _(optional)_: The shebang line used for the stub file. Defaults to `#!/usr/bin/env php`.
- `exclude-pharizer` _(optional)_: If set to `true`, automatically excludes all
pharizer files (if pharizer has been installed with composer for the current project).
Defaults to `true`.

## Commands
### Build
With the build command, all targets from the `pharizer.yaml` are built:
```
$> pharizer build
```

To build a single target, append the target name as defined in the `pharizer.yaml`:

```
$> pharizer build myphar.phar
```

### List Files
Similarly to the _build_ command, the `list-files` command either processes
all or a specific target from the configuration file. The command lists all
files that match the filters for a target and can be used to test and try out
the filters.

## Filters
### Definition
Filters are defined as a list of include and exclude regular expression (PCRE) patterns.
By default, the regular expressions are not anchored, i.e. if you want them to be anchored
to the beginning or end of a file path, you have to use `^` or `$`.

Delimiter escaping is handled internally, so it is *not* necessary to escape the slash character (`/`).

Patterns are matched against the full file path of each file under `source-directory`. The source
path itself (including the slash in the beginning) is truncated.

Example:
The file `/my/app/src/file.php` in the source directory `/my/app` will become `src/file.php` when 
applying filter patterns.

### Processing
For each file in the `source-directory`, the patterns are processed in the order in which
they have been given in the target filter list. The first pattern that matches determines
whether the file is included or excluded. If no pattern matches, the file is included by default.
If you want to change the default behaviour to exclude, simply add `exclude: ".*"` as last filter.

### Caveats
#### YAML Escaping
YAML does not allow characters like `*`, so when giving a pattern containing an asterisk, put it in single
quotes. Avoid double quotes, as they don't work well with backslash escapes.
```
- exclude: '.*\.php'
```
#### Anchors
Try to anchor patterns either with `^` and `$` or `/` to avoid unintended matching.

Example:
```
lib/file.php
lib/file2.php
other/glibc.c
```

The pattern `include: src` would not only match the files in the `src` directory, but also the
"src" part of `glibc.c`. This can be avoided by anchoring the pattern, e.g. `include: ^src/`.


## Options
### Configuration File
By default, `pharizer.yaml` in the current directory is used as configuration file.
With the `-c` option, an alternative configuration file can be specified:
```
$> pharizer build -c ../myphar.yaml
```

### Verbosity
For each successfully built target, _pharizer_ will print the name and size of the target
PHAR file. To turn this off, use the `-q` (quiet) option.


## Return Codes
If no errors occurred, _pharizer_ exits with a return code of 0, otherwise with a return code > 0.
