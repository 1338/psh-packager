# psh-packager
Package composer cli applications into a PSH

## Currently working
- Symfony

## How to use
```bash
git clone <project> project
./build <stub type> <project name> <location path>
```

For example:
```bash
git clone git://link.to/repo.git project
./build symfony toolchain /path/to/ToolChain
```

### Symfony target requirements
- symfony/filesystem
- symfony/finder